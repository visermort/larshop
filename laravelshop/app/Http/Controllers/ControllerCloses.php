<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Closes;
use Faker\Factory as Faker;


class ControllerCloses extends Controller
{
    private $filterData;//сруктура для фильтра

    //отображeние списком
    public function index()
    {
       // $closes = new Closes;
        $onPage = config('shop.itemsOnPage');
        $closesData = Closes::paginate($onPage);
        //обрабатываем словари и изображения


        //наполняем массив
        $data= array(
            'title' => 'Магазин - Одежда',
            'pageTitle' => 'Одежда',
            'closes' => $closesData,
            'count' => Closes::count(),
            'filter' => $this->filterData
        );
        return view('pages.list',$data);

    }
    //отображение формы редактирования
    public function edit()
    {
        echo 'Edit';
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

}
