<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Foto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FotoController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "inmueble_id" => "required|integer|exists:inmuebles,id_inmueble",
                "fotos" => "required|array",
                "fotos.*" => "image|mimes:jpeg,png,jpg|dimensions:width=1080,height=1080|max:4096",
                "destacado" => "integer|in:0,1"
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }
            foreach ($request->file('fotos') as $foto) {
                $folderPath = 'inmuebles/' . $request->inmueble_id;
                $path = $foto->store($folderPath, 'public'); // Asegúrate de especificar 'public' como el segundo argumento

                $url = Storage::url($path);
                Foto::create([
                    'id_inmueble' => $request->inmueble_id,
                    'url' => $url,
                    'destacado' => $request->get('destacado', 0)
                ]);
            }
            return ResponseHelper::responseApi(201, "Se ha registrado correctamente");
        } catch (\Throwable $th) {
            Log::error("Error al subir las fotos del inmueble " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function show($id)
    {
        try {
            $validator = Validator::make(["id" => $id], [
                "id" => "required|integer|exists:fotos,id_inmueble",
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $fotos = Foto::where('id_inmueble', $id)->get();

            return ResponseHelper::responseApi(200, "Se ha encontrado correctamente", ["fotos" => $fotos]);
        } catch (\Throwable $th) {
            Log::error("Error al subir las fotos del inmueble " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function destroy($id)
    {

        try {
            $validator = Validator::make(["id" => $id], [
                "id" => "required|integer|exists:fotos,id_foto",
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }
            $foto = Foto::find($id);
            $path = parse_url($foto->url, PHP_URL_PATH);
            $storagePath = substr($path, strlen('/storage/'));


            if (Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
            }

            $foto->delete();
            return ResponseHelper::responseApi(200, "Se ha eliminado correctamente");
        } catch (\Throwable $th) {
            Log::error("Error al eliminar una foto del inmueble " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor",$th->getMessage());
        }

    }
}
