@extends('layouts.app')

@section('content')
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

        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <form class="modal_wrap" method="post" action="/">
                <h4 class="modal-title" id="myModalLabel">Купить</h4>
                <input type="hidden" name="spancheck" value="">
                <input type="hidden" name="product" value="Товар id= {{ $viewData['id'] }}, {{ $viewData['name'] }} }}">
                <div class="modal_text">{{ $viewData['name'] }}</div>
                <div class="modal_text">{{ $viewData['manufacturer'] }}</div>
                <div class="modal_text">{{ $viewData['country'] }}</div>
                <div class="modal_formgroup">
                    <label for="modal__name">Имя</label>
                    <input class="modal__input validate" id="modal__name" type="text" name="name">
                </div>
                <div class="modal_formgroup">
                    <label for="modal__email">Email</label>
                    <input class="modal__input validate" id="modal__email" type="email" name="email">
                </div>
                <div class="modal_formgroup">
                    <label for="modal__phone">Телефон</label>
                    <input class="modal__input validate" id="modal__phone" type="text" name="phone">
                </div>
                <div class="modal_message">
                    <span class="message_success">Успех</span>
                    <span class="message_error">Ошибка</span>
                </div>
                <button type="submit" class="btn btn-primary   modal__button" > <i class="fa fa-cart-plus"></i>  Купить</button>
                {{ csrf_field() }}
            </form>

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