<?php

namespace App\Http\Controllers;


use App\Http\Requests;
use App\Models\Closes;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Auth;


class ControllerCloses extends Controller
{

    //отображeние списком
    public function index()
    {
       // $closes = new Closes;
        $onPage = config('shop.itemsOnPage');
        $closesData = Closes::paginate($onPage);
        //обрабатываем словари и изображения
        $viewData=[];
//        dd($closesData);
        foreach ($closesData as $item){
            $viewData[] = $this->copyData($item);
        }
        //наполняем массив
        $data= array(
            'title' => 'Магазин - Одежда',
            'pageTitle' => 'Одежда',
            'viewData' => $viewData,
            'modelData' => $closesData,
            'count' => Closes::count(),
            'structure' => $this->structure,
            'table' => 'closes',
            'admin' => (Auth::check() && Auth::user()['attributes']['email'] == config('shop.adminEmail'))//являетcя ли пользователь админом
        );
        return view('pages.list',$data);

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
            'size' => 'integer',
            'season' => 'integer',
            'sex' => 'integer',
            'category' => 'integer',
        ]);
        $onPage = config('shop.itemsOnPage');
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
        if ($request->input('size')) {
            $sql .=' and size = ?';
            $sqlArr []= $request->input('size');
            $filterArr ['size']= $request->input('size');
        }
        if ($request->input('season')) {
            $sql .=' and season = ?';
            $sqlArr []= $request->input('season');
            $filterArr ['season']= $request->input('season');
        }
        if ($request->input('sex')) {
            $sql .=' and sex = ?';
            $sqlArr []= $request->input('sex');
            $filterArr ['sex']= $request->input('sex');
        }
        if ($request->input('category')) {
            $sql .=' and category = ?';
            $sqlArr []= $request->input('category');
            $filterArr ['category']= $request->input('category');
        }

        $closesData = Closes::whereRaw($sql, $sqlArr)
           ->paginate($onPage);
        //обрабатываем словари и изображения
        $viewData=[];

        foreach ($closesData as $item){
            $viewData[] = $this->copyData($item);
        }
        //наполняем массив
        $data= array(
            'title' => 'Магазин - Одежда',
            'pageTitle' => 'Одежда - фильтр',
            'viewData' => $viewData,
            'modelData' => $closesData,
            'count' => Closes::count(),
            'structure' => $this->structure,
            'table' => 'closes',
            'filterData' => $filterArr, //параметры фильтра, чтобы вернуть в форму
            'admin' => (Auth::check() && Auth::user()['attributes']['email'] == config('shop.adminEmail'))//являетcя ли пользователь админом
        );
        return view('pages.list',$data);
    }
    //отображение одиночной записи
    public function single($id)
    {
        $closesData = Closes::find($id);

        $viewData = $this->copyData($closesData);

        $data= array(
            'title' => 'Магазин - Одежда',
            'pageTitle' => 'Одежда - Карточка товара',
            'viewData' => $viewData,
            'modelData' => $closesData,
            'structure' => $this->structure,
            'table' => 'closes',
            'id' => $id
        );
        return view('pages.product',$data);
    }

    //отображение формы редактирования
    public function edit($id)
    {
        $closesData = Closes::find($id);
        //обрабатываем словари и изображения

        $viewData = $this->copyData($closesData);

        //наполняем массив
        $data= array(
            'title' => 'Магазин - Одежда',
            'pageTitle' => 'Одежда - Редактор. Запись '.$id,
            'viewData' => $viewData,
            'modelData' => $closesData,
            'structure' => $this->structure,
            'table' => 'closes',
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
            'title' => 'Магазин - Одежда',
            'pageTitle' => 'Одежда - Редактор. Новая запись',
            'viewData' => $viewData,//пустой массив
            'modelData' => [],//пустой массив
            'structure' => $this->structure,
            'table' => 'closes',
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
            'size' => 'integer',
            'season' => 'integer',
            'sex' => 'integer',
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
                $closes = Closes::find($request->id);
            }
            if (!$request->id || !isset($closes)|| !$closes)  {
                //иначе, или не нашли, тогда новая запись
                $closes = new Closes();
            }

            $closes->name = $request->input('name');
            $closes->manufacturer = $request->input('manufacturer');
            $closes->price = $request->input('price');
            $closes->count = $request->input('count');
            if ($imageId) {
                $closes->image = $imageId;
            }
            $closes->country = $request->input('country');
            $closes->size = $request->input('size');
            $closes->season = $request->input('season');
            $closes->sex = $request->input('sex');
            $closes->category = $request->input('category');
            // сохранение
            $closes->save();
            //редиректим на список
            return redirect('/closes');
        } catch (Exception $e) {
            Log::error('Ошибка редактирования/добавления записи '.$e->getMessage());
            return back()->withInput();
        }
    }
    //наполнение тестовыми данными
    public function sample()
    {
        $faker = Faker::create();
        $manufacturers = ['Berries','Dress','Jerutti'];
        $sizes = ['xs','s','m','l','xl','xxl'];
        $season = ['Зима','Лето','Весна-осень'];
        $sexs = ['М','Ж','Детская'];
        $category = ['Рубашки','Брюки','Куркти','Пальто','Блузки','Юбки'];
        $country = ['Россия','Китай','Германия','Франция'];

        try {
            foreach (range(1, 4) as $index){
                $closes = new Closes();
                $closes->name = $faker->word;
                $closes->manufacturer = $this->writeDict('closes','manufacturer',$manufacturers[mt_rand(0,count($manufacturers)-1)]);
                $closes->description = $faker->text;
                $closes->price = $faker->randomFloat;
                $closes->count = $faker->randomDigit;
                $closes->image = $this->writeImagesFromFile($faker->image(public_path().'/'.config('shop.images'),800,600,'fashion') );
                $closes->size = $this->writeDict('closes','size',$sizes[mt_rand(0,count($sizes)-1)]);
                $closes->season = $this->writeDict('closes','season',$season[mt_rand(0,count($season)-1)]);
                $closes->sex = $this->writeDict('closes','sex',$sexs[mt_rand(0,count($sexs)-1)]);
                $closes->category = $this->writeDict('closes','category',$category[mt_rand(0,count($category)-1)]);
                $closes->country = $this->writeDict('closes','country',$country[mt_rand(0,count($country)-1)]);
                $closes->save();

            }
            echo 'Done!';
        } catch (Exception $e) {
            Log::error('Ошибка записи '.$e->getMessage());
            return redirect('/home');
        }
    }

    public function __construct()
    {
        //для полей select наполняем возможные значения словаря - обязательные - для всех моделей
        $this->structure['manufacturer']['options'] = $this->getDictList('closes','manufacturer');
        $this->structure['country']['options'] = $this->getDictList('closes','country');
        //описываем дополнительные поля для модели, которых нет в базовом контроллере
        $this->structure['size'] = ['title' => 'Размер','type' => 'select'];
        $this->structure['season'] = ['title' => 'Сезон','type' => 'select'];
        $this->structure['sex'] = ['title' => 'Пол','type' => 'select'];
        $this->structure['category'] = ['title' => 'Категория','type' => 'select'];
        //для уникальных полей select заполняем возможные значения
        $this->structure['size']['options'] = $this->getDictList('closes','size');
        $this->structure['season']['options'] = $this->getDictList('closes','season');
        $this->structure['sex']['options'] = $this->getDictList('closes','sex');
        $this->structure['category']['options'] = $this->getDictList('closes','category');
    }

}
