@extends('layouts.app')

@section('content')


    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Купить</h4>

                        <form class="form-message" method="post" action="/">

                            <input type="hidden" name="spancheck" value="">
                            <input type="hidden" name="product" value="Товар id= {{ $viewData['id'] }}, {{ $productTitle }} {{ $viewData['name'] }} }}">
                            <div class="modal__text">Товар {{ $productTitle }} {{ $viewData['name'] }}</div>
                            <div class="modal__text">Производитель {{ $viewData['manufacturer'] }}</div>
                            <div class="modal__text">Страна {{ $viewData['country'] }}</div>
                            <div class="modal__formgroup">
                                <label class="mmodal__label" for="modal__name">Ваше имя</label>
                                <input class="modal__input validate" id="modal__name" type="text" name="name">
                            </div>
                            <div class="modal__formgroup">
                                <label class="mmodal__label" for="modal__email">Ваш Email</label>
                                <input class="modal__input validate" id="modal__email" type="email" name="email">
                            </div>
                            <div class="modal__formgroup">
                                <label class="mmodal__label" for="modal__phone">Ваш телефон</label>
                                <input class="modal__input validate" id="modal__phone" type="text" name="phone">
                            </div>
                            <div class="modal__message">
                                <span class="modal__message_success"></span>
                                <span class="modal__message_error"></span>
                            </div>
                            <div class="modal__formgroup">
                                <button type="submit" class="btn btn-primary   modal__button" > <i class="fa fa-cart-plus"></i>  Купить</button>
                            </div>
                            {{ csrf_field() }}
                        </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            {{--панель навигации  и h1 --}}
            <div class="page-header clearfix">
                <div class="col-sm-6">
                    <h1 class="page-header__h1">{{ $pageTitle }}</h1>
                </div>
                <div class="col-sm-6">
                    <div class="nav nav-pills navbar-right page-header__nav">
                        <li role="presentation">
                            <a class="btn btn-primary  page-header__button" href="/{{ $table }}"> <i class="fa fa-list"></i>  Каталог</a>
                        </li>
                        <li role="presentation">
                            <button  type="button" data-toggle="modal" data-target="#modal" class="btn btn-primary page-header__button" > <i class="fa fa-cart-plus"></i>  Купить</button>
                        </li>
                     </div>
                </div>
            </div>
        </div>



        {{--странца с копирована со страницы редактирования, надо изменить её, чтобы выглядела как карточка товара--}}

        <div class="row">
            {{--форма прсмотра--}}
            {{--         {{ dd($viewData) }}--}}
            {{--            {{dd($structure)}}--}}
            <div class="product_view" >
                {{--проходим по всем элементам струкутры и формируем инпуты в зависимости от типа поля--}}
                @foreach ($structure as $key => $item)
                    <div class="formgroup clearfix @if ($key == 'id') hidden @endif form-edit__group">
                        <div class="col-sm-3">{{ $item['title'] }}</div>
                        <div class="col-sm-9">
                            @if ($item['type'] == 'image')
                                {{--картинки - image и file--}}
                                <div class="form-edit__image_wrap">
                                    <img class="form-edit__image" src="/{{ $viewData[$key] }}"/>
                                </div>
                            @elseif ($item['type'] == 'textarea')
                                {{--textarea--}}
                                <textarea class="form-edit__input form-edit__textarea" name="{{ $key }}">{{ $viewData[$key] }}</textarea>
                            @else
                                {{--по умолчанию text--}}
                                <input class="form-edit__input" type="text" name="{{ $key }}" value="{{ $viewData[$key] }}" >
                            @endif
                        </div>
                    </div>
                @endforeach


            </div>
        </div>
    </div>
@endsection