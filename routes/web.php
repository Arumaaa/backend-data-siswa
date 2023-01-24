<?php

use App\Http\Middleware\UserMiddleware;
use Illuminate\Http\Request;
/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('api/logout', ['middleware' => 'auth','uses' => 'LoginController@logout']);


$router->post('api/register',['uses' => 'LoginController@register']);
$router->post('api/login',['uses' => 'LoginController@login']);

$router->group(['prefix'=>'api','middleware'=>'auth'] , function() use ($router){

$router->get('siswa',['uses' => 'SiswaController@index']);

$router->get('siswa/{id}',['uses' =>'SiswaController@show']);

$router->delete('siswa/delete/{id}',['uses' => 'SiswaController@destroy']);

$router->put('siswa/update/{id}',['uses' => 'SiswaController@update']);

$router->post('siswa/tambah',['uses' => 'SiswaController@create']);

});