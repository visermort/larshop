@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
{{--форма редактирования--}}
         {{--{{ dd($viewData) }}--}}
{{--            {{dd($structure)}}--}}
            <form action="/{{ $table }}/save/{{ $viewData['id'] }}" method="post" enctype="multipart/form-data" id="form-edit">
                {{--проходим по всем элементам струкутры и формируем инпуты--}}
                @foreach ($structure as $key => $item)
                <div class="formgroup ">
                    <div class="col-sm-3">{{ $item['title'] }}</div>
                    <div class="col-sm-9">
                        @if ($item['type'] == 'image')
                                            место для картинки
                        @elseif ($item['type'] == 'select')
                                    место для select
                        @elseif ($item['type'] == 'textarea')
                            <textarea name="{{ $key }}">{{ $viewData[$key] }}</textarea>
                        @else
                            <input type="text" name="{{ $key }}" value="{{ $viewData[$key] }}" >
                        @endif


                    </div>

                </div>
                @endforeach

            </form>
        </div>
    </div>
@endsection
