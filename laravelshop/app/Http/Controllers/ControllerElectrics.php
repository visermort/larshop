<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Electrics;
use Faker\Factory as Faker;
use App\Models\Images;

class ControllerElectrics extends Controller
{
    private $title = 'Электротовары';
    private $table = 'electrics';

    //отображeние списком
    public function index()
    {
        $closesData = Electrics::paginate($this->getConfig('itemsOnPage'));
        //обрабатываем словари и изображения
        $viewData=[];
        foreach ($closesData as $item){
            $viewData[] = $this->copyData($item);
        }
        //наполняем массив
      //  print_r($this->structure);
        $data= array(
            'title' => 'Магазин - '.$this->title,
            'pageTitle' => $this->title,
            'viewData' => $viewData,
            'modelData' => $closesData,
            'count' => Electrics::count(),
            'structure' => $this->structure,
            'table' => $this->table,
            'admin' => $this->isAdmin()//являетcя ли пользователь админом
        );
        return view('pages.list',$data);

    }
    //удаление записи
    public function delete($id)
    {
        //eщё раз проверяем авторизацию пользователя
        if (Auth::check() && Auth::user()['attributes']['email'] == config('shop.adminEmail')) {
            $closes = Electrics::find($id);
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
            'category' => 'integer',
            'power'=>'ingeger',
            'voltage'=>'ingeger',
            'massa' => 'numeric',
            'height' => 'numeric',
            'width' => 'numeric',
            'depth' => 'numeric'
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
        if ($request->input('category')) {
            $sql .= ' and category = ?';
            $sqlArr [] = $request->input('category');
            $filterArr ['category'] = $request->input('category');
        }
        if ($request->input('power')) {
            $sql .=' and power = ?';
            $sqlArr []= $request->input('power');
            $filterArr ['power']= $request->input('power');
        }
        if ($request->input('voltage')) {
            $sql .=' and voltage = ?';
            $sqlArr []= $request->input('voltage');
            $filterArr ['voltage']= $request->input('voltage');
        }
        if ($request->input('massa')) {
            $sql .=' and massa = ?';
            $sqlArr []= $request->input('massa');
            $filterArr ['massa']= $request->input('massa');
        }
        if ($request->input('width')) {
            $sql .=' and width = ?';
            $sqlArr []= $request->input('width');
            $filterArr ['width']= $request->input('width');
        }
        if ($request->input('height')) {
            $sql .=' and height = ?';
            $sqlArr []= $request->input('height');
            $filterArr ['height']= $request->input('height');
        }
        if ($request->input('depth')) {
            $sql .=' and depth = ?';
            $sqlArr []= $request->input('depth');
            $filterArr ['depth']= $request->input('depth');
        }


        $closesData = Electrics::whereRaw($sql, $sqlArr) -> paginate($this->getConfig('itemsOnPage'));
        //обрабатываем словари и изображения
        $viewData=[];

        foreach ($closesData as $item){
            $viewData[] = $this->copyData($item);
        }
        //наполняем массив
        $data= array(
            'title' => 'Магазин - '.$this->title,
            'pageTitle' => $this->title.' - фильтр',
            'viewData' => $viewData,
            'modelData' => $closesData,
            'count' => Electrics::count(),
            'structure' => $this->structure,
            'table' => $this->table,
            'filterData' => $filterArr, //параметры фильтра, чтобы вернуть в форму
            'admin' => $this->isAdmin()//являетcя ли пользователь админом
        );
        return view('pages.list',$data);
    }
    //отображение одиночной записи
    public function single($id)
    {
        $closesData = Electrics::find($id);

        $viewData = $this->copyData($closesData);

        $data= array(
            'title' => 'Магазин - '.$this->title,
            'pageTitle' => $this->title.' - Карточка товара',
            'viewData' => $viewData,
            'modelData' => $closesData,
            'structure' => $this->structure,
            'table' => $this->table,
            'productTitle' => $this->title,
            'id' => $id
        );
        return view('pages.product',$data);
    }

    //отображение формы редактирования
    public function edit($id)
    {
        $closesData = Electrics::find($id);
        //обрабатываем словари и изображения

        $viewData = $this->copyData($closesData);

        //наполняем массив
        $data= array(
            'title' => 'Магазин - '.$this->title,
            'pageTitle' => $this->title.' - Редактор. Запись '.$id,
            'viewData' => $viewData,
            'modelData' => $closesData,
            'structure' => $this->structure,
            'table' => $this->table,
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
            'title' => 'Магазин - '.$this->title,
            'pageTitle' => $this->title.' - Редактор. Новая запись',
            'viewData' => $viewData,//пустой массив
            'modelData' => [],//пустой массив
            'structure' => $this->structure,
            'table' => $this->table,
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
            'category' => 'integer',
            'power'=>'ingeger',
            'voltage'=>'ingeger',
            'massa' => 'numeric',
            'height' => 'numeric',
            'width' => 'numeric',
            'depth' => 'numeric'
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
                $closes = Electrics::find($request->id);
            }
            if (!$request->id || !isset($closes)|| !$closes)  {
                //иначе, или не нашли, тогда новая запись
                $closes = new Electrics();
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
            $closes->category = $request->input('category');

            $closes->power = $request->input('power');
            $closes->voltage = $request->input('voltage');
            $closes->massa = $request->input('massa');
            $closes->width = $request->input('width');
            $closes->height = $request->input('heigth');
            $closes->depth = $request->input('depth');
            // сохранение
            $closes->save();
            //редиректим на список
            return redirect('/electrics');
        } catch (Exception $e) {
            Log::error('Ошибка редактирования/добавления записи '.$e->getMessage());
            return back()->withInput();
        }
    }
    //наполнение тестовыми данными
    public function sample()
    {
        $faker = Faker::create();
        $manufacturers = ['IEK','TDM Electric','Siemens','ABB','Legend'];
        $category = ['Кабельная пробукция','Счётчики','Розетки и выключатели','Инструмент для монтажа'];
        $country = ['Россия','Польша','Китай','Турция','Беларусь'];
        $voltage = ['12','220'];

        try {
            foreach (range(1, 20) as $index){
                $closes = new Electrics();
                $closes->name = $faker->word;
                $closes->manufacturer = $this->writeDict($this->table,'manufacturer',$manufacturers[mt_rand(0,count($manufacturers)-1)]);
                $closes->description = $faker->text;
                $closes->price = $faker->randomFloat;
                $closes->count = $faker->randomDigit;
                $closes->image = $this->writeImagesFromFile($faker->image(public_path().'/'.config('shop.images'),800,600,'technics') );
                $closes->country = $this->writeDict($this->table,'country',$country[mt_rand(0,count($country)-1)]);
                $closes->category = $this->writeDict($this->table,'category',$category[mt_rand(0,count($category)-1)]);

                $closes->power = $faker->randomDigit;
                $closes->voltage = $this->writeDict($this->table,'voltage',$voltage[mt_rand(0,count($voltage)-1)]);
                $closes->width = $faker->randomFloat;
                $closes->massa = $faker->randomFloat;
                $closes->height = $faker->randomFloat;
                $closes->depth = $faker->randomFloat;

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
        $this->structure['manufacturer']['options'] = $this->getDictList($this->table,'manufacturer');
        $this->structure['country']['options'] = $this->getDictList($this->table,'country');
        //описываем дополнительные поля для модели, которых нет в базовом контроллере
        $this->structure['category'] = ['title' => 'Категория','type' => 'select'];
        $this->structure['power'] = ['title' => 'Мощность, Вт.','type' => 'text'];
        $this->structure['voltage'] = ['title' => 'Напряжение, В.','type' => 'select'];
        $this->structure['massa'] = ['title' => 'Вес, кг','type' => 'text'];
        $this->structure['width'] = ['title' => 'Ширина, см','type' => 'text'];
        $this->structure['height'] = ['title' => 'Высота, см','type' => 'text'];
        $this->structure['depth'] = ['title' => 'Глубина, см','type' => 'text'];
        //для уникальных полей select заполняем возможные значения
        $this->structure['category']['options'] = $this->getDictList($this->table,'category');
        $this->structure['voltage']['options'] = $this->getDictList($this->table,'voltage');
    }

}
