<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-success navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('login') }}" class="nav-link">Home</a>
        </li>
        <!-- <li class="nav-item d-none d-sm-inline-block">
          <a href="#" class="nav-link">Contact</a>
        </li> -->
    </ul>


    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- keyboard shortcut -->
        {{-- <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-keyboard"></i>
                Keyboard Shortcut
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item">Warehouse Sale => Altr+W</span>
                <div class="dropdown-divider"></div>
                <span class="dropdown-item">Local Van Sale => Altr+L</span>
                <div class="dropdown-divider"></div>
                <span class="dropdown-item">Outer Van Sale => Altr+O</span>
                <div class="dropdown-divider"></div>
                <span class="dropdown-item">Save => Altr+S</span>
                <div class="dropdown-divider"></div>
                <span class="dropdown-item">Clear => Altr+C</span>
                <div class="dropdown-divider"></div>
            </div>
        </li> --}}

        {{-- <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

                <div class="dropdown-divider"></div>
                <a href="{{ route(Request::segment(1) . '.chats.index') }}" class="dropdown-item dropdown-footer">See
                    Messages</a>
            </div>
        </li> --}}


        <!-- Notifications Dropdown Menu -->
        {{-- <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge" id="seennotify">{{ auth()->user()->unreadNotifications->count() }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">{{ auth()->user()->unreadNotifications->count() }}
                    Notifications</span>
                <div class="dropdown-divider"></div>
                @foreach (auth()->user()->unreadNotifications as $notification)

                    <span class="dropdown-item" style="white-space: inherit !important">
                        {!! $notification->data['data'] !!}
                        <i class="fas fa-clock"></i> {{ $notification->created_at->diffForHumans() }}
                    </span>
                    <div class="dropdown-divider"></div>


                @endforeach

        </li> --}}
        <li class="nav-item d-none d-sm-inline-block">
            <span class="nav-link">Supplier => Altr+S</span>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <span class="nav-link">Customer => Altr+C</span>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <span class="nav-link">Purchase => Altr+P</span>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <span class="nav-link">Sale => Altr+V</span>
        </li>
        <!-- Login -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user"></i> {{ @Auth::user()->name }}
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                {{-- <a class="dropdown-item" href="#"> <i
                    class="fas fa-user"></i> {{ Auth::user()->name }}
                </a> --}}
                <a class="dropdown-item" href="{{ route(Request::segment(1) . '.changepassword') }}"> <i
                        class="fas fa-lock"></i> Change Password
                </a>
                <a class="dropdown-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
              document.getElementById('logout-form').submit();"> <i
                        class="fas fa-key"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>

    </ul>
</nav>
<!-- /.navbar -->
