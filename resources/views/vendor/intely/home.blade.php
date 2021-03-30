@extends('vendor.intely.layouts.index', ['title' => 'Home'])
@section('content')
  <!-- Bannar Section -->
  <section class="bannar-section">
    <div class="container">
      <div class="row">
        <div class="offset-md-1 col-md-10 text-center">
          <h2 class="pl-4 pr-4">Quality Care by Caring Hearts</h2>
          <p>iCareConnect is Canada’s leading online workforce management platform that matches fully-vetted, employment ready healthcare professionals to Health Care facilities, Long term care homes and private homes.<br>Our platform utilizes smart-matching capabilities that enables facilities and private homes to argument and optimize their staffing needs.</p>
          <!-- <button class="btn"><span>Get Started</span></button> -->
          <a class="btn apply-now d-inline-block" href="#Health"><span>Get Started</span></a>
        </div>
      </div>
    </div>
  </section>
  <!-- Next Section -->
  <section class="benifits-section">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="benifits-part">
            <div class="benifit">
              <div class="row">
                <div class="col-md-4 text-center">
                  <img src="{{ asset('assets/intely/images/ic_fullcoverage.png') }}">
                  <h4 class="mt-3">Full Coverage</h4>
                  <p class="mb-0">We provide high quality healthcare professionals for your last minute or scheduled shifts on an on demand basis.</p>
                </div>
                <div class="col-md-4 text-center">
                  <img src="{{ asset('assets/intely/images/ic_transparency.png') }}">
                  <h4 class="mt-3">Transparency</h4>
                  <p class="mb-0">View full profile and credentials of every healthcare professional before they arrive at your facility or home.</p>
                </div>
                <div class="col-md-4 text-center">
                  <img src="{{ asset('assets/intely/images/ic_smart_technology.png') }}">
                  <h4 class="mt-3"> Smart Matching Technology</h4>
                  <p class="mb-0">Our platform uses innovative and smart matching technology to connect healthcare facilities and families with quality health care providers on demand</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Next Section -->
  <section class="scheduling-process" id="Health">
    <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          <small>Health Care Facilities</small>
          <h3 class="mt-2">Simplify your scheduling process</h3>
        </div>
        <div class="col-md-5">
          <img class="img-fluid" src="{{ asset('assets/intely/images/ic_1.png') }}">
        </div>
        <div class="col-md-7">
          <div class="pl-3">
            <p>With a few clicks on your customized portal, our intelligent matching solution will display a list of available personnel in your local area.</p>
          <p>Our platform takes the stress out of running your operations so that you can focus on providing quality care.</p>
            <a class="btn apply-now d-inline-block" href="{{ url('web/facility') }}"><span>Learn More</span></a>
          </div>
        </div>
      </div>
    </div>
  </section>
 <!-- Next Section  -->
 <section class="nursing-professionals" id="Nursing">
   <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          <small>Nursing Professionals</small>
          <h3 class="mt-2">Take control of your schedule</h3>
        </div>
        <div class="col-md-7">
          <div class="pl-3">
            <p>Are you a passionate health care professional that wants to have flexible work hours? Our mobile app allows you to browse through many available shifts that is tailored around your schedule, preferences and work experience.</p>
          <button class="btn mt-4" onclick="location.href=`{{ route('nurse-prof') }}`"><span>Learn More</span></button>
          </div>
        </div>
        <div class="col-md-5">
          <img class="img-fluid" src="{{ asset('assets/intely/images/ic_2.png') }}">
        </div>
      </div>
    </div>
 </section>
 <!-- Next Section -->
  <section class="scheduling-process">
    <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          <small>Home Care</small>
          <h3 class="mt-2">Quality care close to home</h3>
        </div>
        <div class="col-md-5">
          <img class="img-fluid" src="{{ asset('assets/intely/images/ic_3.png') }}">
        </div>
        <div class="col-md-7">
          <div class="pl-3">
            <p>iCareConnect is your non-traditional staffing platform that provides you with a wide variety of healthcare support services on an on-demand basis. We pride ourselves in providing services within a few hours to meet your last minute requests.</p>
          <p>Our team consists of highly qualified and fully credentialed health care professionals who are ready to work on demand.</p>
          <button class="btn mt-4" onclick="location.href=`{{ route('homecare') }}`"><span>Learn More</span></button>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Next Section  -->
 <section class="nursing-professionals eleminate-staffing" id="healthcareF">
   <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          <small>Health Care Facilities</small>
          <h3 class="mt-2">Eliminate your staffing challenges</h3>
        </div>
        <div class="col-md-6">
          <div class="pl-3">
            <h5>Real time notifications</h5>
            <p>Use our platform to see where your icareconnect nurses are and when to expect them to arrive at your facility.</p>
            <h5>Profile Overview</h5>
            <p>Get full visibility of the nursing professional coming to your facility. See their work history, credentials, and certifications.</p>
            <h5>No Hidden Fees</h5>
            <p>No agency fees. No lock-in contract, no setup costs and only pay for hours of service.</p>
          <a class="btn apply-now d-inline-block" href="{{ url('web/facility-form') }}"><span>Request a demo</span></a>
          </div>
        </div>
        <div class="col-md-6">
          <!-- <img class="img-fluid" src="{{ asset('assets/intely/images/desktop.png') }}"> -->
        </div>
      </div>
    </div>
 </section>
 <!-- Next Section  -->
 <section class="nursing-professionals eleminate-staffing bg-white">
   <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          <small>Nursing Professionals</small>
          <h3 class="mt-2">You are in control of your schedule</h3>
        </div>
        <div class="col-md-6">
          <img class="img-fluid" src="{{ asset('assets/intely/images/ic_howitwork.png') }}">
        </div>
        <div class="col-md-6">
          <div class="pl-3">
            <h5>Schedule Flexibility</h5>
            <p>Take control of your schedule. Work only when you want to.</p>
            <h5>Competitive Pay</h5>
            <p>Our rates are competitive and we offer bonus pay for every milestones attained</p>
            <h5>Paperless Billing </h5>
            <p>We offer a technology that enables you to check-in and out and submit time sheet through your app. Time tracking hours has never been easier.</p>
            <a class="btn apply-now d-inline-block" href="{{ config::get('builds')->ios_url['sp_url'] }}" target="__blank"><span>Apply Now</span></a>
          </div>
        </div>
      </div>
    </div>
 </section>
 <!-- Next Section  -->
 <section class="nursing-professionals eleminate-staffing" id="homeCareId">
   <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          <small>Home Care</small>
          <h3 class="mt-2"> The heart of your care</h3>
        </div>
        <div class="col-md-6">
          <div class="pl-3">
            <h5>Security Clearance</h5>
            <p>Our Healthcare workers are insured and bonded, with yearly local and federal background checks to put your mind at rest. Our healthcare workers undergo continuous education to keep abreast of developments in the health care industry. </p>
            <h5>Profile Overview</h5>
            <p>Review the profile of your selected health care professionals before they arrive in your home.</p>
            <h5>GPS Notification</h5>
            <p>Relax, GPS notification lets you know when your nurse is on the way.</p>
            <!-- <a class="btn apply-now d-inline-block" href="{{ config::get('builds')->ios_url['user_url'] }}" target="__blank"><span>Sign Up</span></a> -->
          <!-- <button class="btn mt-4"><span>Sign Up</span></button> -->
          </div>
        </div>
        <div class="col-md-6">
          <img class="img-fluid mx-auto d-block" src="{{ asset('assets/intely/images/bak.png') }}">
        </div>
      </div>
    </div>
 </section>
 <!-- No Fees -->
 <section class="no-fees-cmitmnt">
   <div class="container">
     <div class="row">
       <div class="col-md-12 text-center">
         <h3>No fees or commitment necessary</h3>
         <p class="mb-0">No nursing agency fees, no lock-in contract, no setup costs and only pay for hours of service. Requesting a nursing professional has never been easier.</p>
       </div>
     </div>
   </div>
 </section>
@endsection