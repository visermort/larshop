<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Watch;
use Faker\Factory as Faker;
use App\Models\Images;

class ControllerWatch extends Controller
{
    private $title = 'Наручные часы';
    private $table = 'watch';

    //отображeние списком
    public function index()
    {
        $closesData = Watch::paginate($this->getConfig('itemsOnPage'));
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
            'count' => Watch::count(),
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
            $closes = Watch::find($id);
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
            'style' => 'integer',
            'type' => 'integer',
            'category'=>'integer',
            'waterproof' => 'integer',
            'material' => 'integer',
            'display' => 'integer'
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


        $sql .= $this->addFilter('style',$request->input('style'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('type',$request->input('type'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('category',$request->input('category'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('waterproof',$request->input('waterproof'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('material',$request->input('material'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('display',$request->input('display'),$sqlArr,$filterArr);

        $closesData = Watch::whereRaw($sql, $sqlArr) -> paginate($this->getConfig('itemsOnPage'));
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
            'count' => Watch::count(),
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
        $closesData = Watch::find($id);

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
        $closesData = Watch::find($id);
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
            'style' => 'integer',
            'type' => 'integer',
            'category'=>'integer',
            'waterproof' => 'integer',
            'material' => 'integer',
            'display' => 'integer'
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
                $closes = Watch::find($request->id);
            }
            if (!$request->id || !isset($closes)|| !$closes)  {
                //иначе, или не нашли, тогда новая запись
                $closes = new Watch();
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

            $closes->style = $request->input('style');
            $closes->type = $request->input('type');
            $closes->category = $request->input('category');
            $closes->waterproof = $request->input('waterproof');
            $closes->material = $request->input('material');
            $closes->display = $request->input('display');
            // сохранение
            $closes->save();
            //редиректим на список
            return redirect('/Watch');
        } catch (Exception $e) {
            Log::error('Ошибка редактирования/добавления записи '.$e->getMessage());
            return back()->withInput();
        }
    }
    //наполнение тестовыми данными
    public function sample()
    {
        $faker = Faker::create();
        $manufacturers = ['Acteo','Alpina','Bluemarine','Boss Orange','Полёт'];
        $country = ['Россия','Германия','Швейцария','Австрия'];

        $style = ['Военные','Дизайнерские','На каждый день','Спортивные','Под костюм'];
        $type = ['Механические','Кварцевые','Солнечная батарея','Автокварц','С ручным заводом'];
        $category = ['Для бега','Для спорта','Для плавания','','','','',''];
        $waterproof = ['Мытьё рук, дождь','Мытьё машины, душ','Плавание без ныряния','Ныряние без акваланга','Ныряние с аквалангом'];
        $material = ['Сталь','Титан','Пластик','Силикон'];
        $display = ['Стрелочный','Цифровой','Комбинированный'];


        try {
            foreach (range(1, 20) as $index){
                $closes = new Watch();
                $closes->name = $faker->word;
                $closes->manufacturer = $this->writeDict($this->table,'manufacturer',$manufacturers[mt_rand(0,count($manufacturers)-1)]);
                $closes->description = $faker->text;
                $closes->price = $faker->randomFloat;
                $closes->count = $faker->randomDigit;
                $closes->image = $this->writeImagesFromFile($faker->image(public_path().'/'.config('shop.images'),800,600,'business') );
                $closes->country = $this->writeDict($this->table,'country',$country[mt_rand(0,count($country)-1)]);

                $closes->style = $this->writeDict($this->table,'style',$style[mt_rand(0,count($style)-1)]);
                $closes->type = $this->writeDict($this->table,'type',$type[mt_rand(0,count($type)-1)]);
                $closes->category = $this->writeDict($this->table,'category',$category[mt_rand(0,count($category)-1)]);
                $closes->waterproof = $this->writeDict($this->table,'waterproof',$waterproof[mt_rand(0,count($waterproof)-1)]);
                $closes->material = $this->writeDict($this->table,'material',$material[mt_rand(0,count($material)-1)]);
                $closes->display =$this->writeDict($this->table,'display',$display[mt_rand(0,count($display)-1)]);

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
        $this->structure['style'] = ['title' => 'Стиль','type' => 'select'];
        $this->structure['type'] = ['title' => 'Тип механизма','type' => 'select'];
        $this->structure['category'] = ['title' => 'Функциональность','type' => 'select'];
        $this->structure['waterproof'] = ['title' => 'Водозащита','type' => 'select'];
        $this->structure['material'] = ['title' => 'Материал корпуса','type' => 'select'];
        $this->structure['display'] = ['title' => 'Циферблат','type' => 'select'];
        //для уникальных полей select заполняем возможные значения
        $this->structure['style']['options'] = $this->getDictList($this->table,'style');
        $this->structure['type']['options'] = $this->getDictList($this->table,'type');
        $this->structure['category']['options'] = $this->getDictList($this->table,'category');
        $this->structure['waterproof']['options'] = $this->getDictList($this->table,'waterproof');
        $this->structure['material']['options'] = $this->getDictList($this->table,'material');
        $this->structure['display']['options'] = $this->getDictList($this->table,'display');
    }

}
