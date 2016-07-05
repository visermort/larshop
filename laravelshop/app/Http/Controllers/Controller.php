<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use App\Models\Images;
use App\Models\Dict;
use App\Models\Config;
use Intervention\Image\Facades\Image as Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;



class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    //сруктура для передачи в представления
    //поля базовой модели - для каждой категории товаров
    protected $structure = array(
        'id' => ['title' => 'id','type' => 'text'],
        'name' => ['title' => 'Наименование','type' => 'text'],
        'manufacturer' => ['title' => 'Производитель','type' => 'select'],
        'description' => ['title' => 'Описание','type' => 'textarea'],
        'price' => ['title' => 'Цена, руб.','type' => 'text'],
        'count' => ['title' => 'Количество','type' => 'text'],
        'image' => ['title' => 'Изображение','type' => 'image'],
        'country' => ['title' => 'Страна','type' => 'select']

    );

    //перенос данных из модели в данные для отображения
    //словари переделываются в значения,
    //id картинок заменяется на 3 изображения
    protected function copyData($arr){
        $res=[];
        foreach ($this->structure  as $key => $field) {
           // echo print_r($field).'<br>';
            switch ($field['type'])  {
                case 'select': $res[$key] = $this->getDict($arr[$key]);
                            break;
                case 'image': $res[$key] = config('shop.images').'/'.$this -> getFullimage($arr[$key]);
                            $res[$key.'_mid'] = config('shop.images').'/'.$this -> getMidImage($arr[$key]);
                            $res[$key.'_thumb'] = config('shop.images').'/'.$this -> getThumbnail($arr[$key]);
                            break;
                default: $res[$key] = $arr[$key];
            }
        }
        return $res;
    }


    //Картинки
    //обработка файла из $_files
    protected function writeImages( $file)
    {
        if ($file->isValid()) {
           // $file->getRealPath();
            $newFileName = $file->getClientOriginalName();
            $file->move(config('shop.images'), $newFileName);

            return $this->writeImagesFromFile($newFileName);
        } else {
            return null;
        }
    }
    //обработка файла картинки, когда он уже в папке
    protected function writeImagesFromFile($file)
    {
        try {
            $path_info = pathinfo($file);
            $fileExt = $path_info['extension'];
            $fileName = $path_info['basename'];
            $fileShort = substr($fileName, 0, strlen($fileName) - strlen($fileExt) - 1);


            //новая модель
            $images = new Images;
            //оригинал
            $images->original = $fileName;
            //открываем файл в Intervention , ресайзим, пишем имя

            $fullPath = public_path(config('shop.images') . '/' . $fileName);
            $imgTemp = Image::make($fullPath);
            $imgTemp->fit(300, 300);
            $middleName = $fileShort . '_mid';
            $imgTemp->save(public_path(config('shop.images') . '/' . $middleName . '.' . $fileExt));
            $images->middle = $middleName . '.' . $fileExt;
            //ещё ресайзим и ещё пишем
            $thumbName = $fileShort . '_thumb';
            $imgTemp->fit(100, 100);
            $imgTemp->save(public_path(config('shop.images') . '/' . $thumbName . '.' . $fileExt));
            $images->thumb = $thumbName . '.' . $fileExt;
            //сохраняем модель
            $images->save();
            //возвращаем id
            return $images->id;
        } catch (Exception $e) {
            Log::error('Ошибка обработки изображения '.$e->getMessage());
            return null;
        }

    }
    protected function getFullImage($id)
    {
        $images = Images::where('id', $id)->get();
        if (count($images)){
            return $images[0]->original;
        } else {
            return 0;
        }
    }
    protected function getThumbnail($id)
    {
        $images = Images::where('id', $id)->get();
        if (count($images)){
            return $images[0]->thumb;
        } else {
            return 0;
        }
    }
    protected function getMidImage($id)
    {
        $images = Images::where('id', $id)->get();
        if (count($images)){
            return $images[0]->middle;
        } else {
            return 0;
        }
    }
    //Словари
    //проверка, есть ли значение, если нет, то вставка
    protected function writeDict($table,$field,$value)
    {
       // $dict =  Dict::firstOrCreate(array( - не работает, непонятная ошибка
       //     'table_name' => $table,
       //     'field_name' => $field,
       //     'value' => $value));
        if (!$value) {
            return '';
        }
        try {
            $dict = Dict::where('table_name', $table)
                ->where('field_name', $field)
                ->where('value', $value)
                ->get();
            if (count($dict)) {
                return $dict[0]->id;
            } else {
                $dict = new Dict;
                $dict->table_name = $table;
                $dict->field_name = $field;
                $dict->value = $value;
                $dict->save();
                return $dict->id;
            }
        } catch (Exception $e) {
            Log::error('Ошибка записи в словарь '.$e->getMessage());
            return '';
        }
    }
    //нахождение кода по значению
    protected function getDictId($table,$field,$value)
    {
        $dict = Dict::where('table_name', $table)
            ->where('field_name', $field)
            ->where('value', $value)
            ->get();
        if (count($dict)){
            return $dict[0]->id;
        } else {
            return null;
        }
    }
    //нахождение значения по коду
    protected function getDict($id)
    {
        $dict = Dict::where('id', $id)->get();
       // print_r($dict);
        if (count($dict)){
            return $dict[0]->value;
        } else {
            return '';
        }
    }
    //вернуть весь словарь для поля таблицы
    protected function getDictList($table,$field)
    {
        $dict = Dict::where('table_name', $table)
            ->where('field_name', $field)
            ->get();
        if (count($dict)){
            $list=[];
            foreach($dict as $item){
                $list[$item->id]=$item->value;
            }
            return $list;
        } else {
            return [];
        }
    }
    //вернуть массив словарей для таблицы
    protected function getDictLists($table)
    {
        $res = [];
        $dictList = Dict::where('table_name',$table)
            ->groupBy('field_name')
            ->get();
        foreach ($dictList as $dict){
           // $res[$dict->field_name]=[];
            $dictVals = Dict::where([
                'table_name' => $table,
                'field_name' => $dict->field_name
            ]) -> get();
            foreach ($dictVals as $val) {
                $res[$dict->field_name][$val->id] = $val->value;
            }

        }
        //print_r($res);
        return $res;
    }


    //получение конфиг из таблицы или из файла
    public function getConfig($idConfig)
    {
        $res='';
        try {
            if (Schema::hasTable('config')) {
                $config = Config::where(['id_config' => $idConfig])-> get();
                if (count($config)) {
                    $res = $config[0]->config;
                }
            }
            if ($res == '') {
                $res = config('shop.' . $idConfig);
            }
            return $res;
        } catch (Exception $e) {
            return config('shop.'.$idConfig);
        }
    }

    //запись конфига
    public function saveConfig($idConfig,$value)
    {
        $config = Config::where(['id_config' => $idConfig])-> get();
        if (!count($config)) {
            $config = new Config;
            $config -> id_config = $idConfig;
        } else {
            $config = $config[0];
        }
        $config ->config = $value;
        $config -> save();
    }
    //является ли пользователь админом - пока данные из конфига, а не из базы
    protected function isAdmin()
    {
        return (Auth::check() && Auth::user()['attributes']['email'] == config('shop.adminEmail'));
    }
    //при формированиии запроса для фильтра, добавляем sql и 2 массива
    protected function addFilter($item,$requestItem,&$arr,&$filter)
    {
        if ($requestItem) {
            $arr [] = $requestItem;
            $filter [$item] = $requestItem;
        return ' and ' . $item . ' = ?';
        }
    }

//    public function __construct(){
//        $siteName = $this -> getConfig('siteName');
//        echo 'sss'.$siteName;
//        view()->composer('*',function($view) use ($siteName) {
//            $view->with('siteName',$siteName);
//        });
//
//    }

}
