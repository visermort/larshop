<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Arduino;
use Faker\Factory as Faker;
use App\Models\Images;

class ControllerArduino extends Controller
{
    private $title = 'Компоненты для Arduino';
    private $table = 'arduino';

    //отображeние списком
    public function index()
    {
        $closesData = Arduino::paginate($this->getConfig('itemsOnPage'));
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
            'count' => Arduino::count(),
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
            $closes = Arduino::find($id);
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
            'voltage'=>'integer',
            'eeprom' => 'integer',
            'flash' => 'integer'
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

        $sql .= $this->addFilter('voltage',$request->input('voltage'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('eeprom',$request->input('eeprom'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('flash',$request->input('flash'),$sqlArr,$filterArr);

        $closesData = Arduino::whereRaw($sql, $sqlArr) -> paginate($this->getConfig('itemsOnPage'));
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
            'count' => Arduino::count(),
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
        $closesData = Arduino::find($id);

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
        $closesData = Arduino::find($id);
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
            'voltage'=>'integer',
            'eeprom' => 'integer',
            'flash' => 'integer'
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
                $closes = Arduino::find($request->id);
            }
            if (!$request->id || !isset($closes)|| !$closes)  {
                //иначе, или не нашли, тогда новая запись
                $closes = new Arduino();
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

            $closes->voltage = $request->input('voltage');
            $closes->eeprom = $request->input('eeprom');
            $closes->flash = $request->input('flash');
            // сохранение
            $closes->save();
            //редиректим на список
            return redirect('/Arduino');
        } catch (Exception $e) {
            Log::error('Ошибка редактирования/добавления записи '.$e->getMessage());
            return back()->withInput();
        }
    }
    //наполнение тестовыми данными
    public function sample()
    {
        $faker = Faker::create();
        $manufacturers = ['Ilimex','Propox','Seeed Studio','Waleshare','Chip45.com','Henry Test'];
        $type = ['Датчики','Платы расширения','Дисплеи и индикаторы','Контроллеры','Сервоприводы и сервомоторы','Шасси, колёса, крепёж','Провода и разъёмы'];
        $country = ['Россия','Китай','Польша','США','Великобритания'];
        $voltage = ['0','3,6 B','12 B','24 B'];
        $eeprom = ['0','4 kB','8 kB','16 kB','32 kB','64 kB'];
        $flash = ['0','128 kB','256 kB','512 kB','1024 kB','2048 kB'];


        try {
            foreach (range(1, 20) as $index){
                $closes = new Arduino();
                $closes->name = $faker->word;
                $closes->manufacturer = $this->writeDict($this->table,'manufacturer',$manufacturers[mt_rand(0,count($manufacturers)-1)]);
                $closes->description = $faker->text;
                $closes->price = $faker->randomFloat;
                $closes->count = $faker->randomDigit;
                $closes->image = $this->writeImagesFromFile($faker->image(public_path().'/'.config('shop.images'),800,600,'technics') );
                $closes->country = $this->writeDict($this->table,'country',$country[mt_rand(0,count($country)-1)]);
                $closes->type = $this->writeDict($this->table,'type',$type[mt_rand(0,count($type)-1)]);

                $closes->voltage = $this->writeDict($this->table,'voltage',$voltage[mt_rand(0,count($voltage)-1)]);
                $closes->eeprom = $this->writeDict($this->table,'eeprom',$eeprom[mt_rand(0,count($eeprom)-1)]);
                $closes->flash = $this->writeDict($this->table,'flash',$flash[mt_rand(0,count($flash)-1)]);

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
        $this->structure['type'] = ['title' => 'Тип изделия','type' => 'select'];
        $this->structure['voltage'] = ['title' => 'Питание','type' => 'select'];
        $this->structure['eeprom'] = ['title' => 'Память EEPROM','type' => 'select'];
        $this->structure['flash'] = ['title' => 'Память FLASH','type' => 'select'];
        //для уникальных полей select заполняем возможные значения
        $this->structure['type']['options'] = $this->getDictList($this->table,'type');
        $this->structure['voltage']['options'] = $this->getDictList($this->table,'voltage');
        $this->structure['eeprom']['options'] = $this->getDictList($this->table,'eeprom');
        $this->structure['flash']['options'] = $this->getDictList($this->table,'flash');
    }

}