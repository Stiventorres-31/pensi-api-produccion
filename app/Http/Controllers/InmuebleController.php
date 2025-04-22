<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Inmueble;
use App\Models\InmuServicio;
use App\Models\InmuServicioExtra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InmuebleController extends Controller
{

    public function index()
    {
        try {
            $inmuebles = Inmueble::where("estado", 1)->get();
            return ResponseHelper::responseApi(200, "Se ha obtenido correctamente", ["inmuebles" => $inmuebles]);
        } catch (\Throwable $th) {
            Log::error("Error al obtener todos los datos " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }



    //BUSCAR POR NOMBRE
    public function search(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "search" => "required|string"
            ]);
            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }



            $inmuebles = Inmueble::with(['servicios_ex.servicio_ex', 'servicios.servicio', 'fotos', 'genero', 'usuario'])
            ->where(function ($query) use ($request) {
                $query->where("nombre", "like", '%' . $request->search . '%')
                      ->orWhere("descripcion", "like", '%' . $request->search . '%')
                      ->orWhere("codigo", "like", '%' . $request->search . '%');
            })
                ->where("estado", 1)
                ->orderByDesc('created_at')
                ->get();
            return ResponseHelper::responseApi(200, "Se ha obtenido correctamente", ["inmuebles" => $inmuebles]);
        } catch (\Throwable $th) {
            Log::error("Error al consultar un inmueble por nombre " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }
    public function index_user()
    {
        try {
            $inmuebles = Inmueble::all();
            return ResponseHelper::responseApi(200, "Se ha obtenido correctamente", ["inmuebles" => $inmuebles]);
        } catch (\Throwable $th) {
            Log::error("Error al obtener todos los datos " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "codigo" => "required|string|max:20|unique:inmuebles,codigo",
                "nombre" => "required|string",
                "direccion" => "required|string",
                "pais" => "required|string",
                "region" => "required|string",
                "ciudad" => "required|string",
                "medida" => "required|string|max:10",
                "precio" => "required|numeric|between:0,999999999.99",
                "porcentaje_descuento" => "sometimes|numeric|between:0,100",
                "precio_descuento" => "sometimes|numeric|between:0,999999999.99",
                "habitaciones" => "required|integer",
                "id_genero" => "required|integer|exists:generos,id_genero",
                "descripcion" => "required|string",
                "destacado" => "sometimes|integer|in:0,1",
                "link" => "required|string|url|max:255",
                "estado" => "sometimes|integer|in:0,1"
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $inmueble = Inmueble::create([
                "codigo" => strtoupper(trim($request->codigo)),
                "nombre" => strtoupper(trim($request->nombre)),
                "direccion" => strtoupper(trim($request->direccion)),
                "pais" => strtoupper(trim($request->pais)),
                "region" => strtoupper(trim($request->region)),
                "ciudad" => strtoupper(trim($request->ciudad)),
                "medida" => strtoupper(trim($request->medida)),
                "precio" => trim($request->precio),
                "porcentaje_descuento" => trim($request->get('porcentaje_descuento', 0)),
                "precio_descuento" => trim($request->get('precio_descuento', 0)),
                "habitaciones" => strtoupper(trim($request->habitaciones)),
                "id_usuario" => auth()->user()->id,
                "id_genero" => strtoupper(trim($request->id_genero)),
                "destacado" => strtoupper(trim($request->get('destacado', 0))),
                "link" => trim($request->link),
                "estado" => strtoupper(trim($request->get('estado', 0))),
                "descripcion" => strtoupper(trim($request->descripcion)),
            ]);

            return ResponseHelper::responseApi(201, "Se ha registrado correctamente", ["inmueble" => $inmueble]);
        } catch (\Throwable $th) {
            Log::error("Error al registrar un inmueble " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function show($id)
    {
        try {
            $validator = Validator::make(["id" => $id], [
                "id" => "required|integer|max:20|exists:inmuebles,id_inmueble"
            ]);
            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $inmueble = Inmueble::with(['servicios_ex.servicio_ex', 'servicios.servicio', 'fotos', 'genero', 'usuario'])->find($id);
            return ResponseHelper::responseApi(200, "Se ha encontrado correctamente", ["inmueble" => $inmueble]);
        } catch (\Throwable $th) {
            Log::error("Error al consultar un inmueble " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make(array_merge($request->all()), [
                "id" => "required|exists:inmuebles,id_inmueble",
                "codigo" => [

                    "required",
                    "string",
                    Rule::unique("inmuebles", "codigo")->ignore($id, "id_inmueble")
                ],
                "nombre" => "required|string",
                "direccion" => "required|string",
                "pais" => "required|string",
                "region" => "required|string",
                "ciudad" => "required|string",
                "medida" => "required|string|max:10",
                "precio" => "required|numeric|between:0,999999999.99",
                "porcentaje_descuento" => "sometimes|numeric|between:0,100",
                "precio_descuento" => "sometimes|numeric|between:0,999999999.99",
                "habitaciones" => "required|integer",
                "id_genero" => "required|integer|exists:generos,id_genero",
                "descripcion" => "required|string",
                "destacado" => "sometimes|integer|in:0,1",
                "link" => "required|string|url|max:255",
                "estado" => "sometimes|integer|in:0,1"
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $inmueble = Inmueble::find($id);
            $inmueble->codigo = strtoupper(trim($request->codigo));
            $inmueble->nombre = strtoupper(trim($request->nombre));
            $inmueble->direccion = strtoupper(trim($request->direccion));
            $inmueble->pais = strtoupper(trim($request->pais));
            $inmueble->region = strtoupper(trim($request->region));
            $inmueble->ciudad = strtoupper(trim($request->ciudad));
            $inmueble->medida = trim($request->medida);
            $inmueble->precio = trim($request->precio);
            $inmueble->porcentaje_descuento = trim($request->get('porcentaje_descuento', 0));
            $inmueble->precio_descuento = trim($request->get('precio_descuento', 0));
            $inmueble->habitaciones = trim($request->habitaciones);
            $inmueble->id_genero = trim($request->id_genero);
            $inmueble->descripcion = strtoupper(trim($request->descripcion));
            $inmueble->destacado = trim($request->get('destacado', 0));
            $inmueble->link = trim($request->link);
            $inmueble->estado = trim($request->get('estado', 0));
            $inmueble->save();

            return ResponseHelper::responseApi(200, "Se ha actualizado correctamente", ["inmueble" => $inmueble]);
        } catch (\Throwable $th) {
            Log::error("Error al actualizar un inmueble " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    // public function update_show(Request $request, $id)
    // {
    //     try {
    //         $validator = Validator::make(["id" => $id], [
    //             "id" => "required|exists:inmuebles,id_inmueble",
    //         ]);

    //         if ($validator->fails()) {
    //             return ResponseHelper::responseApi(422, $validator->errors()->first());
    //         }

    //         $inmueble = Inmueble::find($id);
    //         $inmueble->estado = 1;
    //         $inmueble->save();

    //         return ResponseHelper::responseApi(200, "Se ha actualizado correctamente", ["inmueble" => $inmueble]);
    //     } catch (\Throwable $th) {
    //         Log::error("Error al activar un inmueble " . $th->getMessage());
    //         return ResponseHelper::responseApi(500, "Error interno en el servidor");
    //     }
    // }

    public function destroy(Request $request, $id)
    {
        try {
            $validator = Validator::make(["id" => $id], [
                "id" => "required|exists:inmuebles,id_inmueble",
            ]);

            if ($validator->fails()) {
                return ResponseHelper::responseApi(422, $validator->errors()->first());
            }

            $inmueble = Inmueble::find($id);
            $inmueble->estado = !$inmueble->estado;
            $inmueble->save();

            return ResponseHelper::responseApi(200, "Se ha actualizado correctamente", ["inmueble" => $inmueble]);
        } catch (\Throwable $th) {
            Log::error("Error al cambiar estado de un inmueble " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }

    public function get_destacado()
    {
        try {
            $inmuebles = Inmueble::with(['servicios_ex.servicio_ex', 'servicios.servicio', 'fotos', 'genero', 'usuario'])->where('destacado', 1)->where('estado', 1)->orderByDesc('created_at')->get();

            return ResponseHelper::responseApi(200, "Se ha obtenido correctamente", ["inmuebles_destacados" => $inmuebles]);
        } catch (\Throwable $th) {
            Log::error("Error al obtener los inmuebles destacados " . $th->getMessage());
            return ResponseHelper::responseApi(500, "Error interno en el servidor");
        }
    }
    public function filtro(Request $request)
    {


        try {
            $inmuebles = Inmueble::with(['servicios', 'servicios_ex']) // relaciones necesarias
                ->where('estado', 1)
                ->when($request->precio_max, function ($query, $precioMax) {
                    $query->whereBetween('precio', [0, $precioMax]);
                })
                ->when($request->filled(['descuento_min', 'descuento_max']), function ($query) use ($request) {
                    $query->whereBetween('porcentaje_descuento', [$request->descuento_min, $request->descuento_max]);
                })
                ->when($request->region, fn($query, $region) => $query->where('region', $region))
                ->when($request->pais, fn($query, $pais) => $query->where('pais', $pais))
                ->when($request->ciudad, fn($query, $ciudad) => $query->where('ciudad', $ciudad))
                ->when($request->ubicacion, function ($query, $ubicacion) {
                    $query->where(function ($q) use ($ubicacion) {
                        $q->where('region', 'like', "%{$ubicacion}%")
                            ->orWhere('pais', 'like', "%{$ubicacion}%")
                            ->orWhere('ciudad', 'like', "%{$ubicacion}%");
                    });
                })
                ->when($request->filled(['min_habitaciones', 'max_habitaciones']), function ($query) use ($request) {
                    $query->whereBetween('habitaciones', [
                        $request->min_habitaciones ?? 0,
                        $request->max_habitaciones
                    ]);
                })->when($request->min_habitaciones && !$request->max_habitaciones, function ($query) use ($request) {
                    $query->where('habitaciones', '>=', $request->min_habitaciones);
                })->when(!$request->min_habitaciones && $request->max_habitaciones, function ($query) use ($request) {
                    $query->where('habitaciones', '<=', $request->max_habitaciones);
                })->when($request->id_genero, fn($query, $id) => $query->where('id_genero', $id))
                ->when($request->id_servicio_extra, function ($query, $id) {
                    $query->whereHas('servicios_ex', fn($q) => $q->where('id_servicio_extra', $id));
                })->when($request->id_tipo_servicio, function ($query, $id) {
                    $query->whereHas('tiposServicios', fn($q) => $q->where('id_tipo_servicio', $id));
                })->when($request->metros, function ($query, $metros) {
                    $query->whereRaw('CAST(medida AS SIGNED) <= ?', [intval($metros)]);
                })->orderByDesc('created_at')
                ->get();

            return ResponseHelper::responseApi(200, "Se ha obtenido correctamente", ["inmuebles" => $inmuebles]);
        } catch (\Throwable $th) {

            Log::error("Error en el filtro de inmuebles cliente ". $th->getMessage());
            return ResponseHelper::responseApi(500, $th->getMessage());
        }
    }
}
