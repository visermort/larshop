<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Fishing;
use Faker\Factory as Faker;
use App\Models\Images;;


class ControllerFishing extends Controller
{
    private $title = 'Рыболовные удочки';
    private $table = 'fishing';

    //отображeние списком
    public function index()
    {
        $closesData = Fishing::paginate($this->getConfig('itemsOnPage'));
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
            'count' => Fishing::count(),
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
            $closes = Fishing::find($id);
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
            'material' => 'integer',
            'length'=>'integer',
            'construct' => 'integer',
            'weigth' => 'integer'
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
        $sql .= $this->addFilter('material',$request->input('material'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('length',$request->input('length'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('construct',$request->input('construct'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('weight',$request->input('weight'),$sqlArr,$filterArr);

        $closesData = Fishing::whereRaw($sql, $sqlArr) -> paginate($this->getConfig('itemsOnPage'));
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
            'count' => Fishing::count(),
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
        $closesData = Fishing::find($id);

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
        $closesData = Fishing::find($id);
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
            'material' => 'integer',
            'length'=>'integer',
            'construct' => 'integer',
            'weigth' => 'integer'
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
                $closes = Fishing::find($request->id);
            }
            if (!$request->id || !isset($closes)|| !$closes)  {
                //иначе, или не нашли, тогда новая запись
                $closes = new Fishing();
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
            $closes->material = $request->input('material');
            $closes->length = $request->input('length');
            $closes->construct = $request->input('construct');
            $closes->weight = $request->input('weight');
            // сохранение
            $closes->save();
            //редиректим на список
            return redirect('/Fishing');
        } catch (Exception $e) {
            Log::error('Ошибка редактирования/добавления записи '.$e->getMessage());
            return back()->withInput();
        }
    }
    //наполнение тестовыми данными
    public function sample()
    {
        $faker = Faker::create();
        $manufacturers = ['Garbolino','Salmo','Tubertino'];
        $country = ['Россия','Китай','Финляндия','Польша'];

        $type = ['Бортовые','Карповые','Матчевые','Нахлыстовые','Троллинговые','Фидерные'];
        $material = ['Стекловолокно','Стекропластик','Угдеволокно','Carbon','Composit'];
        $length = ['<1','1-2','2.1-3','3.1-4','4.1-5','5.1-6','6.1-7','>7'];
        $construct = ['Штекерный','Телескопический','Телескоп-штекерный'];
        $weight = ['100-200','201-300','301-400','401-500','501-600','>600'];


        try {
            foreach (range(1, 20) as $index){
                $closes = new Fishing();
                $closes->name = $faker->word;
                $closes->manufacturer = $this->writeDict($this->table,'manufacturer',$manufacturers[mt_rand(0,count($manufacturers)-1)]);
                $closes->description = $faker->text;
                $closes->price = $faker->randomFloat;
                $closes->count = $faker->randomDigit;
                $closes->image = $this->writeImagesFromFile($faker->image(public_path().'/'.config('shop.images'),800,600,'nature') );
                $closes->country = $this->writeDict($this->table,'country',$country[mt_rand(0,count($country)-1)]);

                $closes->type = $this->writeDict($this->table,'type',$type[mt_rand(0,count($type)-1)]);
                $closes->material =$this->writeDict($this->table,'material',$material[mt_rand(0,count($material)-1)]);
                $closes->length = $this->writeDict($this->table,'length',$length[mt_rand(0,count($length)-1)]);
                $closes->construct = $this->writeDict($this->table,'construct',$construct[mt_rand(0,count($construct)-1)]);
                $closes->weight = $this->writeDict($this->table,'weight',$weight[mt_rand(0,count($weight)-1)]);

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
        $this->structure['type'] = ['title' => 'Назначение','type' => 'select'];
        $this->structure['material'] = ['title' => 'Материал удилища','type' => 'select'];
        $this->structure['length'] = ['title' => 'Длина, м','type' => 'select'];
        $this->structure['construct'] = ['title' => 'Тип конструкции','type' => 'select'];
        $this->structure['weight'] = ['title' => 'Вес, грамм','type' => 'select'];
        //для уникальных полей select заполняем возможные значения
        $this->structure['type']['options'] = $this->getDictList($this->table,'type');
        $this->structure['material']['options'] = $this->getDictList($this->table,'material');
        $this->structure['length']['options'] = $this->getDictList($this->table,'length');
        $this->structure['construct']['options'] = $this->getDictList($this->table,'construct');
        $this->structure['weight']['options'] = $this->getDictList($this->table,'weight');
    }

}
