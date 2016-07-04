<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Phones;
use Faker\Factory as Faker;
use App\Models\Images;;

class ControllerPhones extends Controller
{
    private $title = 'Телефоны';
    private $table = 'phones';

    //отображeние списком
    public function index()
    {
        $closesData = Phones::paginate($this->getConfig('itemsOnPage'));
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
            'count' => Phones::count(),
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
            $closes = Phones::find($id);
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
            'display' => 'integer',
            'simm'=>'integer',
            'os' => 'integer',
            'camera' => 'integer',
            'gps' => 'integer'
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
        $sql .= $this->addFilter('display',$request->input('display'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('simm',$request->input('simm'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('os',$request->input('os'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('camera',$request->input('camera'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('gps',$request->input('gps'),$sqlArr,$filterArr);

        $closesData = Phones::whereRaw($sql, $sqlArr) -> paginate($this->getConfig('itemsOnPage'));
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
            'count' => Phones::count(),
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
        $closesData = Phones::find($id);

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
        $closesData = Phones::find($id);
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
            'display' => 'integer',
            'simm'=>'integer',
            'os' => 'integer',
            'camera' => 'integer',
            'gps' => 'integer'
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
                $closes = Phones::find($request->id);
            }
            if (!$request->id || !isset($closes)|| !$closes)  {
                //иначе, или не нашли, тогда новая запись
                $closes = new Phones();
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
            $closes->display = $request->input('display');
            $closes->simm = $request->input('simm');
            $closes->os = $request->input('os');
            $closes->camera = $request->input('camera');
            $closes->gps = $request->input('gps');
            // сохранение
            $closes->save();
            //редиректим на список
            return redirect('/Phones');
        } catch (Exception $e) {
            Log::error('Ошибка редактирования/добавления записи '.$e->getMessage());
            return back()->withInput();
        }
    }
    //наполнение тестовыми данными
    public function sample()
    {
        $faker = Faker::create();
        $manufacturers = ['Aple','Nokia','Sony','Fly','LG','Samsung'];
        $country = ['Россия','Китай','Финляндия','Южная Корея'];

        $type = ['Смартфоны','Кнопочные'];
        $display = ['2','3','4','5','6'];
        $simm = ['1 simm','2 simm'];
        $os = ['Без ОС','Android','Windows','Ios'];
        $camera = ['2 Mp','3 Mp','5 Mp','7 Mp','10 Mp','Без камеры'];
        $gps = ['Нет','A-gps','Gps'];


        try {
            foreach (range(1, 20) as $index){
                $closes = new Phones();
                $closes->name = $faker->word;
                $closes->manufacturer = $this->writeDict($this->table,'manufacturer',$manufacturers[mt_rand(0,count($manufacturers)-1)]);
                $closes->description = $faker->text;
                $closes->price = $faker->randomFloat;
                $closes->count = $faker->randomDigit;
                $closes->image = $this->writeImagesFromFile($faker->image(public_path().'/'.config('shop.images'),800,600,'city') );
                $closes->country = $this->writeDict($this->table,'country',$country[mt_rand(0,count($country)-1)]);

                $closes->type = $this->writeDict($this->table,'type',$type[mt_rand(0,count($type)-1)]);
                $closes->display =$this->writeDict($this->table,'display',$display[mt_rand(0,count($display)-1)]);
                $closes->simm = $this->writeDict($this->table,'simm',$simm[mt_rand(0,count($simm)-1)]);
                $closes->os = $this->writeDict($this->table,'os',$os[mt_rand(0,count($os)-1)]);
                $closes->camera = $this->writeDict($this->table,'camera',$camera[mt_rand(0,count($camera)-1)]);
                $closes->gps = $this->writeDict($this->table,'gps',$gps[mt_rand(0,count($gps)-1)]);

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
        $this->structure['type'] = ['title' => 'Тип телефона','type' => 'select'];
        $this->structure['display'] = ['title' => 'Размер дисплея, дюйм','type' => 'select'];
        $this->structure['simm'] = ['title' => 'Количество simm','type' => 'select'];
        $this->structure['os'] = ['title' => 'Операционная система','type' => 'select'];
        $this->structure['camera'] = ['title' => 'Разрешение камеры','type' => 'select'];
        $this->structure['gps'] = ['title' => 'Наличие gps','type' => 'select'];
        //для уникальных полей select заполняем возможные значения
        $this->structure['type']['options'] = $this->getDictList($this->table,'type');
        $this->structure['display']['options'] = $this->getDictList($this->table,'display');
        $this->structure['simm']['options'] = $this->getDictList($this->table,'simm');
        $this->structure['os']['options'] = $this->getDictList($this->table,'os');
        $this->structure['camera']['options'] = $this->getDictList($this->table,'camera');
        $this->structure['gps']['options'] = $this->getDictList($this->table,'gps');
    }

}