<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->get('users','UsersController@index');
    $router->get('products','ProductsController@index');
    $router->get('products/create', 'ProductsController@create');
    $router->post('products', 'ProductsController@store');
    $router->get('orders','OrderController@index')->name('orders.index');
    $router->get('orders/{order}','OrderController@show')->name('orders.show');
    $router->post('orders/{order}/ship','OrderController@ship')->name('orders.ship');
});
