<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Styles -->
    {!! Html::style(elixir('css/vendor.css')) !!}
    {!! Html::style(elixir('css/app.css')) !!}
    @stack('style')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">{{ trans('front-end/home.labels.toggle-nav') }}</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            {!! 
                                link_to_action(
                                    'Web\RoomsController@index', trans('front-end/room.waiting-hall'), [], []
                                ) 
                            !!}
                        </li>
                        <!-- Authentication Links -->

                        @if (Auth::guest())
                            <li><a href="{{ url('/login') }}">{{ trans('front-end/home.buttons.login') }}</a></li>
                            <li><a href="{{ url('/register') }}">{{ trans('front-end/home.buttons.register') }}</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    @can('accessAdmin', User::class)
                                        <li>
                                            {{ link_to_action('Admin\HomeController@index', trans('admin/home.buttons.admin-cp') , null, null) }}
                                        </li>
                                    @endcan
                                    <li>
                                        {{ link_to_action('Web\UsersController@show', trans('front-end/profile/show.labels.profile'), Auth::user()->id, null) }}
                                    </li>
                                    <li>
                                        <a href="{{ url('/logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            {{ trans('front-end/home.buttons.logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <!-- Scripts -->
    @stack('script')
    {!! Html::script(elixir('js/app.js')) !!}
    {!! Html::script(elixir('js/vendor.js')) !!}
    @stack('datatables')
</body>
</html>
