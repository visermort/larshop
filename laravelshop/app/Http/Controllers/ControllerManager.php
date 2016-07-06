<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Config;
use Validator;
use App\Models\Dict;


class ControllerManager extends Controller
{

    public function index()
    {
        $configs = Config::all();

        $data= array(
            'title' => 'Магазин - Админ. панель ',
            'pageTitle' => 'Магазин - административная панель. Сайт. Основные настройки'
        );
        foreach ($configs as $config) {
            $data[$config['id_config']] = $config['config'];
        }
       return view('pages.manager',$data);
    }
    public function model($model)
    {
        //обращаемся к соответствующему контроллеру, чтобы получить струкутуру - нужна для отображения словарей
        $controllerName = 'App\Http\Controllers\Controller'.ucfirst($model);
        $controller = new $controllerName;
        $structure = $controller->structure;
        $controller = null;
        $data= array(
            'model' => $model,
            'model_title' => config('shop.models')[$model]['title'],
            'title' => 'Магазин - Админ. панель ',
            'pageTitle' => 'Магазин - административная панель. Товар. Категория '.config('shop.models')[$model]['title'],
           // 'dictList' => $this->getDictLists($model), //набор словарей для данной модели
            'structure' => $structure
        );
        return view('pages.manager_model',$data);
    }

    //сохранение основных настроек сайта
    public function save(Request $request)
    {
        $this->validate($request, [
            'siteName' => 'required|string',
            'itemsOnPage' => 'integer',
            'adminEmail' => 'required|email'
        ]);

        //sitename
        $this->saveConfig('siteName', $request->siteName);
        //admin Email
        $this->saveConfig('adminEmail', $request->adminEmail);
        //items on page
        $this->saveConfig('itemsOnPage', $request->itemsOnPage);

        return back()->withInput();
    }

    //    удаление элемента словаря
    public function dictDel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $message = 'Ошибка - вы ввели неправильные данные';
            $status = false;
        } else {
            try {
                $dict = Dict::find($request->id);
                $dict -> forceDelete();
                $message = 'Удалено';
                $status = true;
            } catch (Exception $e) {
                $message = 'Ошибка при удалении '.$e->getMessage();
                $status = false;
            }
        }

        return json_encode([
            'status' => $status,
            'message' => $message
        ]);

    }
    //    добавление элемента словаря
    public function dictAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'table' => 'required',
            'field' => 'required',
            'value' => 'required'
        ]);
        if ($validator->fails()) {
            $message = 'Ошибка - вы ввели неправильные данные';
            $status = false;
            $id='';
        } else {
            try {
                $id = $this -> writeDict($request->table, $request->field, $request->value);
                if ($id !='' && is_int($id) ) {
                    $message = 'Вставка выполнена';
                    $status = true;
                } else {
                    $message = 'Ошибка при вставке1';
                    $status = false;
                }
            } catch (Exception $e) {
                $message = 'Ошибка при вставке2  '.$e->getMessage();
                $status = false;
                $id='';
            }
        }

        return json_encode([
            'status' => $status,
            'message' => $message,
            'id' => $id,
        ]);

    }
}
