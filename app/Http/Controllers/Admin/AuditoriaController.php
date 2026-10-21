<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rol;
use App\Models\DepartamentoBolivia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuditoriaController extends Controller
{

    public function index(Request $request)
    {
        $query = User::with(['rolRelacion', 'departamento']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'ILIKE', "%{$search}%")
                  ->orWhere('cor', 'ILIKE', "%{$search}%")
                  ->orWhere('num_doc', 'ILIKE', "%{$search}%")
                  ->orWhere('tel', 'ILIKE', "%{$search}%")
                  ->orWhereHas('rol', fn($r) => $r->where('nom', 'ILIKE', "%{$search}%"));
            });
        }

        $users = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();
        $roles = Rol::orderBy('id')->get();
        $departamentos = DepartamentoBolivia::orderBy('nom')->get();

        return view('admin.panels.auditoria', compact('users', 'roles', 'departamentos'));
    }

    public function storeUsuario(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom'       => 'required|string|max:150',
                'cor'       => 'required|email|max:150|unique:users,cor',
                'clv'       => 'required|string|min:6',
                'rol_id'    => 'required|exists:roles,id',
                'tel'       => 'nullable|string|max:20',
                'tip_doc'   => 'nullable|string|max:20',
                'num_doc'   => 'nullable|string|max:15',
                'ci_cmp'    => 'nullable|string|max:5',
                'ci_dep_id' => 'nullable|exists:departamentos_bolivia,id',
            ], [
                'nom.required'    => 'El nombre completo es obligatorio.',
                'cor.required'    => 'El correo electrónico es obligatorio.',
                'cor.email'       => 'Ingrese un correo electrónico válido.',
                'cor.unique'      => 'Este correo electrónico ya se encuentra registrado.',
                'clv.required'    => 'La contraseña es obligatoria.',
                'clv.min'         => 'La contraseña debe tener al menos 6 caracteres.',
                'rol_id.required' => 'Debe seleccionar un rol para el usuario.',
            ]);

            $rol = Rol::find($validated['rol_id']);

            User::create([
                'nom'       => trim($validated['nom']),
                'cor'       => mb_strtolower(trim($validated['cor'])),
                'clv'       => Hash::make($validated['clv']),
                'rol_id'    => $validated['rol_id'],
                'rol'       => $rol ? $rol->nom : 'turista',
                'tel'       => $validated['tel'] ?? null,
                'tip_doc'   => $validated['tip_doc'] ?? null,
                'num_doc'   => $validated['num_doc'] ?? null,
                'ci_cmp'    => $validated['ci_cmp'] ?? '',
                'ci_dep_id' => ($validated['tip_doc'] ?? '') === 'CI' ? ($validated['ci_dep_id'] ?? null) : null,
                'act'       => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Usuario creado correctamente!',
            ], 201);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => collect($ve->errors())->flatten()->first(),
                'errors'  => $ve->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Error al registrar usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error en el servidor al registrar el usuario: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function updateUsuario(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'nom'       => 'required|string|max:150',
                'cor'       => 'required|email|max:150|unique:users,cor,' . $id,
                'rol_id'    => 'required|exists:roles,id',
                'clv'       => 'nullable|string|min:6',
                'tel'       => 'nullable|string|max:20',
                'tip_doc'   => 'nullable|string|max:20',
                'num_doc'   => 'nullable|string|max:15',
                'ci_cmp'    => 'nullable|string|max:5',
                'ci_dep_id' => 'nullable|exists:departamentos_bolivia,id',
                'act'       => 'nullable|boolean',
            ], [
                'nom.required'    => 'El nombre completo es obligatorio.',
                'cor.required'    => 'El correo electrónico es obligatorio.',
                'cor.email'       => 'Ingrese un correo electrónico válido.',
                'cor.unique'      => 'Este correo ya pertenece a otro usuario.',
                'clv.min'         => 'La nueva contraseña debe tener al menos 6 caracteres.',
                'rol_id.required' => 'Debe seleccionar un rol para el usuario.',
            ]);

            $rol = Rol::find($validated['rol_id']);

            $data = [
                'nom'       => trim($validated['nom']),
                'cor'       => mb_strtolower(trim($validated['cor'])),
                'rol_id'    => $validated['rol_id'],
                'rol'       => $rol ? $rol->nom : $user->rol,
                'tel'       => $validated['tel'] ?? null,
                'tip_doc'   => $validated['tip_doc'] ?? null,
                'num_doc'   => $validated['num_doc'] ?? null,
                'ci_cmp'    => $validated['ci_cmp'] ?? '',
                'ci_dep_id' => ($validated['tip_doc'] ?? '') === 'CI' ? ($validated['ci_dep_id'] ?? null) : null,
            ];

            if ($request->has('act')) {
                $data['act'] = (bool) $request->act;
            }

            if (!empty($validated['clv'])) {
                $data['clv'] = Hash::make($validated['clv']);
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => '¡Usuario actualizado correctamente!',
                'user'    => $user,
            ]);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => collect($ve->errors())->flatten()->first(),
                'errors'  => $ve->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error("Error al actualizar usuario {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error en el servidor al actualizar el usuario: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminación suave (Soft Delete) de usuario.
     */
    public function destroyUsuario($id)
    {
        try {
            if (Auth::id() == $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propia cuenta de administrador mientras tienes la sesión iniciada.',
                ], 422);
            }

            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => "El usuario {$user->nom} ha sido eliminado correctamente.",
            ]);
        } catch (\Throwable $e) {
            Log::error("Error al eliminar usuario {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar el usuario: ' . $e->getMessage(),
            ], 500);
        }
    }
}
