<?php

namespace App\Http\Controllers;


use App\Http\Requests;
use App\Models\Closes;
use Faker\Factory as Faker;


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
            'table' => 'closes'
        );
        return view('pages.list',$data);

    }
    //отображение формы редактирования
    public function edit($id)
    {
        // $closes = new Closes;
        $closesData = Closes::find($id);
        //обрабатываем словари и изображения

        $viewData = $this->copyData($closesData);

        //наполняем массив
        $data= array(
            'title' => 'Магазин - Одежда',
            'pageTitle' => 'Одежда - Редактор',
            'viewData' => $viewData,
            'modelData' => $closesData,
            'structure' => $this->structure,
            'table' => 'closes'
        );
        return view('pages.edit',$data);
    }
    //сохранение результатов редактирования/добавления
    public function save()
    {
            echo 'save';
    }
    //наполнение тестовыми данными
    public function sample()
    {
        $faker = Faker::create();
        $manufacturers = ['Berries','Dress','Jerutti'];
        $sizes = ['xs','s','m','l','xl','xxl'];
        $seasons = ['Зима','Лето','Весна-осень'];
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
                $closes->season = $this->writeDict('closes','seasons',$seasons[mt_rand(0,count($seasons)-1)]);
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
        //описываем дополнительные поля для модели, которых нет в базовом контроллере
        $this->structure['size'] = ['title' => 'Размер','type' => 'select'];
        $this->structure['season'] = ['title' => 'Сезон','type' => 'select'];
        $this->structure['sex'] = ['title' => 'Пол','type' => 'select'];
        $this->structure['category'] = ['title' => 'Категория','type' => 'select'];
        //для полей select наполняем возможные значения словаря
        $this->structure['manufacturer']['options'] = $this->getDictList('closes','manufacturer');
        $this->structure['size']['options'] = $this->getDictList('closes','size');
        $this->structure['season']['options'] = $this->getDictList('closes','season');
        $this->structure['sex']['options'] = $this->getDictList('closes','sex');
        $this->structure['category']['options'] = $this->getDictList('closes','category');
        $this->structure['country']['options'] = $this->getDictList('closes','country');
    }

}
