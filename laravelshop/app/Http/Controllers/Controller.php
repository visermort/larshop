<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use App\Models\Images;
use App\Models\Dict;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    //images
    protected function writeImages( $file)
    {
        $image = new Images;



        $idImage=0;

        return $idImage;
    }
    //обработка файла, когда он уже в папке (для заполнения тестовыми данными)
    protected function writeImagesFromFile($file)
    {

    }
    protected function getFullImage($id)
    {
        return '';
    }
    protected function getThumbnail($id)
    {
        return '';
    }
    protected function getMidImage($id)
    {
        return '';
    }
    //dictionary
    //проверка, есть ли значение,если нет, то вставка
    protected function writeDict($table,$field,$value)
    {
       // $dict =  Dict::firstOrCreate(array(
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
}
