<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Contact_lenses;
use Faker\Factory as Faker;
use App\Models\Images;;

class ControllerContact_lenses extends Controller
{
    private $title = 'Контактные линзы';
    private $table = 'contact_lenses';

    //отображeние списком
    public function index()
    {
        $closesData = Contact_lenses::paginate($this->getConfig('itemsOnPage'));
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
            'count' => Contact_lenses::count(),
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
            $closes = Contact_lenses::find($id);
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
            'diameter'=>'integer',
            'ufprotect' => 'integer',
            'dioptleft' => 'integer',
            'dioptright' => 'integer',
            'curve' => 'integer'
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
        $sql .= $this->addFilter('diameter',$request->input('diameter'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('ufprotect',$request->input('ufprotect'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('dioptleft',$request->input('dioptleft'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('dioptright',$request->input('dioptright'),$sqlArr,$filterArr);
        $sql .= $this->addFilter('curve',$request->input('curve'),$sqlArr,$filterArr);

        $closesData = Contact_lenses::whereRaw($sql, $sqlArr) -> paginate($this->getConfig('itemsOnPage'));
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
            'count' => Contact_lenses::count(),
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
        $closesData = Contact_lenses::find($id);

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
        $closesData = Contact_lenses::find($id);
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
            'diameter'=>'integer',
            'ufprotect' => 'integer',
            'dioptleft' => 'integer',
            'dioptright' => 'integer',
            'curve' => 'integer'
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
                $closes = Contact_lenses::find($request->id);
            }
            if (!$request->id || !isset($closes)|| !$closes)  {
                //иначе, или не нашли, тогда новая запись
                $closes = new Contact_lenses();
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
            $closes->ufprotect = $request->input('ufprotect');
            $closes->diameter = $request->input('diameter');
            $closes->dioptleft = $request->input('dioptleft');
            $closes->dioptright = $request->input('dioptright');
            $closes->curve = $request->input('curve');
            // сохранение
            $closes->save();
            //редиректим на список
            return redirect('/Contact_lenses');
        } catch (Exception $e) {
            Log::error('Ошибка редактирования/добавления записи '.$e->getMessage());
            return back()->withInput();
        }
    }
    //наполнение тестовыми данными
    public function sample()
    {
        $faker = Faker::create();
        $manufacturers = ['Acuvie','Dailles','FreshLook','Опти-Фри'];
        $country = ['Россия','Германия','Финляндия'];

        $type = ['Однодневные','Двухдневные','Ежемесячные'];
        $material = ['Гидрогель','Силикон-гидрогель'];
        $diameter = ['12 mm','13 mm','13.8 mm','14.5 mm','15 mm'];
        $ufprotect = ['Нет','Есть'];
        $dioptleft = ['-3','-2.5','-2','1.5','-1','-0.5','0','+3','+2.5','+2','+1.5','+1','+0.5'];
        $dioptright = ['-3','-2.5','-2','1.5','-1','-0.5','0','+3','+2.5','+2','+1.5','+1','+0.5'];
        $curve = ['7.8','8.0','8.2','8.5','8.7','8.9','9.2','9.5'];


        try {
            foreach (range(1, 20) as $index){
                $closes = new Contact_lenses();
                $closes->name = $faker->word;
                $closes->manufacturer = $this->writeDict($this->table,'manufacturer',$manufacturers[mt_rand(0,count($manufacturers)-1)]);
                $closes->description = $faker->text;
                $closes->price = $faker->randomFloat;
                $closes->count = $faker->randomDigit;
                $closes->image = $this->writeImagesFromFile($faker->image(public_path().'/'.config('shop.images'),800,600,'people') );
                $closes->country = $this->writeDict($this->table,'country',$country[mt_rand(0,count($country)-1)]);

                $closes->type = $this->writeDict($this->table,'type',$type[mt_rand(0,count($type)-1)]);
                $closes->material =$this->writeDict($this->table,'material',$material[mt_rand(0,count($material)-1)]);
                $closes->diameter = $this->writeDict($this->table,'diameter',$diameter[mt_rand(0,count($diameter)-1)]);
                $closes->ufprotect = $this->writeDict($this->table,'ufprotect',$ufprotect[mt_rand(0,count($ufprotect)-1)]);
                $closes->dioptleft = $this->writeDict($this->table,'dioptleft',$dioptleft[mt_rand(0,count($dioptleft)-1)]);
                $closes->dioptright = $this->writeDict($this->table,'dioptright',$dioptright[mt_rand(0,count($dioptright)-1)]);
                $closes->curve = $this->writeDict($this->table,'curve',$curve[mt_rand(0,count($curve)-1)]);

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
        $this->structure['type'] = ['title' => 'Тип линзы','type' => 'select'];
        $this->structure['material'] = ['title' => 'Материал','type' => 'select'];
        $this->structure['diameter'] = ['title' => 'Диаметр, мм','type' => 'select'];
        $this->structure['ufprotect'] = ['title' => 'Защита от ультарфиолета','type' => 'select'];
        $this->structure['dioptleft'] = ['title' => 'Сила левой линзы','type' => 'select'];
        $this->structure['dioptright'] = ['title' => 'Сила правой линзы','type' => 'select'];
        $this->structure['curve'] = ['title' => 'Кривизна','type' => 'select'];
        //для уникальных полей select заполняем возможные значения
        $this->structure['type']['options'] = $this->getDictList($this->table,'type');
        $this->structure['material']['options'] = $this->getDictList($this->table,'material');
        $this->structure['diameter']['options'] = $this->getDictList($this->table,'diameter');
        $this->structure['ufprotect']['options'] = $this->getDictList($this->table,'ufprotect');
        $this->structure['dioptleft']['options'] = $this->getDictList($this->table,'dioptleft');
        $this->structure['dioptright']['options'] = $this->getDictList($this->table,'dioptright');
        $this->structure['curve']['options'] = $this->getDictList($this->table,'curve');
    }

}