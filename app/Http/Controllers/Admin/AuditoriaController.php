<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rol;
use App\Models\DepartamentoBolivia;
use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuditoriaController extends Controller
{
    public function index()
    {
        // SoftDeletes filtra automáticamente los registros donde eli_en no sea null
        $users = User::with(['rol', 'departamento'])
            ->orderBy('id', 'desc')
            ->get();

        $roles = Rol::all();
        $departamentos = DepartamentoBolivia::all();

        $bitacora = BitacoraAuditoria::with('usuario')
            ->orderBy('cre_en', 'desc')
            ->limit(100)
            ->get();

        return view('admin.panels.auditoria', compact('users', 'roles', 'departamentos', 'bitacora'));
    }

    public function storeUsuario(Request $request)
    {
        $request->validate([
            'nom'       => 'required|string|max:150',
            'cor'       => 'required|email|unique:users,cor',
            'clv'       => 'required|string|min:6',
            'rol_id'    => 'required|exists:roles,id',
            'tel'       => 'nullable|string|max:20',
            'tip_doc'   => 'nullable|string|max:20',
            'num_doc'   => 'nullable|string|max:15',
            'ci_cmp'    => 'nullable|string|max:5',
            'ci_dep_id' => 'nullable|required_if:tip_doc,CI|exists:departamentos_bolivia,id',
        ]);

        User::create([
            'nom'       => $request->nom,
            'cor'       => $request->cor,
            'clv'       => Hash::make($request->clv),
            'rol_id'    => $request->rol_id,
            'tel'       => $request->tel,
            'tip_doc'   => $request->tip_doc,
            'num_doc'   => $request->num_doc,
            'ci_cmp'    => $request->ci_cmp ?? '',
            'ci_dep_id' => $request->tip_doc === 'CI' ? $request->ci_dep_id : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado correctamente'
        ]);
    }

    public function updateUsuario(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nom'       => 'required|string|max:150',
            'cor'       => 'required|email|unique:users,cor,' . $id,
            'rol_id'    => 'required|exists:roles,id',
            'tel'       => 'nullable|string|max:20',
            'tip_doc'   => 'nullable|string|max:20',
            'num_doc'   => 'nullable|string|max:15',
            'ci_cmp'    => 'nullable|string|max:5',
            'ci_dep_id' => 'nullable|required_if:tip_doc,CI|exists:departamentos_bolivia,id',
        ]);

        $data = [
            'nom'       => $request->nom,
            'cor'       => $request->cor,
            'rol_id'    => $request->rol_id,
            'tel'       => $request->tel,
            'tip_doc'   => $request->tip_doc,
            'num_doc'   => $request->num_doc,
            'ci_cmp'    => $request->ci_cmp ?? '',
            'ci_dep_id' => $request->tip_doc === 'CI' ? $request->ci_dep_id : null,
        ];

        if ($request->filled('clv')) {
            $data['clv'] = Hash::make($request->clv);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente'
        ]);
    }

    public function destroyUsuario($id)
    {
        $user = User::findOrFail($id);
        
        // Ejecuta SoftDelete nativo actualizando automaticamente eli_en
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente'
        ]);
    }
}