@if(!isset($after_signup))
<header class="fixed-top">
    <nav class="navbar navbar-expand-lg navbar-light container">
      <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ asset('assets/intely/images/ic_logo.png') }}"></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto display-flex align-items-center">
          <li class="nav-item {{ Request::is('web/facility') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('web/facility') }}">Health Care Facility</a>
          </li>
          <li class="nav-item {{ Request::is('web/nurse-professionals') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('nurse-prof') }}">Nursing Professionals</a>          
          </li>
          <li class="nav-item {{ Request::is('web/homecare') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('homecare') }}">Home Care</a>          
          </li>
          <li class="nav-item {{ Request::is('web/covid-19') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('covid-19') }}">Covid-19</a>          
          </li>
          <li class="nav-item {{ Request::is('web/about-us') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('about-us') }}">About Us</a>          
          </li>
          <li class="nav-item {{ Request::is('web/contact-us') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('contact-us') }}">Contact Us</a>          
          </li>
          @if(!Auth::Check())
          <li class="nav-item" data-user="{{ Auth::guard('web')->check() }}">
            <button class="mr-3" data-toggle="modal" data-target="#login">Login</button>        
          </li>
          <!-- <li class="nav-item">
            <button  class="signup-modal" data-toggle="modal" data-target="#signup-modal"> Sign Up</button>
          </li> -->
          @elseif(Auth::user()->hasrole('service_provider') || Auth::user()->hasrole('customer'))
          <li class="nav-item" data-user="{{ Auth::guard('web')->check() }}">
            <a class="mr-3" href="{{ url('web/profile') }}">{{ Auth()->user()->name }}</a>        
          </li>
          @endif
        </ul>
    </div>
  </nav>
</header>
<!-- Modal -->
<div class="sign_up_content">
  <div class="modal fade" id="signup-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
        <div class="modal-content border-0 bg-transparent">
           <div class="modal-header text-center border-0 p-0">
              <h3 class="modal-title m-auto" id="sign_in_popupLabel">Welcome to iCareConnect! </h3>
              <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <div class="modal-body border-0 px-sm-5 pb-0 pt-lg-5 mt-lg-5">
              <h4 class="text-24 text-center">Who you are?</h4>
              <ul class="sign_up_list three-column d-flex align-items-center justify-content-between">
                <li>
                  <a href="#">
                    <img src="{{ asset('assets/intely/images/ic_nurse_1.png') }}" alt="">
                    <p>Nurse</p>
                  </a>
                </li>
                <li class="active" data-toggle="modal" data-target="#facility-modal">
                  <a href="#">
                    <img src="{{ asset('assets/intely/images/ic_hospital.png') }}" alt="">
                    <p>Facility</p>
                  </a>
                </li>
                <li>
                  <a href="#">
                    <img src="{{ asset('assets/intely/images/ic_homecare_1.png') }}" alt="">
                    <p>Homecare</p>
                  </a>
                </li>
              </ul>
           </div>
        </div>
     </div>
</div>  
</div>
 <!-- Modal -->
<div class="modal login fade" id="login" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header border-0 p-0 mb-4">
          <h3 class="modal-title modal-heading m-auto" id="">Welcome to iCareConnect! </h3>
          <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body border-0 p-0">
          <h4 class="text-center">Login Here</h4>
          <form class="login-form" id="ic__login">
            <!-- <input type="hidden" name="user_type" value="service_provider"> -->
            <input type="hidden" name="domain" value="intely">
            <div class="form-group group mb-lg-5">
              <input required="" id="email" name="email" class="input" type="email">
              <span class="highlight"></span><span class="bar"></span>
              <label class="label m-0" for="email">Email Address</label>
              <span class="alert-danger email_error"></span>
            </div>
            <div class="form-group group mb-lg-5">
              <input required="" id="password" name="password" class="input" type="password" >
              <span class="highlight"></span><span class="bar"></span>
              <label class="label m-0" for="password">Password</label>
              <span class="alert-danger password_error"></span>

            </div>
            <div class="form-group mt-4 mb-3 pt-3 text-center">
              <span class="alert-danger main_error"></span>
              <button class="btn form-btn w-100 login_btn_text"><span>Submit</span></button>
            </div>
            <a class="forget_btn text-center d-block" href="#">Forgot Password?</a>
          </form>
        </div>
      </div>
  </div>
</div>
<div class="form_one_content">
  <div class="modal fade" id="facility-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 bg-transparent">
           <div class="modal-header text-center border-0 p-0">
              <h3 class="modal-title m-auto" id="sign_in_popupLabel">No sign-up fees or commitment necessary.</h3>
              <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <div class="modal-body border-0 mt-3">
              <h4 class="text-22 text-center">Schedule your demo today!</h4>
              <form class="mt-lg-5 pt-lg-4">
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <label>First Name</label>
                      <input class="form-control" type="" name="Last" id="First" placeholder="">
                    </div>
                    <div class="col-md-6">
                      <label>Last Name</label>
                      <input class="form-control" type="" name="Last" id="Last" placeholder="">
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <label>Email Address</label>
                      <input class="form-control" type="" name="" id="iem" placeholder="">
                    </div>
                    <div class="col-md-6">
                      <label>Phone Number</label>
                      <input class="form-control" type="" name="" id="iem2" placeholder="">
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="row row-spacing no-gutters">
                    <div class="col">
                      <label>Facility Name</label>
                      <input class="form-control" type="" name="" id="fc" placeholder="">
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="row row-spacing no-gutters">
                    <div class="col">
                      <label>Job Title</label>
                      <input class="form-control" type="" name="" id="jb" placeholder="">
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <label>City</label>
                      <input class="form-control" type="" name="" id="City" placeholder="">
                    </div>
                    <div class="col-md-6">
                      <label>Province</label>
                      <select class="form-control" name="" id="Province">
                        <option value=""></option>
                        <option value="">Province</option>
                        <option value="">Province</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="row row-spacing no-gutters">
                    <div class="col">
                      <label>Job Title</label>
                      <input class="form-control" type="" name="" id="job" placeholder="">
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="row row-spacing no-gutters">
                    <div class="col">
                      <label>Comments</label>
                      <textarea class="form-control" name="" id="Comments" cols="30" rows="5"></textarea>
                    </div>
                  </div>
                </div>

                <div class="form-group mt-4 pt-3 text-center">
                  <a href="{{ route('web-dashboard')}}" class="btn form-btn"><span>Submit</span></a>
                </div>

              </form>
           </div>
        </div>
     </div>  
   </div>  
</div> 
@else
   <!-- Navigation-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-white fixed-top" id="mainNav">
    <a class="navbar-brand" href="{{ (!isset($nurse))?url('web/dashboard'):url('web/profile') }} "><img src="{{ asset('assets/intely/images/ic_logo.png')}}" alt=""></a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
        @if(!isset($nurse))
          <li class="nav-item {{ Request::is('web/dashboard') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Dashboard">
            <a class="nav-link" href="{{ route('web-dashboard') }}">            
              <img src="{{ asset('assets/intely/images/icon02.png')}}" alt="">
              <span class="nav-link-text">Dashboard</span>
            </a>
          </li>
          <li class="nav-item {{ Request::is('web/jobs') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Charts">
            <a class="nav-link" href="{{ route('web-jobs') }}">
              <img src="{{ asset('assets/intely/images/icon03.png')}}" alt="">
              <span class="nav-link-text">Jobs</span>
            </a>
          </li>
          <li class="nav-item {{ Request::is('web/nurses') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Tables">
            <a class="nav-link" href="{{ route('web-nurses') }}">
              <img src="{{ asset('assets/intely/images/icon04.png')}}" alt="">
              <span class="nav-link-text">Nurses</span>
            </a>
          </li>
        @else
          <li class="nav-item {{ Request::is('web/profile') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Dashboard">
            <a class="nav-link" href="{{ url('web/profile') }}">            
              <img src="{{ asset('assets/intely/images/icon02.png')}}" alt="">
              <span class="nav-link-text">Profile</span>
            </a>
          </li>
        @endif
      </ul>
      <ul class="navbar-nav ml-auto nav-right align-items-center">
        <li class="mt-1 position-relative">
          <a href="#"><i class="far fa-bell" style="font-size: 24px;"></i></a>
          <span class="notify-no position-absolute">3</span>
        </li>
        <li class="nav-item logout_btn">
          <a class="nav-link" href="{{ route('logout') }}">
            <i class="fas fa-sign-out-alt"></i>Logout</a>
        </li>
      </ul>
    </div>
  </nav>
@endif 