<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Validator;
use Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data= array(
            'title' => 'Магазин - Одежда',
            'pageTitle' => 'Одежда',
        );
        return view('home',$data);
    }

    public function sendMail($request)
    {
        $validator = Validator::make($request->all(), [
            'spancheck' => 'size|0',
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'product' => 'required'
        ]);
        $message='';
        $status = false;
        if ($validator->fails()) {
            $message = 'Ошибка - вы ввели неправильные данные';
            $status = false;
        } else {
//            $text = '<p>'.$request->name.' заказал '.$request->product.
//                ' на сайте '.config('shop.siteName').'</p><p> и оставил свои контактные данные: телефон '.
//                $request->phone.' email '.$request->email.'</p>';
            $data = array (
                'name' => $request->name,
                'product' => $request->product,
                'siteName' => config('shop.siteName'),
                'phone' => $request->phone,
                'email' => $request->email
            );
            try {
                Mail::send('common.mail',$data,function($message){
                    $message->to(config('shop.adminEmail'));
                    $message->subject('Заказ товара на сайте '.config('shop.siteName'));
                });
                $status = true;
                $message = '<p>Ваш заказ принят.</p><p>Наш менеджер свяжется с Вами в ближайшее время.</p>'.
                    '<p>Спасибо за обращение в нашу компанию!</p>';

            } catch (Exception $e) {
                $status = false;
                $message = 'Сообщение не отправлено '.$e->getMessage();
            }
        }
        return json_encode(array(
            'status' => $status,
            'message' => $message
        ));

    }



}
