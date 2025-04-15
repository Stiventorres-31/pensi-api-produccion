<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FotoController;
use App\Http\Controllers\GeneroController;
use App\Http\Controllers\InmuebleController;
use App\Http\Controllers\InmuebleServicioController;
use App\Http\Controllers\InmuebleServicioExtraController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\ServicioExtraController;
use App\Http\Controllers\TipoServicioController;
use App\Http\Controllers\UserController;

/**
 * Open routes
 *
 */


Route::post("clients", [ClienteController::class, "store"]);

Route::post("login/user", [AuthController::class, "authUser"]);
Route::post("login/client", [AuthController::class, "authClient"]);

Route::get("generos", [GeneroController::class, "index"]);
Route::get("servicios/extra", [ServicioExtraController::class, "index"]);
Route::get("servicios", [TipoServicioController::class, "index"]);

Route::get("inmuebles", [InmuebleController::class, "index"]);
Route::get("inmuebles/{id}", [InmuebleController::class, "show"]);


Route::get("destacados/inmuebles", [InmuebleController::class, "get_destacado"]);

Route::post("buscar/inmuebles", [InmuebleController::class, "search"]);
Route::post("filtro/inmuebles", [InmuebleController::class, "filtro"]);


Route::get("noticias", [NoticiaController::class, "index"]);
Route::get("noticias/{id}", [NoticiaController::class, "show"]);



/**
 * Protected routes
 *
 */

Route::group(["middleware" => "auth:api"], function () {

    Route::post("users", [UserController::class, "store"])->middleware("admin");
    Route::get("users", [UserController::class, "index"]);
    Route::put("users/{id}", [UserController::class, "update"])->middleware("admin");
    Route::delete("users/{id}", [UserController::class, "destroy"])->middleware("admin");

    Route::get("clients", [ClienteController::class, "index"]);

    // Route::get("profile-user", [ApiController::class, "profile"]);
    // Route::get("refresh-token-user", [ApiController::class, "refreshToken"]);
    Route::post("logout-user", [AuthController::class, "logoutUser"]);

    //Rutas crud genero
    Route::post("generos", [GeneroController::class, "store"]);
    Route::get("generos/{id}", [GeneroController::class, "show"]);
    Route::put("generos/{id}", [GeneroController::class, "update"]);
    Route::delete("generos/{id}", [GeneroController::class, "destroy"]);

    //Rutas crud servicios extra
    Route::post("servicios/extra", [ServicioExtraController::class, "store"]);
    Route::get("servicios/extra/{id}", [ServicioExtraController::class, "show"]);
    Route::put("servicios/extra/{id}", [ServicioExtraController::class, "update"]);
    Route::delete("servicios/extra/{id}", [ServicioExtraController::class, "destroy"]);

    //Rutas crud servicios
    Route::post("servicios", [TipoServicioController::class, "store"]);
    Route::get("servicios/{id}", [TipoServicioController::class, "show"]);
    Route::put("servicios/{id}", [TipoServicioController::class, "update"]);
    Route::delete("servicios/{id}", [TipoServicioController::class, "destroy"]);

    //Rutas crud noticias
    //
    Route::post("noticias", [NoticiaController::class, "store"]);
    Route::patch("noticias/{id}", [NoticiaController::class, "update"]);
    Route::post("noticias-update-imagen/{id}", [NoticiaController::class, "image_update"]);
    Route::delete("noticias/{id}", [NoticiaController::class, "destroy"]);


    //Rutas crud servicios inmuebles
    Route::post("inmuebles/servicios", [InmuebleServicioController::class, "store"]);
    Route::delete("inmuebles/servicios/{id}", [InmuebleServicioController::class, "destroy"]);

    //Rutas crud servicios extra inmuebles
    Route::post("inmuebles/servicios/extra", [InmuebleServicioExtraController::class, "store"]);
    Route::delete("inmuebles/servicios/extra/{id}", [InmuebleServicioExtraController::class, "destroy"]);

    //Rutas crud inmuebles

    Route::post("inmuebles", [InmuebleController::class, "store"]);
    Route::get("user/inmuebles", [InmuebleController::class, "index_user"]);
    Route::put("inmuebles/{id}", [InmuebleController::class, "update"]);
    //Route::put("inmuebles/status/{id}", [InmuebleController::class, "update_show"]);
    Route::delete("inmuebles/{id}", [InmuebleController::class, "destroy"]);

    //Crud imagenes inmuebles
    Route::post("upload/imagenes", [FotoController::class, "store"]);
    Route::delete("imagenes/{id}", [FotoController::class, "destroy"]);
});



// Rutas protegidas para clientes
Route::group(["middleware" => "auth:client-api"], function () {
    // Route::get("profile-client", [ApiController::class, "profile"]);
    // Route::get("refresh-token-client", [ApiController::class, "refreshToken"]);
    Route::post("logout-client", [AuthController::class, "logoutClient"]);
});
