<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\InmuServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InmuebleServicioController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "inmueble_id" => "required|integer|exists:inmuebles,id_inmueble",
                "servicio_id" => [
                    'required',
                    'integer',
                    'exists:tipos_servicios,id_tipo_servicio',
                    Rule::unique('inmuebles_tipos_servicios', 'id_tipo_servicio')->where(function ($query) use ($request) {
                        return $query->where('id_inmueble', $request->inmueble_id);
                    })

                ],
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }


            $inmu_servi = InmuServicio::create([
                "id_inmueble" => $request->inmueble_id,
                "id_tipo_servicio" => $request->servicio_id,
            ]);
            // $inmu_servi= Inmueble::find($request->inmueble_id);

            // $inmu_servi->servicios()->attach($request->servicio_id);

            return ResponseHelper::responseApi(201, "Se ha registrado correctamente", ["inmueble_servicio" => $inmu_servi]);
        } catch (\Throwable $th) {
            Log::error("Error al registrar un tipo de servicio al inmueble " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function destroy($id){
        try {
            $validator = Validator::make(["id"=>$id], [
                "id" => "required|integer|exists:inmuebles_tipos_servicios,id_inmueble_tipo_servicio"
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }
            $inmu_servi = InmuServicio::find($id);
            $inmu_servi->delete();
            return ResponseHelper::responseApi(201, "Se ha registrado correctamente");
        } catch (\Throwable $th) {
            Log::error("Error al eliminar un tipo de servicio al inmueble " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }
}
