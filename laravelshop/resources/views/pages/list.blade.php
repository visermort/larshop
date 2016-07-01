@extends('layouts.app')

@section('content')
    {{--главное меню  - не на всех страницах--}}
    @include('common.mainmenu')

    <div class="container">
       <div class="col-sm-2  panel panel-default">
            <div class="panel-heading">
                Фильтр
            </div>
            <div class="filter">
                будущий фильтр
            </div>

        </div>

        <div class="col-sm-10  products panel panel-default">

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
                   <div class="col-sm-1 product__cell">Ссылки</div>


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
