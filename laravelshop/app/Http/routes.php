<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
use Illuminate\Http\Request;

//Route::get('/', function () {
//      echo 'работает';
//});
//Route::get('/', function () {
//      return view('welcome');
//});

Route::auth();

//Route::get('/home', 'HomeController@index');

Route::get('/', 'HomeController@index');


Route::group(['middleware' => ['auth','admin']], function () {

    Route::get('/manager/','ControllerManager@index');
    Route::get('/manager/site','ControllerManager@site');
    Route::get('/manager/model/{action}',function($action){
        $controllerName='App\Http\Controllers\ControllerManager';
        $controller = new $controllerName;
        return $controller->model($action);
    });

});

//Route::get('/closes', 'ControllerCloses@index');
Route::get('/{model}', function($model) {
    if (!in_array($model, ['manager'])){
        $className = 'App\Http\Controllers\Controller' . ucfirst($model);
        $controller = new $className;
        return $controller->index();
    }
});

Route::get('/{model}/filter', function($model,Request $request) {
        $className = 'App\Http\Controllers\Controller' . ucfirst($model);
        $controller = new $className;
        return $controller->filter($request);
});

Route::get('/{model}/{id}', function($model,$id) {
    $className = 'App\Http\Controllers\Controller' . ucfirst($model);
    $controller = new $className;
    return $controller->single($id);
});



Route::group(['middleware' => ['auth','admin']], function () {



    Route::get('/{model}/{action}', function($model,$action = 'index') {
        $model = ucfirst($model);
        $action =strtolower($action);
        if (in_array($action,['sample','add'])) {
            $className = 'App\Http\Controllers\Controller' . $model;
            $controller = new $className;
            return $controller->$action();
        }
    });

    Route::get('/{model}/{action}/{id}', function($model,$action = 'index',$id=0) {
        $model = ucfirst($model);
        $action =strtolower($action);
        if (in_array($action,['edit','delete'])) {
            $className = 'App\Http\Controllers\Controller' . $model;
            $controller = new $className;
            return $controller->$action($id);
        }
    });
    Route::post('/{model}/{action}', function($model,$action = 'index',Request $request) {
        $model = ucfirst($model);
        $action =strtolower($action);
        if (in_array($action,['save'])) {
            $className = 'App\Http\Controllers\Controller' . $model;
            $controller = new $className;
            return $controller->$action($request);
        }
    });

});



