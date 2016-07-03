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
                        {{--<li role="presentation">--}}
                            {{--<a class="btn   page-header__button" href="/manager"> <i class="fa fa-list"></i> Отменить</a>--}}
                        {{--</li>--}}
                        <li role="presentation">
                            <button type="submit" class="btn  page-header__button" form="form-manager"><i class="fa fa-cloud"></i> Сохранить</button>
                        </li>

                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            @include('common.menu_manager')

            <div class="col-md-8 panel panel-default ">
                <div class="row panel-heading">
                   Основые настройки сайта
                </div>
                {{--сообщения о валидации - пока стандартные--}}
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="form-manager" class="form-manager" action="/manager/site/save" method="post">
                     <div class="formgroup form-manager__formgroup clearfix">
                        <div class="col-sm-4">
                            <label class="form-manager__label" for="siteName">Название сайта</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" id="siteName" name="siteName" class="form-manager__input" placeholder="Название сайта" value="{{ $siteName or '' }}">
                        </div>

                    </div>
                    <div class="formgroup form-manager__formgroup clearfix">
                        <div class="col-sm-4">
                            <label class="form-manager__label" for="adminEmail">Email администратора</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" id="adminEmail" name="adminEmail" class="form-manager__input" placeholder="Email администратора" value="{{ $adminEmail or '' }}">
                        </div>

                    </div>
                    <div class="formgroup form-manager__formgroup clearfix">
                        <div class="col-sm-4">
                            <label class="form-manager__label" for="onpage">Количество элементов на странице</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" id="onpage" name="itemsOnPage" class="form-manager__input" placeholder="Количество элементов на странице" value="{{ $itemsOnPage or '' }}">
                        </div>

                    </div>
                    {{ csrf_field() }}

                </form>

            </div>

        </div>
    </div>
@endsection
