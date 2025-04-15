<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Genero;
use App\Models\User;
use App\Models\Foto;
use App\Models\InmuServicioExtra;
use App\Models\InmuServicio;

class Inmueble extends Model
{
    use HasFactory;
    protected $table = 'inmuebles';
    protected $primaryKey = 'id_inmueble';
    protected $fillable = [
        'codigo',
        'nombre',
        'direccion',
        'pais',
        'region',
        'ciudad',
        'medida',
        'precio',
        'porcentaje_descuento',
        'precio_descuento',
        'habitaciones',
        'id_usuario',
        'id_genero',
        'destacado',
        'link',
        'estado',
        'descripcion',

    ];

    public function fotos()
    {
        return $this->hasMany(Foto::class, 'id_inmueble');
    }

    public function servicios_ex(){
        return $this->hasMany(InmuServicioExtra::class, 'id_inmueble');
    }

    public function servicios(){
        return $this->hasMany(InmuServicio::class, 'id_inmueble');
    }

    public function genero()
    {
        return $this->belongsTo(Genero::class, 'id_genero');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }






}
