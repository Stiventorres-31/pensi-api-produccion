<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Genero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GeneroController extends Controller
{
    public function index()
    {

        try {
            $generos = Genero::all();

            return ResponseHelper::responseApi(200, "Se ha obtenido correctamente", ["generos" => $generos]);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error("Error al obtener todos los generos " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error en el servidor");
        }
    }

    public function store(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                "descripcion" => "required|string|unique:generos,descripcion"
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $genero = Genero::create([
                "descripcion" => $request->descripcion,
            ]);

            return ResponseHelper::responseApi(201, "Genero creado correctamente", ["genero" => $genero]);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error("Error al registrar un genero " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error en el servidor");
        }
    }

    public function show($id)
    {
        try {
            $validator = Validator::make(["id" => $id], [
                "id" => "required|integer|exists:generos,id_genero"
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $genero = Genero::find($id);

            return ResponseHelper::responseApi(201, "Se ha encontrado correctamente", ["genero" => $genero]);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error("Error al consultar un genero " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error en el servidor");
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make(["id" => $id], [
                "id" => "required|integer|exists:generos,id_genero"
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $validatorBody = Validator::make($request->all(), [
                "descripcion" => [
                    "required",
                    "string",
                    Rule::unique("generos", "descripcion")->ignore($id)
                ]
            ]);

            if ($validatorBody->fails()) {
                return ResponseHelper::responseApi(422, $validatorBody->errors()->first());
            }

            $genero = Genero::find($id);
            $genero->descripcion = $request->descripcion;
            $genero->save();

            return ResponseHelper::responseApi(200, "Se ha actualizado correctamente", ["genero" => $genero]);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error("Error al actualizar un genero " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error en el servidor");
        }
    }

    public function destroy($id){
        try {
            $validator = Validator::make(["id" => $id], [
                "id" => "required|integer|exists:generos,id_genero"
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }


            $genero = Genero::with('inmubles_g')->find($id);
            if ($genero->inmubles_g()->count() > 0) {
                return ResponseHelper::responseApi(400,"No se puede liminar porque está en uso");
            }

            $genero->delete();
            return ResponseHelper::responseApi(200, "Se ha eliminado correctamente");
        } catch (\Throwable $th) {
            //throw $th;
            Log::error("Error al eliminar un genero " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error en el servidor");
        }

    }
}
