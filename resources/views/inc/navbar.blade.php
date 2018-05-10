
<nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    UoW Event Booking
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                  <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        @if(Auth::check() && auth()->user()->role >= 2)
                            <a class="nav-link" href="/studentDashboard">Home <span class="sr-only">(current)</span></a>
                        @else
                            <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
                        @endif
                    </li>
                    <li class="nav-item active">
                      <a class="nav-link" href="https://www.uow.edu.au/student/life/index.html">UoW Student Life</a>
                    </li>

                    @if(Auth::check() && auth()->user()->role==0)
                        <li class="nav-item active">
                          <a class="nav-link" href="/statistics">View statistics</a>
                        </li>
                    @elseif(Auth::check() && auth()->user()->role == 2)
                        <li class="nav-item active">
                          <a class="nav-link" href="/subscription">Subscription</a>
                        </li>
                    @endif


                    <li class="nav-item dropdown show">
                  <a class="nav-link dropdown-toggle" id="id3" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Dropdown link
                  </a>
                  <div class="dropdown-menu" aria-labelledby="id3">
                    <a class="dropdown-item" >Action</a>
                    <a class="dropdown-item">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                  </div>
                    </li>
                  </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                            <li><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <a class="dropdown-item" href="/settings">
                                        Account Setting
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>