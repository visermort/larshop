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
        $data= array(
            'title' => 'Магазин - Админ. панель ',
            'pageTitle' => 'Магазин - административаная панель. Модель '.$model
        );
        return view('pages.manager',$data);
    }

}
