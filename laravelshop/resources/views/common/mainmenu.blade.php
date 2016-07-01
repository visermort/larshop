<nav class="navbar navbar-default ">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

        </div>
        <div class="collapse navbar-collapse" id="main-navbar-collapse">
            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-left">
                <li class="dropdown">
                @foreach(config('shop.models') as $menuItem)

                    <li role="presentation">
                        <a class="btn   " href="/{{ $menuItem['href'] }}">
                            <i class="fa fa-list"></i> {{$menuItem['title']}}
                        </a>
                    </li>

                    @endforeach
                    </li>
            </ul>
        </div>
    </div>
</nav>