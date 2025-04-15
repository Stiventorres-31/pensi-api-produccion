<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foto extends Model
{
    use HasFactory;


    protected $table = 'fotos';
    protected $primaryKey = 'id_foto';
    protected $fillable = [
        'id_inmueble',
        'url',
        'destacado',
    ];

    public function inmuebles(){
        return $this->belongsTo(Inmueble::class,'id_inmueble');
    }
}
