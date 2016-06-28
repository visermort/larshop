@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            {{ dd($viewData) }}
            {{--{{ dd($structure) }}--}}
           {{--@foreach ($viewData as $item)--}}
            {{--<p>--}}
             {{--{{ $item['name'] }}--}}
            {{--</p>--}}
            {{--<p>--}}
                {{--{{ $item['description'] }}--}}
            {{--</p>--}}
           {{--@endforeach--}}

        </div>
    </div>
@endsection
