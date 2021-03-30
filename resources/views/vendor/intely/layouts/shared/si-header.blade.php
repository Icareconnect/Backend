<header>
    <div class="navigation-wrap">
        <div class="container">
            <div class="row align-items-center header-sec">
                <div class="col-6">
                  <a class="navbar-brand" href="{{ url('/')}}"><img src="{{ asset('assets/mp2r/images/ic_logo.png') }}" alt=""> </a>
				</div>
                    <div class="col-6"> 
                        <div class="right-content">
                           <p class="pull-right">{{$completed??'0%'}} complete 
                            @if(Auth::check())
                            <a href="{{ route('logout') }}">Logout</a>
                            @endif
                           </p>
                        </div>
					</div>
            </div>
        </div>
    </div>
</header>