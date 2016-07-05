@extends('layouts.app')

@section('content')


    <div class="container">
        <div class="row">
            <div class="page-header clearfix">
                <div class="col-sm-6">
                    <h1 class="page-header__h1">{{ $pageTitle }}</h1>
                </div>
                <div class="col-sm-6">
                    <ul class="nav nav-pills navbar-right page-header__nav">
                        <li role="presentation">
                            <a class="btn   page-header__button" href="/manager"> <i class="fa fa-wrench"></i> Панель администратора</a>
                        </li>
                        {{--<li role="presentation">--}}
                            {{--<button type="submit" class="btn btn-primary page-header__button" form="form-edit"><i class="fa fa-cloud"></i> Сохранить</button>--}}
                        {{--</li>--}}

                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            @include('common.menu_manager')

            <div class="col-md-8 panel panel-default ">
                <div class="row panel-heading">
                    Инструменты настройки модели "{{ $model_title }}"
                </div>
                <div class="navbar">
                    <ul class="nav navbar-nav  ">
                        <li role="presentation">
                                <a class="btn   " href="/{{ $model }}">
                                    <i class="fa fa-list"></i> Каталог
                                </a>
                        </li>
                        <li role="presentation">
                                <a class="btn   " href="/manager/{{ $model }}/add">
                                    <i class="fa fa-plus"></i> Добавить запись
                                </a>
                        </li>
                        <li role="presentation">
                                <a class="btn   " href="/manager/{{ $model }}/sample/">
                                    <i class="fa fa-plus"></i><i class="fa fa-plus"></i> Наполнить тестовыми данными
                                </a>
                        </li>
                    </ul>
                </div>
                <div class="dictlist">
                    {{--<pre><{{ print_r($dictList) }}/pre>--}}
                    <div class="dictlist-header">
                        Словари модели
                    </div>
                    <ul class="dictlist__tabs">
                        @foreach($dictList as $key=>$dict)
                            <li class="dictlist__tabs-item tab-{{$key}}">
                            {{ $key }}
                            </li>
                        @endforeach
                    </ul>
                    <ul class="dictlist__panels">
                        @foreach($dictList as $key=>$dicts)
                            <li class="dictlist__panels-item panel-{{$key}}">
                                <div class="dictlist__panels-item-panel">
                                    <div class="dictlist__panel-head">
                                        словарь для поля {{ $key }}
                                    </div>
                                    <ul class="dictlist__panels-item-list">
                                    @foreach ($dicts as $id=>$dict)
                                        <li class="dictlist__value-item">
                                            {{$id}} : {{$dict}} <button class="dictlist__button bnt btn-default dict-del" data-id="{{$id}}">Удалить </button>
                                        </li>

                                    @endforeach
                                    </ul>
                                    <div class="dictlist__formgroup">
                                        <input class="dictlist__input-dict" type="text" >
                                        <button class="dictlist__button bnt btn-default dict-add" data-table="{{$model}}" data-field="{{$key}}" >Добавить </button>
                                    </div>
                                </div>
                            </li>

                        @endforeach

                    </ul>
                    <div id="csrftoken" data-token="{{ csrf_token() }}"></div>
                </div>

            </div>
        </div>
    </div>
@endsection
