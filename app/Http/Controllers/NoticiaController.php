<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Noticia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NoticiaController extends Controller
{

    public function index(){
        try {
            $noticias = Noticia::with(['usuario'])->orderBy('created_at', 'desc')->get();

            return ResponseHelper::responseApi(200,"Se han obtenidos correctamente",["noticias"=>$noticias]);

        } catch (\Throwable $th) {
            Log::error("Error al obtener todas las noticias " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }
    public function show($id){
        try {
            $validator = Validator::make(["id"=>$id], [
                "id"=>"required|exists:noticias,id"
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $noticia = Noticia::find($id);

            return ResponseHelper::responseApi(200,"Se ha encontrado correctamente",["noticia"=>$noticia]);

        } catch (\Throwable $th) {
            Log::error("Error al consultar una noticia " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'etiqueta' => 'required|string|max:50',
                'foto' => 'required|image|mimes:jpeg,png,jpg',
                'link' => 'string|url|max:255',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }


            $folderPath = 'public/noticias';

            $path = $request->file('foto')->store($folderPath);
            //$url = Storage::disk('public')->url($path);
            $url = Storage::url($path);
            // Crear la noticia
            $noticia = Noticia::create([
                'titulo' => strtoupper(trim($request->titulo)),
                'descripcion' => strtoupper(trim($request->descripcion)),
                'etiqueta' => strtoupper(trim($request->etiqueta)),
                'url_foto' => trim($url),
                'link' => strtoupper(trim($request->link)),
                'id_user' => auth()->user()->id,
            ]);

            return ResponseHelper::responseApi(201, "Se ha registrado correctamente", ["noticia" => $noticia]);
        } catch (\Throwable $th) {
            Log::error("Error al registrar un noticia " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'etiqueta' => 'required|string|max:50',
                // 'foto' => 'required|image|mimes:jpeg,png,jpg|max:4096',
                'link' => 'string|url|max:255',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }


            $noticia = Noticia::find($id);
            $campos = $request->only(['titulo', 'descripcion', 'etiqueta', 'link']);

            $noticia->fill($campos);

            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('noticias', 'public');
                $noticia->foto = $path;
            }

            if ($noticia->isDirty()) {
                $noticia->save();
                return ResponseHelper::responseApi(200, "Se ha actualizado correctamente", ["noticia" => $noticia]);
            }

            return ResponseHelper::responseApi(200, "No hubo cambios para actualizar", ["noticia" => $noticia]);
        } catch (\Throwable $th) {
            Log::error("Error al actualizar una noticia " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function image_update(Request $request,$id){



        try {
            $validator = Validator::make(array_merge($request->all(), ['id' => $id]), [
                'id'   => 'required|integer|exists:noticias,id',
                'foto' => 'required|image|mimes:jpeg,png,jpg',

            ]);
            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $noti = Noticia::find($id);
            $path = parse_url($noti->url_foto, PHP_URL_PATH);
            $storagePath = substr($path, strlen('/storage/'));
            if (Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
            }

            // Guardar la foto
            $folderPath = 'public/noticias';
            $path = $request->file('foto')->store($folderPath);
            $url = Storage::url($path);
            $noti->url_foto = $url;

            $noti->save();

            return ResponseHelper::responseApi(200,"Se ha actualizado correctamente");

        } catch (\Throwable $th) {
            Log::error("Error al actualizar la imagen de una noticia " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }catch (\Exception $e) {
            Log::error("Exception al actualizar la imagen de una noticia " . $e->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }

    }

    public function destroy($id){


        try {
            $validator = Validator::make(['id' => $id], [
                'id'   => 'required|integer|exists:noticias,id',
            ]);
            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }
            $noticias = Noticia::find($id);
            // Extraer el path del archivo desde la URL
            $path = parse_url($noticias->url_foto, PHP_URL_PATH);

            // Remover el prefijo '/storage/' para obtener el path relativo en el sistema de archivos
            $storagePath = substr($path, strlen('/storage/'));

            // Eliminar la foto del almacenamiento
            if (Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
            }

            $noticias->delete();

            return ResponseHelper::responseApi(200,"Se ha eliminado correctamente");


        } catch (\Throwable $th) {
            Log::error("Error al eliminar una noticia " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }catch (\Exception $e) {
            Log::error("Exception al eliminar una noticia " . $e->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }
}
