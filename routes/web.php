<?php

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
/**
 * root api
 */

use App\Http\Controllers\IndexController;

$router->get('/', function () use ($router) {
    return response()->json([
        'code' => 200,
        'message' => 'OK'
    ]);
});
$router->get('/key', function () use ($router) {
    $key = 'base64:'.base64_encode(random_bytes(32));
    return response()->json(['key' => $key]);
});
/**
 * health check api
 */
$router->get('/health', function () use ($router) {
    return response()->json([
        'time' => date('Y-m-d H:i:s'),
        'version' => $router->app->version()
    ]);
});

/**
 * group api include business logic
 */
$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->post('/wagers', 'IndexController@createWager');
    $router->post('/buy/{wagerId}', 'IndexController@buyWager');
    $router->get('/wagers', 'IndexController@listWager');
});
