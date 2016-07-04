<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Animal_food;
use Faker\Factory as Faker;
use App\Models\Images;

class ControllerAnimal_food extends Controller
{
    private $title = 'Корм для животных';
    private $table = 'animal_food';

    //отображeние списком
    public function index()
    {
        $closesData = Animal_food::paginate($this->getConfig('itemsOnPage'));
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
            'count' => Animal_food::count(),
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
        if ($this->isAdmin()) {
            $closes = Animal_food::find($id);
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
            'type' => 'integer',
            'animal'=>'integer',
            'age' => 'integer',
            'taste' => 'integer',
            'special' => 'integer'
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
        $sql .= $this->addFilter('count',$request->input('count'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('country',$request->input('country'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('type',$request->input('type'),$sqlArr,$filterArr);

        $sql .= $this->addFilter('animal',$request->input('animal'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('age',$request->input('age'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('taste',$request->input('taste'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('special',$request->input('special'),$sqlArr,$filterArr);

        $closesData = Animal_food::whereRaw($sql, $sqlArr) -> paginate($this->getConfig('itemsOnPage'));
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
            'count' => Animal_food::count(),
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
        $closesData = Animal_food::find($id);

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
        $closesData = Animal_food::find($id);
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
            'type' => 'integer',
            'animal'=>'integer',
            'age' => 'integer',
            'taste' => 'integer',
            'special' => 'integer'
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
                $closes = Animal_food::find($request->id);
            }
            if (!$request->id || !isset($closes)|| !$closes)  {
                //иначе, или не нашли, тогда новая запись
                $closes = new Animal_food();
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
            $closes->type = $request->input('type');

            $closes->animal = $request->input('animal');
            $closes->age = $request->input('age');
            $closes->taste = $request->input('taste');
            $closes->special = $request->input('special');
            // сохранение
            $closes->save();
            //редиректим на список
            return redirect('/animal_food');
        } catch (Exception $e) {
            Log::error('Ошибка редактирования/добавления записи '.$e->getMessage());
            return back()->withInput();
        }
    }
    //наполнение тестовыми данными
    public function sample()
    {
        $faker = Faker::create();
        $manufacturers = ['Almo Nature','Hills','Royal Canin','Tetra','Четвероногий Гурман'];
        $type = ['Корм сухой','Корм консервированный','Добавка','Каши и хлопья','Напитки'];
        $country = ['Россия','Германия','Нидеранды','Канада'];
        $age = ['Для маленьких','Для взрослых','Для пожилых'];
        $animal = ['Для кошек','Для собак','Для рыб','Для птиц','Для грызунов'];
        $taste = ['Мясное ассорти','Рыбное ассорти','Мясо-овощное ассорти','Злаковое ассорти'];
        $special = ['Без ароматизаторов','Без ГМО','Без соли','Без консервантов','Без красителей','','','','','',];//пустые, чтобы вставлялись  пустые значения


        try {
            foreach (range(1, 20) as $index){
                $closes = new Animal_food();
                $closes->name = $faker->word;
                $closes->manufacturer = $this->writeDict($this->table,'manufacturer',$manufacturers[mt_rand(0,count($manufacturers)-1)]);
                $closes->description = $faker->text;
                $closes->price = $faker->randomFloat;
                $closes->count = $faker->randomDigit;
                $closes->image = $this->writeImagesFromFile($faker->image(public_path().'/'.config('shop.images'),800,600,'animals') );
                $closes->country = $this->writeDict($this->table,'country',$country[mt_rand(0,count($country)-1)]);
                $closes->type = $this->writeDict($this->table,'type',$type[mt_rand(0,count($type)-1)]);

                $closes->animal = $this->writeDict($this->table,'animal',$animal[mt_rand(0,count($animal)-1)]);
                $closes->age = $this->writeDict($this->table,'age',$age[mt_rand(0,count($age)-1)]);
                $closes->taste = $this->writeDict($this->table,'taste',$taste[mt_rand(0,count($taste)-1)]);
                $closes->special =$this->writeDict($this->table,'special',$special[mt_rand(0,count($special)-1)]);
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
        $this->structure['type'] = ['title' => 'Тип корма','type' => 'select'];
        $this->structure['animal'] = ['title' => 'Предназначение','type' => 'select'];
        $this->structure['age'] = ['title' => 'Возраст животного','type' => 'select'];
        $this->structure['taste'] = ['title' => 'Вид вкуса','type' => 'select'];
        $this->structure['special'] = ['title' => 'Особенности','type' => 'select'];
        //для уникальных полей select заполняем возможные значения
        $this->structure['type']['options'] = $this->getDictList($this->table,'type');
        $this->structure['animal']['options'] = $this->getDictList($this->table,'animal');
        $this->structure['age']['options'] = $this->getDictList($this->table,'age');
        $this->structure['taste']['options'] = $this->getDictList($this->table,'taste');
        $this->structure['special']['options'] = $this->getDictList($this->table,'special');
    }

}