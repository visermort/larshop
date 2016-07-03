<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ControllerManager extends Controller
{
    //
    public function index()
    {
        //наполняем массив
        $data= array(
            'title' => 'Магазин - Админ. панель ',
            'pageTitle' => 'Магазин - административаная панель'
        );
        return view('pages.manager',$data);
    }
    public function site()
    {
        //наполняем массив
        $data= array(
            'title' => 'Магазин - Админ. панель ',
            'pageTitle' => 'Магазин - административаная панель. Сайт. Основные настройки'
        );
        return view('pages.manager',$data);
    }
    public function model($model)
    {
        //наполняем массив
     //   print_r(config('shop.models'));
        $data= array(
            'model' => $model,
            'model_title' => config('shop.models')[$model]['title'],
            'title' => 'Магазин - Админ. панель ',
            'pageTitle' => 'Магазин - административаная панель. Модель '.config('shop.models')[$model]['title']
        );
        return view('pages.manager_model',$data);
    }

}
