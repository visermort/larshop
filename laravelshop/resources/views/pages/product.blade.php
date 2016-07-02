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
                            <button id="button-buy" class="btn btn-primary   page-header__button" > <i class="fa fa-cart-plus"></i>  Купить</button>
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