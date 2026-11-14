const DB='pipocas-offline-v1', ACTIVE='pipocas-offline-activo';
const enc=new TextEncoder(),dec=new TextDecoder();
function open(){return new Promise((ok,no)=>{const r=indexedDB.open(DB,1);r.onupgradeneeded=()=>r.result.createObjectStore('vaults');r.onsuccess=()=>ok(r.result);r.onerror=()=>no(r.error)})}
async function use(mode,fn){const db=await open();try{return await new Promise((ok,no)=>{const t=db.transaction('vaults',mode),r=fn(t.objectStore('vaults'));let v;r.onsuccess=()=>{v=r.result};t.oncomplete=()=>ok(v);t.onerror=()=>no(t.error);t.onabort=()=>no(t.error)})}finally{db.close()}}
async function derive(password,salt){const base=await crypto.subtle.importKey('raw',enc.encode(password),'PBKDF2',false,['deriveKey']);return crypto.subtle.deriveKey({name:'PBKDF2',salt,iterations:250000,hash:'SHA-256'},base,{name:'AES-GCM',length:256},false,['encrypt','decrypt'])}
export function active(){try{return JSON.parse(localStorage.getItem(ACTIVE)||'null')}catch{return null}}
export async function clear(){await use('readwrite',s=>s.clear());localStorage.removeItem(ACTIVE)}
const releases = new Set();
window.addEventListener('pagehide', () => { for (const close of releases) close(); });
async function lease() {
 if(!navigator.locks) throw new Error('Este navegador no admite el bloqueo seguro de datos offline. Usa un navegador actualizado.');
 let release;const ok=await new Promise((resolve,reject)=>navigator.locks.request(DB,{ifAvailable:true},async lock=>{if(!lock){resolve(false);return}resolve(true);await new Promise(r=>release=r)}).catch(reject));
 if(!ok)throw new Error('El acceso sin conexión está abierto en otra pestaña. Bloquéala o ciérrala para continuar.');
 const close=()=>{release?.();releases.delete(close)};releases.add(close);return close;
}
export async function unlock(password,context=null){
 const close=await lease();try {
 if(!crypto.subtle)throw new Error('El acceso sin conexión necesita HTTPS o localhost.');
 if(password.length<12)throw new Error('Usa una clave de al menos 12 caracteres.');
 const current=active();if(context&&current&&current.id!==context.id)await clear();
 const id=context?.id||active()?.id;if(!id)throw new Error('Primero prepara este dispositivo desde una salida con conexión.');
 const record=await use('readonly',s=>s.get(id)),salt=record?.salt||crypto.getRandomValues(new Uint8Array(16)),key=await derive(password,salt);let data;
 if(record){try{data=JSON.parse(dec.decode(await crypto.subtle.decrypt({name:'AES-GCM',iv:record.iv},key,record.body)))}catch{throw new Error('La clave no coincide. Los datos guardados no se han modificado.')}}else if(context)data={usuario:context.usuario,salidas:{}};else throw new Error('No hay datos preparados en este dispositivo.');
 if(context&&data.usuario!==context.usuario)throw new Error('Los datos pertenecen a otra cuenta.');
 let saved=Boolean(record);
 const save=async()=>{if(saved&&active()?.id!==id)throw new Error('La sesión local cambió. Vuelve a preparar el dispositivo.');const iv=crypto.getRandomValues(new Uint8Array(12)),body=await crypto.subtle.encrypt({name:'AES-GCM',iv},key,enc.encode(JSON.stringify(data)));await use('readwrite',s=>s.put({iv,salt,body},id));localStorage.setItem(ACTIVE,JSON.stringify({id,usuario:data.usuario}));saved=true};
 return {data,save,close};
 } catch(e) {close();throw e;}
}
export function vigentes(data){return Object.values(data.salidas).filter(s=>s.vence>Date.now())}
export async function sesion(){const r=await fetch('/guias/offline/sesion',{headers:{Accept:'application/json'},cache:'no-store'});if(!r.ok)throw new Error('Inicia sesión de nuevo para sincronizar.');return r.json()}
export async function sync(vault,salida){
 const current=await sesion();if(current.usuario!==vault.data.usuario||current.id!==active()?.id){await clear();throw new Error('La cuenta cambió. Los datos locales se eliminaron.')}
 if(salida.vence<=Date.now())throw new Error('El manifiesto venció. Actualízalo con conexión.');if(!salida.cola.length)return [];
 const batch=salida.cola.map(x=>({...x})),r=await fetch(salida.urlSincronizar,{method:'POST',headers:{'Content-Type':'application/json',Accept:'application/json','X-CSRF-TOKEN':current.csrf},body:JSON.stringify({escaneos:batch})});
 if(!r.ok)throw new Error(r.status===403?'Ya no tienes acceso a esta salida. Revisa la asignación.':'No se pudo sincronizar. Conservamos los escaneos para reintentar.');
 const {resultados}=await r.json();if(resultados.length!==batch.length)throw new Error('Respuesta incompleta; se conserva la cola.');
 salida.conflictos=resultados.filter(x=>!x.ok);for(const res of resultados){const p=salida.pasajeros.find(p=>p.boleto===res.cod);if(p)p.est=res.ok?'validado':'conflicto'}
 salida.cola=salida.cola.filter(x=>!batch.some(b=>b.cod===x.cod&&b.vld_en===x.vld_en));await vault.save();return resultados;
}
export async function scan(vault,salida,codigo){
 if(salida.vence<=Date.now())throw new Error('El manifiesto venció. Actualízalo con conexión.');
 const cod=codigo.trim().toUpperCase(),p=salida.pasajeros.find(p=>p.boleto===cod);if(!p)throw new Error('El boleto no está en el manifiesto de esta salida.');if(['validado','pendiente_sync'].includes(p.est))return 'Ya estaba registrado.';if(p.est!=='emitido')throw new Error('Boleto no válido. Revisa el conflicto con conexión.');
 salida.cola.push({cod,vld_en:new Date().toISOString()});p.est='pendiente_sync';await vault.save();return 'Escaneo guardado. Se validará definitivamente al sincronizar.';
}
