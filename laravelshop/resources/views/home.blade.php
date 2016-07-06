@extends('layouts.app')

@section('content')

    @include ('common.mainmenu');

    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">

                    <div class="panel-heading">{{ $siteName or "Название сайта" }}</div>

                    <div class="panel-body">
                            <p> Добро пожаловать на начальную станицу магазина </p>
                        <p> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi at, consectetur ducimus est impedit natus nulla quis quisquam reiciendis repellat similique totam. Eligendi est nemo nulla reiciendis suscipit tenetur veniam?</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
