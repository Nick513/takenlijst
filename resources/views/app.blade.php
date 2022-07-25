<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>

        <!-- Metadata -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Title -->
        <title>{{ config('app.name', 'Takenlijst') }}</title>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
        <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.4.0/animate.min.css'>
        <link href="{{ asset('libraries/fontawesome/css/all.css') }}" rel="stylesheet">
        <link href="{{ asset('libraries/menu/menu.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        @yield('customCSS')

    </head>
    <body class="keep-scrolling">
        <div class="h-100" id="app">
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container">
                    <a class="logo d-inline-block nostyle not-selectable" href="{{ url('/') }}">
                        @include('snippets.logo', ['width' => 40, 'height' => 40])
                        <span>Takenlijst</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto">

                            <!-- Authentication Links -->
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                    </li>
                                @endif

                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item">
                                    <a class="nav-link p-0" href="javascript:void(0);">
                                        <div class="search">
                                            <input class="search-input" type="text" placeholder="{{ __('general.search') }}..." />
                                            <div class="symbol">
                                                <svg class="lens">
                                                    <use xlink:href="#lens" />
                                                </svg>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                                                <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="lens">
                                                    <path d="M15.656,13.692l-3.257-3.229c2.087-3.079,1.261-7.252-1.845-9.321c-3.106-2.068-7.315-1.25-9.402,1.83
	s-1.261,7.252,1.845,9.32c1.123,0.748,2.446,1.146,3.799,1.142c1.273-0.016,2.515-0.39,3.583-1.076l3.257,3.229
	c0.531,0.541,1.404,0.553,1.95,0.025c0.009-0.008,0.018-0.017,0.026-0.025C16.112,15.059,16.131,14.242,15.656,13.692z M2.845,6.631
	c0.023-2.188,1.832-3.942,4.039-3.918c2.206,0.024,3.976,1.816,3.951,4.004c-0.023,2.171-1.805,3.918-3.995,3.918
	C4.622,10.623,2.833,8.831,2.845,6.631L2.845,6.631z" />
                                                </symbol>
                                            </svg>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->username }}
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('settings') }}">
                                            <i class="fa-solid fa-cog" style="margin-left: -1px;margin-right: 2px;"></i>
                                            {{ __('Settings') }}
                                        </a>
                                        <a class="dropdown-item logout-button" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fa-solid fa-right-from-bracket"></i>
                                            {{ __('Logout') }}
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest

                            @php $locale = session()->get('locale'); @endphp
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    @switch($locale)
                                        @case('nl')
                                            <img src="{{asset('img/flags/nl.svg')}}" width="25px" alt="">
                                            @break
                                        @case('en')
                                            <img src="{{asset('img/flags/us.svg')}}" width="25px" alt="">
                                            @break
                                        @default
                                            <img src="{{asset('img/flags/nl.svg')}}" width="25px" alt="">
                                    @endswitch
                                    <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    @if($locale !== 'en')
                                        <a class="dropdown-item" href="/lang/en"><img src="{{asset('img/flags/us.svg')}}" width="25px">
                                            <span>{{ __('English') }}</span>
                                        </a>
                                    @endif
                                    @if($locale !== 'nl' && !empty($locale))
                                        <a class="dropdown-item" href="/lang/nl"><img src="{{asset('img/flags/nl.svg')}}" width="25px">
                                            <span>{{ __('Dutch') }}</span>
                                        </a>
                                    @endif
                                </div>
                            </li>

                        </ul>

                    </div>
                </div>
            </nav>
            <main class="py-4">
                @yield('content')
            </main>
        </div>

        <!-- Contains all popups loaded in DOM using JS -->
        <div class="pcontainer"></div>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
        <script src="{{ asset('js/jquery.js') }}"></script>
        <script src="{{ asset('libraries/notify/bootstrap-notify.min.js') }}" type="text/javascript"></script>
        <script src='http://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js'></script>
        <script src="{{ asset('libraries/scrollreveal/scrollreveal.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('libraries/popupoverlay/jquery.popupoverlay.js') }}" type="text/javascript"></script>
        <script src="{{ asset('libraries/menu/menu.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/app.js') }}"></script>
        @include('snippets.notify')
        @yield('customJS')

    </body>
</html>
