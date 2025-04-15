<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\ServicioExtra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ServicioExtraController extends Controller
{
    public function index()
    {

        try {

            $serviciosExtras = ServicioExtra::all();
            return ResponseHelper::responseApi(200, "Se ha obtenido correctamente", ["serviciosExtras" => $serviciosExtras]);

        } catch (\Throwable $th) {
            Log::error("Error al obtener todos los servicios extras " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function show($id)
    {

        try {

            $validator = Validator::make(["id"=>$id], [
                "id" => "required|exists:servicios_extras,id_servicio_extra"
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }
            $servicioExtra = ServicioExtra::find($id);
            return ResponseHelper::responseApi(200, "Se ha encontrado correctamente", ["servicioExtra" => $servicioExtra]);

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

            $servicio = ServicioExtra::create([
                "descripcion" => trim(strtoupper($request->descripcion))
            ]);


            return ResponseHelper::responseApi(201, "Se ha registrado correctamente", ["servicio" => $servicio]);
        } catch (\Throwable $th) {
            Log::error("Error al registrar un servicio extra " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function update(Request $request,$id)
    {

        try {
            $validatorId = Validator::make(["id"=>$id], [
                "id" => "required|exists:servicios_extras,id_servicio_extra"
            ]);

            if ($validatorId->fails()) {
                return ResponseHelper::responseApi(422, $validatorId->errors()->first());
            }

            $validator = Validator::make($request->all(), [
                "descripcion" => [
                    "required","string",Rule::unique("servicios_extras","descripcion")->ignore($id)
                ]
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $servicioExtra = ServicioExtra::find($id);
            $servicioExtra->descripcion = trim(strtoupper($request->descripcion));
            $servicioExtra->save();


            return ResponseHelper::responseApi(200, "Se ha actualizado correctamente", ["servicioExtra" => $servicioExtra]);
        } catch (\Throwable $th) {
            Log::error("Error al actualizar un servicio extra " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function destroy($id)
    {

        try {
            $validator = Validator::make(["id"=>$id], [
                "id" => "required|exists:servicios_extras,id_servicio_extra"
            ]);



            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $servicioExtra = ServicioExtra::with('inmueblesServiciosExtras')->find($id);

            if ($servicioExtra->inmueblesServiciosExtras()->count() > 0) {
                return ResponseHelper::responseApi(400, "El servicio no puede ser eliminado porque está en uso.");
            }

            $servicioExtra->delete();


            return ResponseHelper::responseApi(200, "Se ha eliminado correctamente", ["servicioExtra" => $servicioExtra]);
        } catch (\Throwable $th) {
            Log::error("Error al eliminar un tipo de servicio " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }
}
