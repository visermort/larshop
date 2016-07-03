<div class="col-md-4 model-menu">
    <div class=" navbar">
        <ul class="nav nav-pills nav-stacked">
            <li>
                <a class="btn model-menu__link"  href="/manager/site" role="button" >
                    Сайт
                </a>
            </li>
            <li role="presentation"  class="dropdown">

                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    Категории товаров <span class="caret"></span>
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