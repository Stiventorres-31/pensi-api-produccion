<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\InmuServicioExtra;
use App\Models\ServicioExtra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InmuebleServicioExtraController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "inmueble_id" => "required|integer|exists:inmuebles,id_inmueble",
                "servicio_extra_id" => [
                    'required',
                    'integer',
                    'exists:servicios_extras,id_servicio_extra',
                    Rule::unique('inmuebles_servicios_extras', 'id_inmueble_servicio_extra')->where(function ($query) use ($request) {
                        return $query->where('id_inmueble', $request->inmueble_id);
                    })

                ],
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }


            $inmu_servi = InmuServicioExtra::create([
                "id_inmueble" => $request->inmueble_id,
                "id_servicio_extra" => $request->servicio_extra_id,
            ]);


            return ResponseHelper::responseApi(201, "Se ha registrado correctamente", ["inmueble_servicio" => $inmu_servi]);
        } catch (\Throwable $th) {
            Log::error("Error al registrar un tipo de servicio al inmueble " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function destroy($id){
        try {
            $validator = Validator::make(["id"=>$id], [
                "id" => "required|integer|exists:inmuebles_servicios_extras,id_inmueble_servicio_extra"
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }
            $inmu_servi = InmuServicioExtra::find($id);
            $inmu_servi->delete();
            return ResponseHelper::responseApi(201, "Se ha registrado correctamente");
        } catch (\Throwable $th) {
            Log::error("Error al eliminar un tipo de servicio al inmueble " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }
}
