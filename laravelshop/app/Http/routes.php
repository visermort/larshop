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


Route::auth();

Route::get('/', 'HomeController@index');

Route::post('/mail',function(Request $request){
    $className = 'App\Http\Controllers\HomeController';
    $controller = new $className;
    return $controller->sendMail($request);
});

Route::group(['middleware' => ['auth','admin']], function () {
//Route::group(['middleware' => ['admin']], function () {

    Route::get('/manager/','ControllerManager@index');
    Route::get('/manager/site','ControllerManager@index');
    Route::post('/manager/site/save','ControllerManager@save');

    Route::post('/manager/dict/add','ControllerManager@dictAdd');
    Route::post('/manager/dict/del','ControllerManager@dictDel');

    Route::get('/manager/model/{modelName}',function($modelName){
        $controllerName='App\Http\Controllers\ControllerManager';
        $controller = new $controllerName;
        return $controller->model($modelName);
    });
    Route::get('/manager/{modelName}/{action}',function($modelName,$action){
        if ($modelName !='site' && $modelName !='model') {
            $controllerName = 'App\Http\Controllers\Controller' . ucFirst($modelName);
            $action = strtolower($action);
            if (!class_exists($controllerName) || !is_callable([$controllerName,$action])) {
                abort(404);
            }
            $controller = new $controllerName;
            return $controller->$action();
        }
    });
    Route::post('manager/{model}/{action}', function($model,$action = 'index',Request $request) {
        $model = ucfirst($model);
        $action =strtolower($action);
        if (in_array($action,['save'])) {
            $className = 'App\Http\Controllers\Controller' . $model;
            $controller = new $className;
            return $controller->$action($request);
        }
    });
    Route::get('manager/{model}/{action}/{id}', function($model,$action = 'index',$id=0) {
        $id = (int) $id;
        if ($model !='site' && $model !='model' && $id>0) {
            $model = ucfirst($model);
            $action = strtolower($action);
            if (in_array($action, ['edit', 'delete'])) {
                $className = 'App\Http\Controllers\Controller' . $model;
                $controller = new $className;
                return $controller->$action($id);
            }
        }
    });

});



//Route::get('/closes', 'ControllerCloses@index');
Route::get('/{model}', function($model) {
    if (!in_array($model, ['manager'])){
        $className = 'App\Http\Controllers\Controller' . ucfirst($model);
        if (!class_exists($className)){
            abort(404);
        }
        $controller = new $className;
        return $controller->index();
    }
});

Route::get('/{model}/filter', function($model,Request $request) {
        $className = 'App\Http\Controllers\Controller' . ucfirst($model);
        if (!class_exists($className)){
            abort(404);
        }
        $controller = new $className;
        return $controller->filter($request);
});

Route::get('/{model}/{id}', function($model,$id) {
    $id = (int) $id;
    if ($id > 0) {
        $className = 'App\Http\Controllers\Controller' . ucfirst($model);
        if (!class_exists($className)){
            abort(404);
        }
        $controller = new $className;
        return $controller->single($id);
    } else {
        abort(404);
    }
});





