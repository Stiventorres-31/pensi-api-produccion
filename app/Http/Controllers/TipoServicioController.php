<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TipoServicioController extends Controller
{
    public function index()
    {

        try {

            $servicios = Servicio::all();
            return ResponseHelper::responseApi(200, "Se ha obtenido correctamente", ["servicios" => $servicios]);

        } catch (\Throwable $th) {
            Log::error("Error el obtener todos los tipos de servicios " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function show($id)
    {

        try {

            $validator = Validator::make(["id"=>$id], [
                "id" => "required|exists:tipos_servicios,id_tipo_servicio"
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }
            $servicio = Servicio::find($id);
            return ResponseHelper::responseApi(200, "Se ha encontrado correctamente", ["servicio" => $servicio]);

        } catch (\Throwable $th) {
            Log::error("Error al consultar un tipo de servicio " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function store(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                "descripcion" => "required|string|unique:tipos_servicios,descripcion"
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $servicio = Servicio::create([
                "descripcion" => trim(strtoupper($request->descripcion))
            ]);


            return ResponseHelper::responseApi(201, "Se ha registrado correctamente", ["servicio" => $servicio]);
        } catch (\Throwable $th) {
            Log::error("Error el registrar un tipo de servicio " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function update(Request $request,$id)
    {

        try {
            $validatorId = Validator::make(["id"=>$id], [
                "id" => "required|exists:tipos_servicios,id_tipo_servicio"
            ]);

            if ($validatorId->fails()) {
                return ResponseHelper::responseApi(422, $validatorId->errors()->first());
            }

            $validator = Validator::make($request->all(), [
                "descripcion" => [
                    "required","string",Rule::unique("tipos_servicios","descripcion")->ignore($id)
                ]
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $servicio = Servicio::find($id);
            $servicio->descripcion = trim(strtoupper($request->descripcion));
            $servicio->save();


            return ResponseHelper::responseApi(200, "Se ha actualizado correctamente", ["servicio" => $servicio]);
        } catch (\Throwable $th) {
            Log::error("Error al actualizar un tipo de servicio " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function destroy($id)
    {

        try {
            $validator = Validator::make(["id"=>$id], [
                "id" => "required|exists:tipos_servicios,id_tipo_servicio"
            ]);



            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $servicio = Servicio::with('inmueblesServicios')->find($id);

            if ($servicio->inmueblesServicios()->count() > 0) {
                return ResponseHelper::responseApi(400, "El servicio no puede ser eliminado porque está en uso.");
            }

            $servicio->delete();


            return ResponseHelper::responseApi(200, "Se ha eliminado correctamente", ["servicio" => $servicio]);
        } catch (\Throwable $th) {
            Log::error("Error al eliminar un tipo de servicio " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }
}
