<?php

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


$router->group(['prefix' => 'v1'], function () use ($router) {
    $router->post('/login', ['uses' => 'UserController@login']);
    $router->post('/logout', ['uses' => 'UserController@logout']);
    $router->get('/get_user_list', ['uses' => 'UserController@getList']);
    $router->post('/update_user', ['uses' => 'UserController@update']);
    $router->get('/get_template_list', ['uses' => 'TemplateController@getList']);
    $router->post('/update_template', ['uses' => 'TemplateController@update']);
});
