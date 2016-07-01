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
                            <a class="btn btn-primary  page-header__button" href="/manager"> <i class="fa fa-list"></i> Отменить</a>
                        </li>
                        <li role="presentation">
                            <button type="submit" class="btn btn-primary page-header__button" form="form-edit"><i class="fa fa-cloud"></i> Сохранить</button>
                        </li>

                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 model-menu">
                <div class=" navbar">
                    <ul class="nav nav-pills nav-stacked">
                        <li role="presentation"  class="dropdown">

                            <a class="btn model-menu__link"  href="/manager/site" role="button" >
                                Сайт
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                Модели <span class="caret"></span>
                            </a>

                            <ul class="nav nav-pills dropdown-menu nav-stacked">
                                @foreach(config('shop.models') as $menuItem)
                                    <li >
                                        <a class="btn model-menu__link  " href="/manager/model/{{ $menuItem['href'] }}">
                                            <i class="fa fa-shopping-bag"></i>  {{$menuItem['title']}}
                                        </a>
                                    </li>
                                    @endforeach
                            </ul>
                        </li>
                    </ul>
                </div>



            </div>
            <div class="col-md-8 ">
                 Инструменты настройки
            </div>
        </div>
    </div>
@endsection
