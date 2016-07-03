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
                            <a class="btn btn-primary  page-header__button" href="/manager"> <i class="fa fa-list"></i> Назад</a>
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
                <ul class="nav navbar-nav ">
                        <li role="presentation">
                            <a class="btn   " href="/{{ $model }}">
                                <i class="fa fa-list"></i> Каталог
                            </a>
                            <a class="btn   " href="/manager/{{ $model }}/add">
                                <i class="fa fa-list"></i> Добавить запись
                            </a>
                            <a class="btn   " href="/manager/{{ $model }}/sample/">
                                <i class="fa fa-list"></i> Наполнить тестовыми данными
                            </a>
                        </li>

                </ul>

            </div>
        </div>
    </div>
@endsection
