@extends('layouts.app')

@section('content')
    {{--главное меню  - не на всех страницах--}}
    @include('common.mainmenu')

    <div class="container">
       <div class="col-sm-3  panel panel-default panel-filter">
            <div class="row panel-heading">
                <div class="col-sm-12 product__cell">
                    Фильтр
                </div>
            </div>
            <div class="filter">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/{{ $table }}/filter" method="get">

                @foreach ($structure as $key => $item)
                    @if (in_array($item['type'],['text','select']) && $key !='id')
                    <div class="row filter__item">
                        @if ($key == 'price')
                            <label class="filter__label" for="filter_item_{{$key}}_from">{{ $item['title'] }} от</label>
                            <input type="text" class="filter__input" id="filter_item_{{ $key }}_from"
                                   name="{{ $key }}_from" @if (isset($filterData[$key.'_from']))  value="{{ $filterData[$key.'_from'] }}" @endif >
                            <label class="filter__label" for="filter_item_{{$key}}_to">{{ $item['title'] }} до</label>
                            <input type="text" class="filter__input" id="filter_item_{{ $key }}_to" name="{{ $key }}_to"
                                   @if (isset($filterData[$key.'_to']))  value="{{ $filterData[$key.'_to'] }}" @endif >
                        @elseif ($item['type'] == 'text')
                            <label class="filter__label" for="filter_item_{{$key}}">{{ $item['title'] }}</label>
                            <input type="text" class="filter__input" id="filter_item_{{ $key }}" name="{{ $key }}"
                                   @if (isset($filterData[$key]))  value="{{ $filterData[$key] }}" @endif >
                        @elseif ($item['type'] == 'select')
                            <label class="filter__label" for="filter_item_{{$key}}">{{ $item['title'] }}</label>
                            <select class="filter__input" id="filter_item_{{ $key }}" name="{{ $key }}">
                                <option value="" >-- Не выбрано --</option>
                                @foreach ($item['options'] as $index => $value)
                                    <option value="{{ $index }}" @if (isset($filterData[$key]) && $filterData[$key] == $index) selected @endif >
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    @endif
                @endforeach
                    <div class="formgroup filter__formgoup">
                        <input type="submit" class="btn btn-primary" value="Применить">
                    </div>
                    <div class="formgroup filter__formgoup">
                        <a class="btn btn-primary" href="/{{ $table }}" > Сбросить </a>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>

        </div>

        <div class="col-sm-9  products panel panel-default ">

            <div class="row panel-heading">

                <div class="col-sm-1 product__cell">Фото</div>
                <div class="col-sm-2 product__cell">Наименование</div>
                <div class="col-sm-2 product__cell">Производитель</div>
                <div class="col-sm-4 product__cell">Описание</div>
                <div class="col-sm-1 product__cell">Цена</div>
                <div class="col-sm-1 product__cell">На складе</div>
                <div class="col-sm-1 product__cell"></div>
            </div>
            <div class="products__list">
               @foreach ($viewData as $item)
               <div class="row products__row">
                   <div class="col-sm-1">
                       <img class="products__image" src="/{{ $item['image_thumb'] }}">
                   </div>
                   <div class="col-sm-2 product__cell">{{ $item['name'] }}</div>
                   <div class="col-sm-2 product__cell">{{ $item['manufacturer'] }}</div>
                   <div class="col-sm-4 product__cell">{{ $item['description'] }}</div>
                   <div class="col-sm-1 product__cell">{{ $item['price'] }}</div>
                   <div class="col-sm-1 product__cell">{{ $item['count'] }}</div>
                   <div class="col-sm-1 product__cell">
                       {{--Ссылки--}}
                       <a href="/{{ $table }}/{{ $item['id'] }}">Подробно</a>
                       {{--если админ --}}
                       @if ($admin)
                           <a href="/{{ $table }}/edit/{{ $item['id'] }}">Редактор</a>
                           <a href="/{{ $table }}/delete/{{ $item['id'] }}">Удалить</a>
                       @endif

                   </div>


               </div>
               @endforeach
               <div class="product__pagination">
                   {!! $modelData->render() !!}
               </div>
            </div>
        </div>
    </div>

        {{--</div>--}}

@endsection
