<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Rol;
use App\Models\DepartamentoBolivia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GestionUsuarios extends Component
{
    use WithPagination;

    public $search = '';

    public $modalAbierto = false;
    public $userId = null;
    public $nom = '';
    public $cor = '';
    public $clv = '';
    public $rol_id = '';
    public $tel = '';
    public $tip_doc = 'CI';
    public $num_doc = '';
    public $ci_cmp = '';
    public $ci_dep_id = '';

    public $mensajeExito = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function abrirModalCrear()
    {
        $this->reset(['userId', 'nom', 'cor', 'clv', 'rol_id', 'tel', 'num_doc', 'ci_cmp', 'ci_dep_id']);
        $this->tip_doc = 'CI';
        $this->resetValidation();
        $this->modalAbierto = true;
    }

    public function abrirModalEditar($id)
    {
        $this->resetValidation();
        $user = User::findOrFail($id);

        $this->userId = $user->id;
        $this->nom = $user->nom;
        $this->cor = $user->cor;
        $this->rol_id = $user->rol_id;
        $this->tel = $user->tel ?? '';
        $this->tip_doc = $user->tip_doc ?? 'CI';
        $this->num_doc = $user->num_doc ?? '';
        $this->ci_cmp = $user->ci_cmp ?? '';
        $this->ci_dep_id = $user->ci_dep_id ?? '';
        $this->clv = '';

        $this->modalAbierto = true;
    }

    public function cerrarModal()
    {
        $this->modalAbierto = false;
        $this->resetValidation();
    }

    public function guardar()
    {
        $rules = [
            'nom'       => 'required|string|max:150',
            'cor'       => 'required|email|max:150|unique:users,cor,' . ($this->userId ?? 'NULL') . ',id',
            'rol_id'    => 'required|exists:roles,id',
            'tel'       => 'nullable|string|max:20',
            'tip_doc'   => 'nullable|string|max:20',
            'num_doc'   => 'nullable|string|max:15',
            'ci_cmp'    => 'nullable|string|max:5',
            'ci_dep_id' => 'nullable|exists:departamentos_bolivia,id',
        ];

        if (!$this->userId) {
            $rules['clv'] = 'required|string|min:6';
        }

        $messages = [
            'nom.required'    => 'El nombre completo es obligatorio.',
            'cor.required'    => 'El correo electrónico es obligatorio.',
            'cor.email'       => 'Ingrese un correo electrónico válido.',
            'cor.unique'      => 'Este correo ya pertenece a otro usuario.',
            'rol_id.required' => 'Debe seleccionar un rol para el usuario.',
            'clv.required'    => 'La contraseña es obligatoria para un nuevo usuario.',
            'clv.min'         => 'La contraseña debe tener al menos 6 caracteres.',
        ];

        $this->validate($rules, $messages);

        $rol = Rol::find($this->rol_id);

        $data = [
            'nom'       => trim($this->nom),
            'cor'       => mb_strtolower(trim($this->cor)),
            'rol_id'    => $this->rol_id,
            'rol'       => $rol ? $rol->nom : 'turista',
            'tel'       => $this->tel ? trim($this->tel) : null,
            'tip_doc'   => $this->tip_doc,
            'num_doc'   => $this->num_doc ? trim($this->num_doc) : null,
            'ci_cmp'    => $this->ci_cmp ? trim($this->ci_cmp) : '',
            'ci_dep_id' => $this->tip_doc === 'CI' && $this->ci_dep_id ? $this->ci_dep_id : null,
        ];

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $user->update($data);
            $this->mensajeExito = "¡Usuario {$user->nom} actualizado correctamente!";
            $this->dispatch('swal:alert', [
                'icon'  => 'success',
                'title' => 'Usuario Actualizado',
                'text'  => "Los datos de {$user->nom} fueron guardados correctamente."
            ]);
        } else {
            $data['clv'] = Hash::make($this->clv);
            $data['act'] = true;
            $user = User::create($data);
            $this->mensajeExito = "¡Usuario {$user->nom} creado con éxito!";
            $this->dispatch('swal:alert', [
                'icon'  => 'success',
                'title' => 'Usuario Creado',
                'text'  => "El usuario {$user->nom} ha sido registrado exitosamente."
            ]);
        }

        $this->cerrarModal();
    }

    public function toggleEstado($id)
    {
        if (Auth::id() == $id) {
            $this->addError('general', 'No puedes desactivar tu propia cuenta de administrador.');
            $this->dispatch('swal:alert', [
                'icon'  => 'error',
                'title' => 'Acción Denegada',
                'text'  => 'No puedes desactivar tu propia cuenta de administrador.'
            ]);
            return;
        }

        $user = User::findOrFail($id);
        $user->update(['act' => !$user->act]);

        $accion = $user->act ? 'activado' : 'desactivado';
        $this->mensajeExito = "El usuario {$user->nom} ha sido {$accion}.";
        $this->dispatch('swal:toast', [
            'icon'  => $user->act ? 'success' : 'warning',
            'title' => "Usuario {$user->nom} {$accion}"
        ]);
    }

    public function render()
    {
        $query = User::with(['rolRelacion', 'departamento']);

        if (!empty($this->search)) {
            $buscar = trim($this->search);
            $query->where(function ($q) use ($buscar) {
                $q->where('nom', 'ILIKE', "%{$buscar}%")
                  ->orWhere('cor', 'ILIKE', "%{$buscar}%")
                  ->orWhere('num_doc', 'ILIKE', "%{$buscar}%")
                  ->orWhere('tel', 'ILIKE', "%{$buscar}%")
                  ->orWhereHas('rolRelacion', fn($r) => $r->where('nom', 'ILIKE', "%{$buscar}%"));
            });
        }

        $users = $query->orderBy('id', 'desc')->paginate(10);
        $roles = Rol::orderBy('id')->get();
        $departamentos = DepartamentoBolivia::orderBy('nom')->get();

        return view('livewire.gestion-usuarios', compact('users', 'roles', 'departamentos'));
    }
}
