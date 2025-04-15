<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function authUser(Request $request)
    {
        try {

            $credentials = $request->only(['email', 'password']);
            $validator = Validator::make($credentials, [
                "email" => "required|email|exists:users,email",
                "password" => "required",
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $token = auth('api')->attempt($credentials);

            if (!$token) {
                return ResponseHelper::responseApi(401, 'Credenciales inválidas.');
            }

            return ResponseHelper::responseApi(200, "Inicio de sesión correctamente", [
                "token" => $token,
                'user' => auth('api')->user(),
                "expires_in" => auth('api')->factory()->getTTL() * 60
            ]);

        } catch (\Throwable $th) {
            Log::error("Error al iniciar sesión con el usuario " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function authClient(Request $request)
    {
        try {

            $credentials = $request->only(['email', 'password']);
            $validator = Validator::make($credentials, [
                "email" => "required|email|exists:clientes,email",
                "password" => "required",
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $token = auth('client-api')->attempt($credentials);

            if (!$token) {
                return ResponseHelper::responseApi(401, 'Credenciales inválidas.');
            }

            return ResponseHelper::responseApi(200, "Inicio de sesión correctamente", [
                "token" => $token,
                'cliente' => auth('client-api')->user(),
                "expires_in" => auth('client-api')->factory()->getTTL() * 60
            ]);

        } catch (\Throwable $th) {
            Log::error("Error al iniciar sesión con el cliente " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function logoutClient()
    {
        try {

            auth('client-api')->logout();

            return ResponseHelper::responseApi(200, "Se ha cerrado correctamente");

        } catch (\Throwable $th) {
            Log::error("Error al cerrar la sesión del cliente " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function logoutUser()
    {
        try {

            auth('api')->logout();

            return ResponseHelper::responseApi(200, "Se ha cerrado correctamente");

        } catch (\Throwable $th) {
            Log::error("Error al cerrar la sesión del usuario " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }
}
