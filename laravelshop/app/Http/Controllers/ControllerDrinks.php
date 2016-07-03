<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Drinks;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Auth;
use App\Models\Images;



class ControllerDrinks extends Controller
{

    //отображeние списком
    public function index()
    {
        $closesData = Drinks::paginate($this->getConfig('itemsOnPage'));
        //обрабатываем словари и изображения
        $viewData=[];
//        dd($closesData);
        foreach ($closesData as $item){
            $viewData[] = $this->copyData($item);
        }
        //наполняем массив
        $data= array(
            'title' => 'Магазин - Напитки',
            'pageTitle' => 'Напитки',
            'viewData' => $viewData,
            'modelData' => $closesData,
            'count' => Drinks::count(),
            'structure' => $this->structure,
            'table' => 'drinks',
            'admin' => (Auth::check() && Auth::user()['attributes']['email'] == config('shop.adminEmail'))//являетcя ли пользователь админом
        );
        return view('pages.list',$data);

    }
    //удаление записи
    public function delete($id)
    {
        //eщё раз проверяем авторизацию пользователя
        if (Auth::check() && Auth::user()['attributes']['email'] == config('shop.adminEmail')) {
            $closes = Drinks::find($id);
            if (isset($closes['attributes']['image']) && $closes['attributes']['image']) {
                Images::find($closes['attributes']['image'])->delete();
            }
            $closes -> delete();
            return back();
        }
    }

    //    фильтрация
    public function filter($request)
    {
        $this->validate($request, [
            'manufacturer' => 'integer',
            'price_from' => 'numeric|min:0',
            'price_to' => 'numeric|min:0',
            'count' => 'integer|min:0',
            'country' => 'integer',
            'volume' => 'integer',
            'alcoghol' => 'string',
            'stage' => 'integer',
            'category' => 'integer',
        ]);
        $sql='1=1 ';
        $sqlArr=[];
        $filterArr=[];
        if ($request->input('name')) {
            $sql .=' and name like ?';
            $sqlArr []= '%'.$request->input('name').'%';
            $filterArr ['name'] = $request->input('name');
        }
        if ($request->input('manufacturer')) {
            $sql .=' and manufacturer = ?';
            $sqlArr []= $request->input('manufacturer');
            $filterArr ['manufacturer'] = $request->input('manufacturer');
        }
        if ($request->input('price_from')) {
            $sql .=' and price >= ?';
            $sqlArr []= $request->input('price_from');
            $filterArr ['price_from']= $request->input('price_from');
        }
        if ($request->input('price_to')) {
            $sql .=' and price <= ?';
            $sqlArr []= $request->input('price_to');
            $filterArr ['price_to']= $request->input('price_to');
        }
        if ($request->input('count')) {
            $sql .=' and count = ?';
            $sqlArr []= $request->input('count');
            $filterArr ['count']= $request->input('count');
        }
        if ($request->input('country')) {
            $sql .=' and country = ?';
            $sqlArr []= $request->input('country');
            $filterArr ['country']= $request->input('country');
        }
        if ($request->input('volume')) {
            $sql .=' and volume = ?';
            $sqlArr []= $request->input('volume');
            $filterArr ['size']= $request->input('volume');
        }
        if ($request->input('alcoghol')) {
            $sql .=' and alcoghol = ?';
            $sqlArr []= $request->input('alcoghol');
            $filterArr ['season']= $request->input('alcoghol');
        }
        if ($request->input('stage')) {
            $sql .=' and stage = ?';
            $sqlArr []= $request->input('stage');
            $filterArr ['sex']= $request->input('stage');
        }
        if ($request->input('category')) {
            $sql .=' and category = ?';
            $sqlArr []= $request->input('category');
            $filterArr ['category']= $request->input('category');
        }

        $closesData = Drinks::whereRaw($sql, $sqlArr) -> paginate($this->getConfig('itemsOnPage'));
        //обрабатываем словари и изображения
        $viewData=[];

        foreach ($closesData as $item){
            $viewData[] = $this->copyData($item);
        }
        //наполняем массив
        $data= array(
            'title' => 'Магазин - Напитки',
            'pageTitle' => 'Напитки - фильтр',
            'viewData' => $viewData,
            'modelData' => $closesData,
            'count' => Drinks::count(),
            'structure' => $this->structure,
            'table' => 'drinks',
            'filterData' => $filterArr, //параметры фильтра, чтобы вернуть в форму
            'admin' => (Auth::check() && Auth::user()['attributes']['email'] == config('shop.adminEmail'))//являетcя ли пользователь админом
        );
        return view('pages.list',$data);
    }
    //отображение одиночной записи
    public function single($id)
    {
        $closesData = Drinks::find($id);

        $viewData = $this->copyData($closesData);

        $data= array(
            'title' => 'Магазин - Напитки',
            'pageTitle' => 'Напитки - Карточка товара',
            'viewData' => $viewData,
            'modelData' => $closesData,
            'structure' => $this->structure,
            'table' => 'drinks',
            'productTitle' => 'Напитки',
            'id' => $id
        );
        return view('pages.product',$data);
    }

    //отображение формы редактирования
    public function edit($id)
    {
        $closesData = Drinks::find($id);
        //обрабатываем словари и изображения

        $viewData = $this->copyData($closesData);

        //наполняем массив
        $data= array(
            'title' => 'Магазин - Напитки',
            'pageTitle' => 'Напитки - Редактор. Запись '.$id,
            'viewData' => $viewData,
            'modelData' => $closesData,
            'structure' => $this->structure,
            'table' => 'drinks',
            'id' => $id
        );
        return view('pages.edit',$data);
    }
    //добавление записи
    public function add()
    {
        //новая запись
        //делаем пустой массив с данными
        $viewData = [];
        foreach ($this->structure as $key => $item){
            $viewData[$key] = '';
        }
        //наполняем массив
        $data= array(
            'title' => 'Магазин - Напитки',
            'pageTitle' => 'Напитки - Редактор. Новая запись',
            'viewData' => $viewData,//пустой массив
            'modelData' => [],//пустой массив
            'structure' => $this->structure,
            'table' => 'drinks',
            'id' => ''
        );
        return view('pages.edit',$data);
    }
    //сохранение результатов редактирования/добавления
    public function save($request)
    {
        $this->validate($request, [
            'id' => 'integer',
            'name' => 'required|max:255',
            'manufacturer' => 'integer',
            'price' => 'numeric|min:0',
            'count' => 'integer|required|min:0',
            'image' => 'image',
            'country' => 'integer',
            'volume' => 'integer',
            'alcoghol' => 'string',
            'stage' => 'integer',
            'category' => 'integer',
        ]);
//        echo 'save'.$request->input('id');
        try {

            $file = $request->file('image');
            if ($file) {
                //если имеется картинка, то вызываем метод для её сохранения
                $imageId = $this->writeImages($file);
            } else {
                $imageId=null;
            }
            if ($request->id) {
                //если имеется id, то обновление записи
                $closes = Drinks::find($request->id);
            }
            if (!$request->id || !isset($closes)|| !$closes)  {
                //иначе, или не нашли, тогда новая запись
                $closes = new Drinks();
            }

            $closes->name = $request->input('name');
            $closes->manufacturer = $request->input('manufacturer');
            $closes->price = $request->input('price');
            $closes->count = $request->input('count');
            $closes->description = $request->input('description');
            if ($imageId) {
                $closes->image = $imageId;
            }
            $closes->country = $request->input('country');
            $closes->volume = $request->input('volume');
            $closes->alcoghol = $request->input('alcoghol');
            $closes->stage = $request->input('stage');
            $closes->category = $request->input('category');
            // сохранение
            $closes->save();
            //редиректим на список
            return redirect('/drinks');
        } catch (Exception $e) {
            Log::error('Ошибка редактирования/добавления записи '.$e->getMessage());
            return back()->withInput();
        }
    }
    //наполнение тестовыми данными
    public function sample()
    {
        $faker = Faker::create();
        $manufacturers = ['Гленфиддик','Brown-Forman','Латвийский бальзам','Pernod Ricard'];
        $volume = ['0.3','0,25','0.5','0.75','1'];
        $stage = ['1','2','3','4','5','10'];
        $alco = ['0','9','12','13','19','30','40','45'];
        $category = ['Водки','Коньяки','Сухие','Полусухие','Полусладкие','Шампанское'];
        $country = ['Россия','Италия','Испания','Франция','Аргентина'];

        try {
            foreach (range(1, 20) as $index){
                $closes = new Drinks();
                $closes->name = $faker->word;
                $closes->manufacturer = $this->writeDict('drinks','manufacturer',$manufacturers[mt_rand(0,count($manufacturers)-1)]);
                $closes->description = $faker->text;
                $closes->price = $faker->randomFloat;
                $closes->count = $faker->randomDigit;
                $closes->image = $this->writeImagesFromFile($faker->image(public_path().'/'.config('shop.images'),800,600,'nightlife') );
                $closes->volume = $this->writeDict('drinks','volume',$volume[mt_rand(0,count($volume)-1)]);
                $closes->alcoghol = $this->writeDict('drinks','alcoghol',$alco[mt_rand(0,count($alco)-1)]);
                $closes->stage = $this->writeDict('drinks','stage',$stage[mt_rand(0,count($stage)-1)]);
                $closes->category = $this->writeDict('drinks','category',$category[mt_rand(0,count($category)-1)]);
                $closes->country = $this->writeDict('drinks','country',$country[mt_rand(0,count($country)-1)]);
                $closes->save();

            }
            return back() ;
        } catch (Exception $e) {
            Log::error('Ошибка записи '.$e->getMessage());
            return redirect('/home');
        }
    }

    public function __construct()
    {
        //для полей select наполняем возможные значения словаря - обязательные - для всех моделей
        $this->structure['manufacturer']['options'] = $this->getDictList('drinks','manufacturer');
        $this->structure['country']['options'] = $this->getDictList('drinks','country');
        //описываем дополнительные поля для модели, которых нет в базовом контроллере
        $this->structure['volume'] = ['title' => 'Объём','type' => 'select'];
        $this->structure['alcoghol'] = ['title' => 'Крепость','type' => 'select'];
        $this->structure['stage'] = ['title' => 'Выдержка','type' => 'select'];
        $this->structure['category'] = ['title' => 'Категория','type' => 'select'];
        //для уникальных полей select заполняем возможные значения
        $this->structure['volume']['options'] = $this->getDictList('drinks','volume');
        $this->structure['category']['options'] = $this->getDictList('drinks','category');
        $this->structure['stage']['options'] = $this->getDictList('drinks','stage');
        $this->structure['alcoghol']['options'] = $this->getDictList('drinks','alcoghol');
    }




}
