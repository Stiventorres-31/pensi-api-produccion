<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Clientes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::all();

            return ResponseHelper::responseApi(200, "Se ha obtenido correctamente", ["users" => $users]);
        } catch (\Throwable $th) {
            Log::error("Error al obtener todos los usuarios " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }
    public function store(Request $request)
    {
        try {
            // $admin = auth()->user()->admin;

                $validator = Validator::make($request->all(), [
                    "name" => "required|string",
                    "email" => "required|string|email|unique:users,email",
                    "admin"=>"required|in:0,1",
                    "password" => "required|confirmed" //password_confirmation
                ]);

                if ($validator->fails()) {
                    return ResponseHelper::responseApi(422, $validator->errors()->first());
                }

                //Create user
                $user = User::create([
                    "name" => strtoupper($request->name),
                    "email" => $request->email,
                    "admin" => $request->admin,
                    "password" => bcrypt($request->password)
                ]);



                return ResponseHelper::responseApi(201, "Se ha registrado correctamente", ["user" => $user]);
        } catch (\Throwable $th) {
            Log::error("Error al registrar un usuario " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Verificar si el usuario es admin
            $admin = auth()->user()->admin;
            if ($admin != 1) {
                $data = [
                    "status" => 401,
                    "message" => "Usuario no tiene permiso para actualizar"
                ];
                return response()->json($data, 401);
            }

            // Validar parámetros
            $validator = Validator::make($request->all(), [
                "name" => "sometimes|required|string",
                "admin" => "sometimes|required|integer|between:0,1",
                "email" => [
                    "sometimes",
                    "email",
                    Rule::unique("users", "id")->ignore($id)
                ],
                "password" => "nullable|confirmed" // password_confirmation
            ]);
            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            // Buscar usuario
            $user = User::find($id);
            if (!$user) {
                return ResponseHelper::responseApi(404, "Usuario no encontrado");
                // $data = [
                //     "status" => 404,
                //     "message" => ""
                // ];
                // return response()->json($data, 404);
            }

            // Actualizar usuario
            $user->name = $request->has('name') ? $request->name : $user->name;
            $user->email = $request->has('email') ? $request->email : $user->email;
            $user->admin = $request->has('admin') ? $request->admin : $user->admin;

            if ($request->has('password') && !empty($request->password)) {
                $user->password = bcrypt($request->password);
            }

            // Guardar cambios
            $user->save();
            return ResponseHelper::responseApi(200, "Se ha actualizado correctamente", ["user" => $user]);
        } catch (\Throwable $th) {
            Log::error("Error al actualizar un usuario " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function destroy($id)
    {
        try {
            $validator = Validator::make(["id"=>$id], [
                "id" => "required|exists:users,id"
            ]);
            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $user = User::find($id);
            $user->delete();

            return ResponseHelper::responseApi(200,"Se ha eliminado correctamente");

        } catch (\Throwable $th) {
            Log::error("Error al eliminar un usuario " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }
}
