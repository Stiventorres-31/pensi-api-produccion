<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
//JWT Authentication
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Clientes extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'clientes';
    

    protected $fillable = [
        'tipo_identidad',
        'numero_identidad',
        'nombres',
        'email',
        'password',
        'estado'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',

    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */

     public function getJWTIdentifier(){
        return $this->getKey();
    }


    /**
     * Return a key value array, containing any custom claims to be added to the JWT
     *
     * @return array
     */
    public function getJWTCustomClaims(){
        return [];
    }
}
