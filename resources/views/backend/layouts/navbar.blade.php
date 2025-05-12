<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item">
        <a class="btn btn-sm btn-primary nav-link" style="color: white;" href="{{ url()->previous() }}">Back</a>
    </li>
 
</ul>



<ul class="navbar-nav ml-auto">
  
    <li class="nav-item dropdown show">
        <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="true">
            <i class="fa fa-user"></i> {{auth()->user()->name}}
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            {{-- <a href="{{route('profile-management.change.password')}}" class="dropdown-item" >
                <i class="fas fa-lock"></i> Change Password
            </a> --}}
            <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-lock"></i> Logout
            </a>
            <form style="display: none;" method="post" id="logout-form" action="{{route('logout')}}">
                @csrf
            </form>
        </div>
    </li>
</ul>
