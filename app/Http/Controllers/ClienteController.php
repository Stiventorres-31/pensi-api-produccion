<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Clientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function index()
    {
        try {
            $clientes = Clientes::all();
            return ResponseHelper::responseApi(200, "Se ha obtenido todos los clientes", ["clientes" => $clientes]);
        } catch (\Throwable $th) {
            Log::error("Error al obtener todos los clientes " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function store(Request $request)
    {
        try {
            //Create user client
            $validator = Validator::make($request->all(), [
                "nombres" => "required|string",
                "tipo_identidad" => "required|string",
                "numero_identidad" => ['required', 'string', 'regex:/^\d{8,10}$/', 'unique:clientes'],
                "email" => "required|string|email|unique:clientes",
                "password" => "required|confirmed" //password_confirmation
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $cliente = Clientes::create([
                "nombres" => $request->nombres,
                "tipo_identidad" => $request->tipo_identidad,
                "numero_identidad" => $request->numero_identidad,
                "email" => $request->email,
                "password" => bcrypt($request->password),
                "estado" => 1
            ]);


            $loginCliente = new AuthController();
            return $loginCliente->authClient($request);

            // $token = auth()->guard('client-api')->attempt([
            //     "email" => $request->email,
            //     "password" => $request->password
            // ]);
            // if (!$token) {

            //     return ResponseHelper::responseApi(false, "Unauthorized");
            // }



            // return ResponseHelper::responseApi(201, "Se ha registrado correctamente", ["cliente" => $cliente, "token" => $token, "expires_in" => auth()->factory()->getTTL()]);

            //return ResponseHelper::responseApi(201, "Se ha registrado correctamente", ["cliente" => $cliente]);
        } catch (\Throwable $th) {
            Log::error("Error al registrar un cliente " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }
}
