<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Genero;
use App\Models\ServicioExtra;
use App\Models\InmuServicioExtra;
use App\Models\InmuServicio;
use App\Models\Servicio;
use App\Models\Inmueble;
use App\Models\Foto;
use App\Models\Noticia;

class InmublesController extends Controller
{

    /**
     * Crud generos
     */
    public function generos()
    {

        $generos = Genero::all();

        return response()->json([
            'generos' => $generos,
            'status' => 200,
        ], 200);
    }

    public function store_generos(Request $request)
    {

        $request->validate([
            "descripcion" => "required|string",

        ]);

        $genero = Genero::create([
            "descripcion" => $request->descripcion,
        ]);


        if (!$genero) {
            $data = [
                "status" => 500,
                "message" => "El genero no se ha creado correctamente"
            ];
            return response()->json($data, 500);
        }
        $data = [
            "status" => 201,
            "data" => $genero,
            "message" => "Genero creado correctamente"
        ];
        return response()->json($data, 201);
    }

    public function show_genero($id)
    {

        $genero = Genero::find($id);

        if (!$genero) {
            $data = [
                "status" => 404,
                "message" => "El genero no existe"
            ];
            return response()->json($data, 404);
        }

        $data = [
            "status" => 200,
            "data" => $genero,
            "message" => "Genero encontrado"
        ];
        return response()->json($data, 200);
    }

    public function destroy_genero($id)
    {

        $genero = Genero::with('inmubles_g')->find($id);

        if (!$genero) {
            $data = [
                "status" => 404,
                "message" => "El genero no existe"
            ];
            return response()->json($data, 404);
        }
        if ($genero->inmubles_g()->count() > 0) {
            return response()->json([
                "status" => 400,
                "message" => "El genero no puede ser eliminado porque está en uso."
            ], 400);
        }

        $genero->delete();
        $data = [
            "status" => 200,
            "message" => "Genero eliminado correctamente"
        ];
        return response()->json($data, 200);
    }


    public function update_genero(Request $request, $id)
    {
        $genero = Genero::find($id);

        if (!$genero) {
            $data = [
                "status" => 404,
                "message" => "El genero no existe"
            ];
            return response()->json($data, 404);
        }

        $request->validate([
            "descripcion" => "required|string",

        ]);

        //$genero->update($request->all());
        $genero->descripcion = $request->descripcion;
        $genero->save();

        $data = [
            "status" => 200,
            "data" => $genero,
            "message" => "Genero actualizado correctamente"
        ];
        return response()->json($data, 200);
    }


    /**
     * Crud Servios Extra
     */
    public function servicios_ex()
    {

        $servicios_extra = ServicioExtra::all();

        return response()->json([
            'servicios_extra' => $servicios_extra,
            'status' => 200,
        ], 200);
    }

    public function store_servicios_ex(Request $request)
    {

        $request->validate([
            "descripcion" => "required|string",

        ]);

        $servicio_extra = ServicioExtra::create([
            "descripcion" => $request->descripcion,
        ]);

        if (!$servicio_extra) {
            $data = [
                "status" => 500,
                "message" => "El servicio extra no se ha creado correctamente"
            ];
            return response()->json($data, 500);
        }

        $data = [
            "status" => 201,
            "data" => $servicio_extra,
            "message" => "Servicio extra creado correctamente"
        ];

        return response()->json($data, 201);
    }

    public function show_servicio_ex($id)
    {

        $servicio_extra = ServicioExtra::find($id);

        if (!$servicio_extra) {
            $data = [
                "status" => 404,
                "message" => "El servicio extra no existe"
            ];
            return response()->json($data, 404);
        }

        $data = [
            "status" => 200,
            "data" => $servicio_extra,
            "message" => "Servicio extra encontrado"
        ];
        return response()->json($data, 200);
    }

    public function destroy_servicio_ex($id)
    {

        //$servicio_extra = ServicioExtra::find($id);
        $servicio_extra = ServicioExtra::with('inmueblesServiciosExtras')->find($id);

        if (!$servicio_extra) {
            $data = [
                "status" => 404,
                "message" => "El servicio extra no existe"
            ];
            return response()->json($data, 404);
        }
        if ($servicio_extra->inmueblesServiciosExtras()->count() > 0) {
            return response()->json([
                "status" => 400,
                "message" => "El servicio extra no puede ser eliminado porque está en uso."
            ], 400);
        }

        $servicio_extra->delete();
        $data = [
            "status" => 200,
            "message" => "Servicio extra eliminado correctamente"
        ];
        return response()->json($data, 200);
    }

    public function update_servicio_ex(Request $request, $id)
    {

        $servicio_extra = ServicioExtra::find($id);

        if (!$servicio_extra) {
            $data = [
                "status" => 404,
                "message" => "El servicio extra no existe"
            ];
            return response()->json($data, 404);
        }

        $request->validate([
            "descripcion" => "required|string",

        ]);

        $servicio_extra->descripcion = $request->descripcion;
        $servicio_extra->save();

        $data = [
            "status" => 200,
            "data" => $servicio_extra,
            "message" => "Servicio extra actualizado correctamente"
        ];
        return response()->json($data, 200);
    }


    /**
     * Crud inmuesbles servicios extra
     */

    public function inmu_servi_extra()
    {

        $inmu_servi_extra = InmuServicioExtra::all();

        return response()->json([
            'inmu_servi_extra' => $inmu_servi_extra,
            'status' => 200,
        ], 200);
    }

    public function store_inmu_servi_extra(Request $request)
    {

        $request->validate([
            "inmueble_id" => "required|integer|exists:inmuebles,id_inmueble",
            "servicio_extra_id" => [
                'required',
                'integer',
                'exists:servicios_extras,id_servicio_extra',
                Rule::unique('inmuebles_servicios_extras', 'id_servicio_extra')->where(function ($query) use ($request) {
                    return $query->where('id_inmueble', $request->inmueble_id);
                })

            ],
        ]);

        $inmu_servi_extra = InmuServicioExtra::create([
            "id_inmueble" => $request->inmueble_id,
            "id_servicio_extra" => $request->servicio_extra_id,
        ]);

        if (!$inmu_servi_extra) {
            $data = [
                "status" => 500,
                "message" => "El servicio extra no se ha creado correctamente"
            ];
            return response()->json($data, 500);
        }

        $data = [
            "status" => 201,
            "data" => $inmu_servi_extra,
            "message" => "Servicio extra creado correctamente"
        ];
        return response()->json($data, 201);
    }

    public function show_inmu_servi_extra($id)
    {

        $inmu_servi_extra = InmuServicioExtra::find($id);
        if (!$inmu_servi_extra) {
            $data = [
                "status" => 404,
                "message" => "El servicio extra no existe"
            ];
            return response()->json($data, 404);
        }
        $data = [
            "status" => 200,
            "data" => $inmu_servi_extra,
            "message" => "Servicio extra encontrado"
        ];
        return response()->json($data, 200);
    }

    public function destroy_inmu_servi_extra($id)
    {

        $inmu_servi_extra = InmuServicioExtra::find($id);

        if (!$inmu_servi_extra) {
            $data = [
                "status" => 404,
                "message" => "El servicio extra no existe"
            ];
            return response()->json($data, 404);
        }

        $inmu_servi_extra->delete();
        $data = [
            "status" => 200,
            "message" => "Servicio extra eliminado correctamente"
        ];
        return response()->json($data, 200);
    }

    public function update_inmu_servi_extra(Request $request, $id)
    {

        $inmu_servi_extra = InmuServicioExtra::find($id);

        if (!$inmu_servi_extra) {
            $data = [
                "status" => 404,
                "message" => "El servicio extra no existe"
            ];
            return response()->json($data, 404);
        }

        $request->validate([
            "inmueble_id" => "required|integer|exists:inmuebles,id_inmueble",
            "servicio_extra_id" => [
                'required',
                'integer',
                'exists:servicios_extras,id_servicio_extra',
                Rule::unique('inmuebles_servicios_extras', 'id_servicio_extra')->where(function ($query) use ($request) {
                    return $query->where('id_inmueble', $request->inmueble_id);
                })

            ],
        ]);
        $inmu_servi_extra->id_inmueble = $request->inmueble_id;
        $inmu_servi_extra->id_servicio_extra = $request->servicio_extra_id;

        $inmu_servi_extra->save();

        $data = [
            "status" => 200,
            "data" => $inmu_servi_extra,
            "message" => "Servicio extra actualizado correctamente"
        ];
        return response()->json($data, 200);
    }

    /**
     * Crud servicios
     */

    public function servicios()
    {
        $servicio = Servicio::all();

        return response()->json([
            'servicios' => $servicio,
            'status' => 200,
        ], 200);
    }
    public function store_servicios(Request $request)
    {
        $request->validate([
            "descripcion" => "required|string",

        ]);

        $servicio = Servicio::create([
            "descripcion" => $request->descripcion,
        ]);

        if (!$servicio) {
            $data = [
                "status" => 500,
                "message" => "El servicio no se ha creado correctamente"
            ];
            return response()->json($data, 500);
        }

        $data = [
            "status" => 201,
            "data" => $servicio,
            "message" => "Servicio creado correctamente"
        ];

        return response()->json($data, 201);
    }
    public function show_servicio($id)
    {
        $servicio = Servicio::find($id);
        if (!$servicio) {
            $data = [
                "status" => 404,
                "message" => "El servicio no existe"
            ];
            return response()->json($data, 404);
        }
        $data = [
            "status" => 200,
            "data" => $servicio,
            "message" => "Servicio encontrado"
        ];
        return response()->json($data, 200);
    }
    public function destroy_servicio($id)
    {
        $servicio = Servicio::with('inmueblesServicios')->find($id);

        if (!$servicio) {
            $data = [
                "status" => 404,
                "message" => "El servicio extra no existe"
            ];
            return response()->json($data, 404);
        }
        if ($servicio->inmueblesServicios()->count() > 0) {
            return response()->json([
                "status" => 400,
                "message" => "El servicio no puede ser eliminado porque está en uso."
            ], 400);
        }

        $servicio->delete();
        $data = [
            "status" => 200,
            "message" => "Servicio extra eliminado correctamente"
        ];
        return response()->json($data, 200);
    }
    public function update_servicio(Request $request, $id)
    {
        $servicio = Servicio::find($id);

        if (!$servicio) {
            $data = [
                "status" => 404,
                "message" => "El servicio no existe"
            ];
            return response()->json($data, 404);
        }

        $request->validate([
            "descripcion" => "required|string",

        ]);

        $servicio->descripcion = $request->descripcion;
        $servicio->save();

        $data = [
            "status" => 200,
            "data" => $servicio,
            "message" => "Servicio actualizado correctamente"
        ];
        return response()->json($data, 200);
    }



    /**
     * Crud inmuebles servicios
     */

    public function inmu_servicios()
    {
        $inmu_servi = InmuServicio::all();

        return response()->json([
            'inmu_servi' => $inmu_servi,
            'status' => 200,
        ], 200);
    }
    public function store_inmu_servicios(Request $request)
    {

        $request->validate([
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

        $inmu_servi = InmuServicio::create([
            "id_inmueble" => $request->inmueble_id,
            "id_tipo_servicio" => $request->servicio_id,
        ]);

        if (!$inmu_servi) {
            $data = [
                "status" => 500,
                "message" => "El servicio no se ha creado correctamente"
            ];
            return response()->json($data, 500);
        }

        $data = [
            "status" => 201,
            "data" => $inmu_servi,
            "message" => "Servicio creado correctamente"
        ];
        return response()->json($data, 201);
    }
    public function show_inmu_servicio($id)
    {
        $inmu_servi = InmuServicio::find($id);
        if (!$inmu_servi) {
            $data = [
                "status" => 404,
                "message" => "El servicio no existe"
            ];
            return response()->json($data, 404);
        }
        $data = [
            "status" => 200,
            "data" => $inmu_servi,
            "message" => "Servicio encontrado"
        ];
        return response()->json($data, 200);
    }
    public function destroy_inmu_servicios($id)
    {

        $inmu_servi = InmuServicio::find($id);

        if (!$inmu_servi) {
            $data = [
                "status" => 404,
                "message" => "El servicio no existe"
            ];
            return response()->json($data, 404);
        }

        $inmu_servi->delete();
        $data = [
            "status" => 200,
            "message" => "Servicio eliminado correctamente"
        ];
        return response()->json($data, 200);
    }
    public function update_inmu_servicios(Request $request, $id)
    {
        $inmu_servi = InmuServicio::find($id);

        if (!$inmu_servi) {
            $data = [
                "status" => 404,
                "message" => "El servicio no existe"
            ];
            return response()->json($data, 404);
        }

        $request->validate([
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

        $inmu_servi->id_inmueble = $request->inmueble_id;
        $inmu_servi->id_tipo_servicio = $request->servicio_id;

        $inmu_servi->save();

        $data = [
            "status" => 200,
            "data" => $inmu_servi,
            "message" => "Servicio actualizado correctamente"
        ];
        return response()->json($data, 200);
    }

    /**
     * Crud inmuebles
     */

    public function inmuebles()
    {
        $user = auth()->user();

        if (!$user || $user->admin != 1) {
            $inmuebles = Inmueble::with(['servicios_ex.servicio_ex', 'servicios.servicio', 'fotos', 'genero', 'usuario'])->where('estado', 1)->orderByDesc('created_at')->get();
        }else{
            $inmuebles = Inmueble::with(['servicios_ex.servicio_ex', 'servicios.servicio', 'fotos', 'genero', 'usuario'])->orderByDesc('created_at')->get();
        }

        return response()->json([
            'inmuebles' => $inmuebles,
            'status' => 200,
        ], 200);
    }

    public function destacados_Inmuebles(){
        #inmuebles destacados get
        $inmuebles = Inmueble::with(['servicios_ex.servicio_ex', 'servicios.servicio', 'fotos', 'genero', 'usuario'])->where('destacado', 1)->where('estado', 1)->orderByDesc('created_at')->get();

        return response()->json([
            'inmuebles_destacados' => $inmuebles,
            'status' => 200,
        ], 200);
    }

    public function store_inmuebles(Request $request)
    {

        $request->validate([
            "codigo" => "required|string|max:20",
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
            "id_genero" => "required|integer|exists:genero,id_genero",
            "descripcion" => "required|string",
            "destacado" => "sometimes|integer|between:0,1",
            "link" => "required|string|url|max:255",
            "estado" => "sometimes|integer|between:0,1"

        ]);

        $inmueble = Inmueble::create([
            "codigo" => $request->codigo,
            "nombre" => $request->nombre,
            "direccion" => $request->direccion,
            "pais" => $request->pais,
            "region" => $request->region,
            "ciudad" => $request->ciudad,
            "medida" => $request->medida,
            "precio" => $request->precio,
            "porcentaje_descuento" => $request->get('porcentaje_descuento', 0),
            "precio_descuento" => $request->get('precio_descuento', 0),
            "habitaciones" => $request->habitaciones,
            "id_usuario" => auth()->user()->id,
            "id_genero" => $request->id_genero,
            "destacado" => $request->get('destacado', 0),
            "link" => $request->link,
            "estado" => $request->get('estado', 0),
            "descripcion" => $request->descripcion,
        ]);

        if (!$inmueble) {
            $data = [
                "status" => 500,
                "message" => "El inmueble no se ha creado correctamente"
            ];
            return response()->json($data, 500);
        }
        $data = [
            "status" => 201,
            "data" => $inmueble,
            "message" => "Inmueble creado correctamente"
        ];
        return response()->json($data, 201);
    }

    public function show_inmueble_s($id)
    {

        $inmueble = Inmueble::with(['servicios_ex.servicio_ex', 'servicios.servicio', 'fotos', 'genero', 'usuario'])->find($id);

        if (!$inmueble) {
            $data = [
                "status" => 404,
                "message" => "El inmueble no existe e"
            ];
            return response()->json($data, 404);
        }

        $data = [
            "status" => 200,
            "inmueble" => $inmueble,
            "message" => "Inmueble encontrado"
        ];
        return response()->json($data, 200);
    }

    public function user_show_inmueble()
    {

        $userId = auth()->user()->id;


        $inmuebles = Inmueble::with(['servicios_ex.servicio_ex', 'servicios.servicio', 'fotos', 'genero', 'usuario'])
            ->where('id_usuario', $userId)
            ->orderByDesc('created_at')
            ->get();

        if ($inmuebles->isEmpty()) {
            return response()->json([
                "status" => 404,
                "message" => "No se encontraron inmuebles para este usuario"
            ], 404);
        }

        return response()->json([
            "status" => 200,
            "inmuebles" => $inmuebles,
            "message" => "Inmuebles encontrados"
        ], 200);
    }

    public function destroy_inmueble($id)
    {
        $inmueble = Inmueble::find($id);

        if (!$inmueble) {
            $data = [
                "status" => 404,
                "message" => "El inmueble no existe"
            ];
            return response()->json($data, 404);
        }

        $fotos = Foto::where('id_inmueble', $id)->get();
        foreach ($fotos as $foto) {

            $path = parse_url($foto->url, PHP_URL_PATH);
            $storagePath = substr($path, strlen('/storage/'));

            if (Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
            }
            $foto->delete();
        }
        $inmu_servi_extra = InmuServicioExtra::where('id_inmueble', $id)->get();
        foreach ($inmu_servi_extra as $servicio) {
            $servicio->delete();
        }

        $inmu_servi = InmuServicio::where('id_inmueble', $id)->get();
        foreach ($inmu_servi as $servicio) {
            $servicio->delete();
        }

        $inmueble->delete();
        $data = [
            "status" => 200,
            "message" => "Inmueble eliminado correctamente"
        ];
        return response()->json($data, 200);
    }

    public function update_inmueble(Request $request, $id)
    {

        $inmueble = Inmueble::find($id);

        if (!$inmueble) {
            $data = [
                "status" => 404,
                "message" => "El inmueble no existe"
            ];
            return response()->json($data, 404);
        }

        $request->validate([
            "codigo" => "required|string|max:20",
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
            "id_genero" => "required|integer|exists:genero,id_genero",
            "descripcion" => "required|string",
            "destacado" => "sometimes|integer|between:0,1",
            "link" => "required|string|url|max:255",
            "estado" => "sometimes|integer|between:0,1",

        ]);

        $inmueble->codigo = $request->codigo;
        $inmueble->nombre = $request->nombre;
        $inmueble->direccion = $request->direccion;
        $inmueble->pais = $request->pais;
        $inmueble->region = $request->region;
        $inmueble->ciudad = $request->ciudad;
        $inmueble->medida = $request->medida;
        $inmueble->precio = $request->precio;
        $inmueble->porcentaje_descuento = $request->get('porcentaje_descuento', 0);
        $inmueble->precio_descuento = $request->get('precio_descuento', 0);
        $inmueble->habitaciones = $request->habitaciones;
        $inmueble->id_genero = $request->id_genero;
        $inmueble->descripcion = $request->descripcion;
        $inmueble->destacado = $request->get('destacado', 0);
        $inmueble->link = $request->link;
        $inmueble->estado = $request->get('estado', 0);
        $inmueble->save();

        $data = [
            "status" => 200,
            "data" => $inmueble,
            "message" => "Inmueble actualizado correctamente"
        ];
        return response()->json($data, 200);
    }

    public function update_patch_inmueble(Request $request, $id)
    {

        $inmueble = Inmueble::find($id);

        if (!$inmueble) {
            $data = [
                "status" => 404,
                "message" => "El inmueble no existe"
            ];
            return response()->json($data, 404);
        }

        $request->validate([
            "codigo" => "string|max:20",
            "nombre" => "string",
            "direccion" => "string",
            "pais" => "string",
            "region" => "string",
            "ciudad" => "string",
            "medida" => "string|max:10",
            "precio" => "numeric|between:0,999999999.99",
            "porcentaje_descuento" => "numeric|between:0,100",
            "precio_descuento" => "numeric|between:0,999999999.99",
            "habitaciones" => "integer",
            "id_genero" => "integer|exists:genero,id_genero",
            "descripcion" => "string",
            "destacado" => "integer|between:0,1"

        ]);

        if ($request->has('codigo')) {
            $inmueble->codigo = $request->codigo;
        }
        if ($request->has('nombre')) {
            $inmueble->nombre = $request->nombre;
        }
        if ($request->has('direccion')) {
            $inmueble->direccion = $request->direccion;
        }
        if ($request->has('pais')) {
            $inmueble->pais = $request->pais;
        }
        if ($request->has('region')) {
            $inmueble->region = $request->region;
        }
        if ($request->has('ciudad')) {
            $inmueble->ciudad = $request->ciudad;
        }
        if ($request->has('medida')) {
            $inmueble->medida = $request->medida;
        }
        if ($request->has('precio')) {
            $inmueble->precio = $request->precio;
        }
        if ($request->has('porcentaje_descuento')) {
            $inmueble->porcentaje_descuento = $request->get('porcentaje_descuento', 0);
        }
        if ($request->has('destacado')) {
            $inmueble->destacado = $request->get('destacado', 0);
        }

        if ($request->has('precio_descuento')) {
            $inmueble->precio_descuento = $request->get('precio_descuento', 0);
        }
        if ($request->has('habitaciones')) {
            $inmueble->habitaciones = $request->habitaciones;
        }
        if ($request->has('id_genero')) {
            $inmueble->id_genero = $request->id_genero;
        }
        if ($request->has('descripcion')) {
            $inmueble->descripcion = $request->descripcion;
        }

        // Verificar si hay cambios y guardar
        if ($inmueble->isDirty()) {
            $inmueble->save();
            return response()->json([
                "status" => 200,
                "data" => $inmueble,
                "message" => "Inmueble actualizado correctamente"
            ], 200);
        }

        return response()->json([
            "status" => 200,
            "message" => "No hubo cambios en el inmueble"
        ], 200);
    }

    public function update_status(Request $request, $id)
    {

        $inmueble = Inmueble::find($id);

        if (!$inmueble) {
            $data = [
                "status" => 404,
                "message" => "El inmueble no existe"
            ];
            return response()->json($data, 404);
        }

        $request->validate([
            "estado" => "required|in:0,1"
        ]);

        $inmueble->estado = $request->estado;
        $inmueble->save();
        return response()->json([
            "status" => 200,
            "data" => $inmueble,
            "message" => "Estado del inmueble actualizado correctamente"
        ], 200);
    }


    /**
     * Crud fotos
     *
     */
    public function fotos()
    {
    }
    public function store_fotos(Request $request)
    {

        $request->validate([
            "inmueble_id" => "required|integer|exists:inmuebles,id_inmueble",
            "fotos" => "required|array",
            "fotos.*" => "image|mimes:jpeg,png,jpg|dimensions:width=1080,height=1080|max:4096",
            "destacado" => "integer|between:0,1"
        ]);

        /* foreach ($request->file('fotos') as $foto) {
            $folderPath = 'public/inmuebles/' . $request->inmueble_id;
            $path = $foto->store($folderPath);

            $url = Storage::url($path);
            Foto::create([
                'id_inmueble' => $request->inmueble_id,
                'url' => $url
            ]);
        } */
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

        return response()->json([
            "status" => 201,
            "message" => "Fotos guardadas correctamente"
        ]);
    }
    public function show_fotos($id)
    {

        $inmueble = Inmueble::find($id);

        if (!$inmueble) {
            return response()->json([
                'message' => 'El inmueble no existe',
                'status' => 404
            ], 404);
        }

        $fotos = Foto::where('id_inmueble', $id)->get();

        return response()->json([
            'fotos' => $fotos,
            'status' => 200,
        ], 200);
    }
    public function destroy_fotos($id)
    {

        $foto = Foto::find($id);
        if (!$foto) {
            $data = [
                "status" => 404,
                "message" => "La foto no existe"
            ];
            return response()->json($data, 404);
        }

        // Extraer el path del archivo desde la URL
        $path = parse_url($foto->url, PHP_URL_PATH);

        // Remover el prefijo '/storage/' para obtener el path relativo en el sistema de archivos
        $storagePath = substr($path, strlen('/storage/'));

        // Eliminar la foto del almacenamiento
        if (Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->delete($storagePath);
        }

        $foto->delete();

        return response()->json([
            "status" => 200,
            "message" => "Foto eliminada correctamente"
        ], 200);
    }
    public function update_fotos()
    {
    }


    /**
     * Crud noticias
     *
     */

    public function noticias()
    {
        $noti = Noticia::with(['usuario'])->orderBy('created_at', 'desc')->get();

        return response()->json([
            'noticias' => $noti,
            'status' => 200,
        ], 200);
    }
    public function store_noticias(Request $request)
    {

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'etiqueta' => 'required|string|max:50',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:4096',
            'link' => 'string|url|max:255',
        ]);

        try {
            // Guardar la foto
            $folderPath = 'public/noticias';
            $path = $request->file('foto')->store($folderPath);
            //$url = Storage::disk('public')->url($path);
            $url = Storage::url($path);
            // Crear la noticia
            $noticia = Noticia::create([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'etiqueta' => $request->etiqueta,
                'url_foto' => $url,
                'link' => $request->link,
                'id_user' => auth()->user()->id,
            ]);

            return response()->json([
                'message' => 'Noticia creada exitosamente',
                'noticia' => $noticia,
                'status' => 201,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la noticia',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
    public function show_noticia($id)
    {
        $noti = Noticia::with(['usuario'])->find($id);
        if (!$noti) {
            $data = [
                "status" => 404,
                "message" => "La noticia no existe",
            ];
            return response()->json($data, 404);
        }
        $data = [
            "status" => 200,
            "data" => $noti,
            "message" => "Noticia encontrada"
        ];
        return response()->json($data, 200);
    }
    public function destroy_noticia($id)
    {
        $noti = Noticia::find($id);
        if (!$noti) {
            $data = [
                "status" => 404,
                "message" => "La noticia no existe"
            ];
            return response()->json($data, 404);
        }
        try {
            // Extraer el path del archivo desde la URL
            $path = parse_url($noti->url_foto, PHP_URL_PATH);

            // Remover el prefijo '/storage/' para obtener el path relativo en el sistema de archivos
            $storagePath = substr($path, strlen('/storage/'));

            // Eliminar la foto del almacenamiento
            if (Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
            }

            $noti->delete();

            return response()->json([
                "status" => 200,
                "message" => "Noticia eliminada correctamente"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la noticia',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
    public function update_noticia_ft(Request $request, $id)
    {
        $noti = Noticia::find($id);
        if (!$noti) {
            $data = [
                "status" => 404,
                "message" => "La noticia no existe",
            ];
            return response()->json($data, 404);
        }
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:4096'

        ]);

        try {

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
            return response()->json([
                'message' => 'imagen actualizada exitosamente',
                'noticia' => $noti,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la noticia',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
    public function update_noticia(Request $request, $id)
    {
        $noti = Noticia::find($id);
        if (!$noti) {
            $data = [
                "status" => 404,
                "message" => "La noticia no existe",
            ];
            return response()->json($data, 404);
        }

        $request->validate([
            'titulo' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'etiqueta' => 'nullable|string|max:50',
            'link' => 'nullable|string|url|max:255',
        ]);

        try {

            if ($request->has('titulo')) {
                $noti->titulo = $request->titulo;
            }
            if ($request->has('descripcion')) {
                $noti->descripcion = $request->descripcion;
            }
            if ($request->has('etiqueta')) {
                $noti->etiqueta = $request->etiqueta;
            }

            if ($request->has('link')) {
                $noti->link = $request->link;
            }
            if ($noti->isDirty()) {

                $noti->save();
                return response()->json([
                    'message' => 'Noticia actualizada exitosamente',
                    'noticia' => $noti,
                    'status' => 200,
                ], 200);
            }
            return response()->json([
                "status" => 200,
                "message" => "No hubo cambios en la noticia"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la noticia',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


    /**
     * filtros de inmuebles
     *
     */

    public function searchInmuebles()
    {
        $request->validate([
            "search" => "required|string",

        ]);

        $query = Inmueble::query();


        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->where('nombre', 'like', '%' . $search . '%')
                    ->orWhere('codigo', 'like', '%' . $search . '%');
            });
        }
        $inmuebles = $query->with(['servicios_ex.servicio_ex', 'servicios.servicio', 'fotos', 'genero', 'usuario'])->where('destacado', 1)->where('estado', 1)->orderByDesc('created_at')->get();



        // $inmuebles = $query->get();
        // $allData= Inmueble::with(['servicios_ex.servicio_ex', 'servicios.servicio', 'fotos', 'genero', 'usuario'])->find($inmuebles["id"]);

        $smg = (empty($inmuebles)) ? 'No se encontraron resultados, prueba con otro término' : 'Se encontraron resultados';

        return response()->json([
            'message' => $smg,
            'inmuebles' => $inmuebles,
            'status' => 200,
        ], 200);
    }


}
