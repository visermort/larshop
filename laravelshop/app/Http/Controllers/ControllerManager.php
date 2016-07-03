<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Config;


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
        $data= array(
            'model' => $model,
            'model_title' => config('shop.models')[$model]['title'],
            'title' => 'Магазин - Админ. панель ',
            'pageTitle' => 'Магазин - административаная панель. Товар. Категория '.config('shop.models')[$model]['title']
        );
        return view('pages.manager_model',$data);
    }

    //сохранение основных настроек сайта
    public function save(Request $request)
    {
        $this->validate($request,[
            'siteName' => 'required|string',
            'itemsOnPage' => 'integer',
            'adminEmail' => 'required|email'
        ]);

         //sitename
        $this->saveConfig('siteName',$request->siteName);
        //admin Email
        $this->saveConfig('adminEmail',$request->adminEmail);
        //items on page
        $this->saveConfig('itemsOnPage',$request->itemsOnPage);

        return back()->withInput();
    }

}
