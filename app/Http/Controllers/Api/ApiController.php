<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Clientes;
use Illuminate\Support\Facades\Auth;


class ApiController extends Controller
{
    //Store API - POST (name , email , password)

    public function store(Request $request)
    {

        //Validate parameters type_register
        if ($request->type_register == 'client_jmci') {

            //Create user client
            $request->validate([
                "nombres" => "required|string",
                "tipo_identidad" => "required|string",
                "numero_identidad" => ['required', 'string', 'regex:/^\d{8,10}$/', 'unique:clientes'],
                "email" => "required|string|email|unique:clientes",
                "password" => "required|confirmed" //password_confirmation
            ]);

            $client = Clientes::create([
                "nombres" => $request->nombres,
                "tipo_identidad" => $request->tipo_identidad,
                "numero_identidad" => $request->numero_identidad,
                "email" => $request->email,
                "password" => bcrypt($request->password),
                "estado" => 1
            ]);

            $token = auth()->guard('client-api')->attempt([
                "email" => $request->email,
                "password" => $request->password
            ]);
            if (!$token) {

                return response()->json([
                    "status" => false,
                    "message" => "Unauthorized"
                ]);
            }

            //Return response
            if (!$client) {
                $data = [
                    "status" => 500,
                    "message" => "User not created"
                ];
                return response()->json($data, 500);
            }
            $data = [
                "status" => 201,
                "data" => $client,
                "message" => "client created",
                "token" => $token,
                "expires_in" => auth()->factory()->getTTL()
            ];
            return response()->json($data, 201);
        } elseif ($request->type_register == 'user_jmci') {

            $admin = auth()->user()->admin;
            if ($admin != 1) {
                $data = [
                    "status" => 401,
                    "message" => "Usuario no tiene permiso para crear"
                ];
                return response()->json($data, 401);
            }

            //Validate parameters
            $request->validate([
                "name" => "required|string",
                "email" => "required|string|email|unique:users",
                "password" => "required|confirmed" //password_confirmation
            ]);

            //Create user
            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
              
                "password" => bcrypt($request->password)
            ]);

            //Return response
            if (!$user) {
                $data = [
                    "status" => 500,
                    "message" => "User not created"
                ];
                return response()->json($data, 500);
            }
            $data = [
                "status" => 201,
                "data" => $user,
                "message" => "User created"
            ];
            return response()->json($data, 201);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'type of register not found'
            ], 400);
        }
    }

    //User
    public function get_users()
    {
        $users = User::all();

        return response()->json([
            'usuarios' => $users,
            'status' => 200,
        ], 200);
    }

    public function put_users(Request $request, $id)
    {
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
        $request->validate([
            "name" => "sometimes|required|string",
            "email" => "sometimes|required|string|email|unique:users,email," . $id,
            "password" => "nullable|confirmed" // password_confirmation
        ]);

        // Buscar usuario
        $user = User::find($id);
        if (!$user) {
            $data = [
                "status" => 404,
                "message" => "Usuario no encontrado"
            ];
            return response()->json($data, 404);
        }

        // Actualizar usuario
        $user->name = $request->has('name') ? $request->name : $user->name;
        $user->email = $request->has('email') ? $request->email : $user->email;
        if ($request->has('password') && !empty($request->password)) {
            $user->password = bcrypt($request->password);
        }

        // Guardar cambios
        if (!$user->save()) {
            $data = [
                "status" => 500,
                "message" => "Usuario no actualizado"
            ];
            return response()->json($data, 500);
        }

        // Retornar respuesta
        $data = [
            "status" => 200,
            "data" => $user,
            "message" => "Usuario actualizado correctamente"
        ];
        return response()->json($data, 200);
    }

    public function delete_users($id){
        $admin = auth()->user()->admin;
        if ($admin != 1) {
            return response()->json([
                "status" => 401,
                "message" => "Usuario no tiene permiso para eliminar"
            ], 401);
        }

        // Find the user by ID
        $user = User::find($id);

        // Check if user exists
        if (!$user) {
            return response()->json([
                "status" => 404,
                "message" => "Usuario no encontrado"
            ], 404);
        }

        // Attempt to delete the user
        if ($user->delete()) {
            return response()->json([
                "status" => 200,
                "message" => "Usuario eliminado correctamente"
            ], 200);
        } else {
            return response()->json([
                "status" => 500,
                "message" => "Error al eliminar el usuario"
            ], 500);
        }
    }





    //Clientes
    public function get_clientes()
    {
        $clientes = Clientes::all();

        return response()->json([
            'clientes' => $clientes,
            'status' => 200,
        ], 200);
    }

    //Login API - POST (email, password)

    public function login(Request $request)
    {
        //Validate parameters
        $request->validate([
            "email" => "required|string|email",
            "password" => "required",
        ]);


        $token = auth()->attempt([
            "email" => $request->email,
            "password" => $request->password
        ]);

        if (!$token) {

            return response()->json([
                "status" => false,
                "message" => "Unauthorized"
            ], 400);
        }

        return response()->json([
            "status" => true,
            "message" => "User logged in",
            "token" => $token,
            'data' => auth()->user(),
            "expires_in" => auth()->factory()->getTTL()
        ]);
    }
    public function login_client(Request $request)
    {
        //Validate parameters
        $request->validate([
            "email" => "required|string|email",
            "password" => "required",
        ]);


        $token = auth()->guard('client-api')->attempt([
            "email" => $request->email,
            "password" => $request->password
        ]);

        if (!$token) {
            return response()->json([
                "status" => false,
                "message" => "Unauthorized",
            ], 400);
        }

        return response()->json([
            'status' => true,
            'message' => 'Client logged in successfully',
            "data" => auth('client-api')->user(),
            'token' => $token,
            'expires_in' => auth('client-api')->factory()->getTTL()
        ]);
    }



    //Profile API - GET (JWT Auth Token)

    public function profile()
    {
        //$userData = auth()->user();
        $userData = request()->user();

        return response()->json([
            "status" => true,
            "data" => $userData,
            "message" => "User profile data",
            "user_id" => request()->user()->id,
            "email" => request()->user()->email
        ]);
    }

    //Refresh Token API - GET (JWT Auth Token)

    public function refreshToken()
    {

        $token = auth()->refresh();

        return response()->json([
            "status" => true,
            "message" => "Token refreshed",
            "token" => $token,
            "expires_in" => auth()->factory()->getTTL()
        ]);
    }

    //Logout API - GET (JWT Auth Token)

    public function logout()
    {
        auth()->logout();

        return response()->json([
            "status" => true,
            "message" => "User logged out"
        ]);
    }
}
