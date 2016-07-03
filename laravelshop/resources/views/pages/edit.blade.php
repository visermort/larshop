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
                    <ul class="nav nav-pills navbar-right page-header__nav">
                        <li role="presentation">
                            <a class="btn btn-primary  page-header__button" href="/{{ $table }}"> <i class="fa fa-list"></i> Каталог</a>
                        </li>
                        <li role="presentation">
                            <button type="submit" class="btn btn-primary page-header__button" form="form-edit"><i class="fa fa-cloud"></i> Сохранить</button>
                        </li>

                    </ul>
                </div>
            </div>
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
        <div class="row">
            {{--форма редактирования--}}
                         {{--         {{ dd($viewData) }}--}}
                        {{--            {{dd($structure)}}--}}
            <form class="form-edit" action="/manager/{{ $table }}/save" method="post" enctype="multipart/form-data" id="form-edit">
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
                            <div class="form-edit__file_input">
                                <input class="form-edit__input" type="file" name="{{ $key }}">
                            </div>
                        @elseif ($item['type'] == 'select')
                            {{--select--}}
                            <select class="form-edit__input" name="{{ $key }}" >
                                @if (isset($item['options']))
                                    @foreach ($item['options'] as $index => $value)
                                        <option value="{{ $index }}" @if ($value == $viewData[$key]) selected  @endif  >
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
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
                {{--вставили ключ--}}
                {{ csrf_field() }}
            </form>
        </div>
    </div>
@endsection
