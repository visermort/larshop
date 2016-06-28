<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use App\Models\Images;
use App\Models\Dict;
use Intervention\Image\Facades\Image as Image;



class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    //images
    protected function writeImages( $file)
    {
        $image = new Images;
//        $image->original = $file



        $idImage=0;

        return $idImage;
    }
    //обработка файла, когда он уже в папке
    protected function writeImagesFromFile($file)
    {
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

    }
    protected function getFullImage($id)
    {
        $images = Images::where('id', $id);
        if (count($images)){
            return $images[0]->original;
        } else {
            return 0;
        }
    }
    protected function getThumbnail($id)
    {
        $images = Images::where('id', $id);
        if (count($images)){
            return $images[0]->thumb;
        } else {
            return 0;
        }
    }
    protected function getMidImage($id)
    {
        $images = Images::where('id', $id);
        if (count($images)){
            return $images[0]->middle;
        } else {
            return 0;
        }
    }
    //dictionary
    //проверка, есть ли значение, если нет, то вставка
    protected function writeDict($table,$field,$value)
    {
       // $dict =  Dict::firstOrCreate(array( - не работает, непонятная ошибка
       //     'table_name' => $table,
       //     'field_name' => $field,
       //     'value' => $value));
        $dict = Dict::where('table_name', $table)
            ->where('field_name', $field)
            ->where('value', $value)
            ->get();
        if (count($dict)){
            return $dict[0]->id;
        } else {
            $dict = new Dict;
            $dict ->table_name = $table;
            $dict ->field_name = $field;
            $dict ->value = $value;
            $dict ->save();
            return $dict->id;
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
        $dict = Dict::where('id', $id);
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
                $list[]=$item->value;
            }
            return $list;
        } else {
            return [];
        }
    }
}
