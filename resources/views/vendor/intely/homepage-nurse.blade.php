@extends('vendor.intely.layouts.index', ['title' => 'Nursing Professionals'])
@section('content')
<link rel="stylesheet" href="{{ asset('assets/healtcaremydoctor/css/intlTelInput.css') }}">
  <!-- Bannar Section -->
  <section class="bannar-section banner-nursing d-flex align-items-center">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12 banner-content text-center">
          <h2 class="">For Nursing Professionals</h2>
          <p class="text-22 py-3">Transform the way you work.</p>
          <a class="btn apply-now d-inline-block" href="{{ config::get('builds')->ios_url['sp_url'] }}" target="__blank"><span>Apply Now</span></a>
          <!-- <button class="btn"><span>Apply now</span></button> -->
        </div>
      </div>
    </div>
  </section>

  <!-- Content Area Begin From Here -->
  <div class="content-area">
    

    <!-- Schedule Flexibility Section -->
    <section class="schedule_flexibility bg-light-gray p-75">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6 pr-lg-5">
            <img class="img-fluid" src="{{ asset('assets/intely/images/ic_schedule.png') }}" alt="">
          </div>
          <div class="col-md-6 pl-lg-5">
            <div class="text-38 mb-4">Schedule Flexibility </div>
            <p class="text-22">Take control of your schedule and work to fit your lifestyle.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Competitive Rates Section -->
    <section class="competitive_Rates p-75">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6 pr-lg-5">
            <div class="text-38 mb-4">Competitive Rates</div>
            <p class="text-22">Our rates are highly competitive. We value our front line heroes and we pay bonus for set cumulative hours.</p>
          </div>
          <div class="col-md-6 pl-lg-5 text-center">
            <img class="img-fluid" src="{{ asset('assets/intely/images/ic_competitive_rates.png') }}" alt="">
          </div>
        </div>
      </div>
    </section>

    <!-- how it work Section -->
    <section class="how_it_work bg-light-gray p-75">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-12 text-center mb-5 pb-lg-4">
            <div class="text-32">How it works?</div>
          </div>
          <div class="col-md-5 pr-lg-5">
            <img class="img-fluid" src="{{ asset('assets/intely/images/ic_phone.png') }}" alt="">
          </div>
          <div class="col-md-7">
            <div class="text-26 how-head mb-4">Simplified Registration Steps</div>
              <div class="onlie_box">
                <h5 class="text-26">1)   Register Online</h5>
                <p class="text-22">Download the iCareConnect mobile app</p>
              </div>
              <div class="onlie_box">
                <h5 class="text-26">2)   Upload Documents</h5>
                
                <div class=" row no-gutters row-spacing pl-3">
                  <div class="col-sm-6">
                    <div class="document_list position-relative">
                      <i class="fas fa-check-circle green_round position-absolute"></i>
                      <p class="text-22 m-2">Resume</p>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="document_list position-relative">
                      <i class="fas fa-check-circle green_round position-absolute"></i>
                      <p class="text-22 m-2">Practice License</p>
                    </div>
                  </div>
                </div>

                <div class=" row no-gutters row-spacing pl-3">
                  <div class="col-sm-6">
                    <div class="document_list position-relative">
                      <i class="fas fa-check-circle green_round position-absolute"></i>
                      <p class="text-22 m-2">Employer Reference</p>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="document_list position-relative">
                      <i class="fas fa-check-circle green_round position-absolute"></i>
                      <p class="text-22 m-2">Tax Form( T4A /T4)</p>
                    </div>
                  </div>
                </div>

                <div class=" row no-gutters row-spacing pl-3">
                  <div class="col-sm-6">
                    <div class="document_list position-relative">
                      <i class="fas fa-check-circle green_round position-absolute"></i>
                      <p class="text-22 m-2">Direct Deposit</p>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="document_list position-relative">
                      <i class="fas fa-check-circle green_round position-absolute"></i>
                      <p class="text-22 m-2">Social Insurance Number</p>
                    </div>
                  </div>
                </div>

                <div class=" row no-gutters row-spacing pl-3">
                  <div class="col-sm-6">
                    <div class="document_list position-relative">
                      <i class="fas fa-check-circle green_round position-absolute"></i>
                      <p class="text-22 m-2">Policy Acknowledgment</p>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="document_list position-relative">
                      <i class="fas fa-check-circle green_round position-absolute"></i>
                      <p class="text-22 m-2">Health Records</p>
                    </div>
                  </div>
                </div>

                <div class=" row no-gutters row-spacing pl-3">
                  <div class="col-sm-6">
                    <div class="document_list position-relative">
                      <i class="fas fa-check-circle green_round position-absolute"></i>
                      <p class="text-22 m-2">Schedule Interview</p>
                    </div>
                  </div>
                </div>

                <div class="row mt-lg- mt-4">
                  <div class="col">
                    <h5 class="text-20 mb-3">Download iCareConnect mobile app</h5>
                    <div class="search_box download-app mb-4 d-flex align-items-center justify-content-between mr-4">
                      <div class="download-search d-flex align-items-center justify-content-start">
                        <label class="d-flex align-items-center m-0">
                        <input type="tel" id="phone" name="phone">
                      </div>
                      <button class="btn" id="send_link"> <span>Send Link</span></button>
                    </div>
                    <a class="mr-3" href="{{ config::get('builds')->android_url['sp_url'] }}" target="__blank"><img src="{{ asset('assets/intely/images/ic_google.png') }}" alt=""></a>
                    <a class="mr-3" href="{{ config::get('builds')->ios_url['sp_url'] }}" target="__blank"><img src="{{ asset('assets/intely/images/ic_apple.png') }}" alt=""></a>
                  </div>
                </div>

              </div>
          </div>
        </div>
      </div>
    </section>

   <!-- Nursing Professionals Section -->
    <section class="nursing_professionals bb-85">
      <div class="container">
        <div class="row">
          <div class="col text-center">
            <div class="text-28 mb-lg-4 mb-3">Nursing Professionals</div>
            <div class="text-32 mb-lg-5 mb-4">Personalize your page</div>
          </div>
        </div>
        <div class="row align-items-center">
          <div class="col-md-6 pr-lg-5">
              <div class="text-26 mb-2">Update Shift Filters</div>
              <p class="text-22 mb-lg-4">What are your preferred shift times? How far from home are you willing to travel? Are there specific types of shifts or facilities you prefer?</p>
              
              <div class="text-26 mb-2">Turn on Notifications</div>
              <p class="text-22 mb-lg-4">Notifications will update you on your shifts and important messages.</p>

              <div class="text-26 mb-2">Opt-in to Shift Premiums</div>
              <p class="text-22 mb-lg-4">Shift premiums include short notice, night, double and on-call shifts.</p>

              <div class="text-26 mb-2">Tracking</div>
              <p class="text-22 mb-lg-5 mb-4">View your work history and how much you’ve earned.</p>

              <a class="btn apply-now d-inline-block" href="{{ config::get('builds')->ios_url['sp_url'] }}" target="__blank"><span>Apply Now</span></a>
          </div>
          <div class="col-md-6 pl-lg-5 text-center">
            <img class="img-fluid" src="{{ asset('assets/intely/images/ic_screen.png') }}" alt="">
          </div>
        </div>
      </div>
    </section>

  </div>
  
 <!-- Get Experience -->
 <section class="get_experience py-md-5 py-4">
   <div class="container">
     <div class="row">
       <div class="col-md-12 text-center">
         <h3>Get ready to experience our quality care</h3>
         <p class="text-28 my-lg-4 my-3 pb-lg-2">Start your application by downloading our mobile app and one of care coordinators will connect with you</p>         
         <a class="btn apply-now d-inline-block" href="{{ config::get('builds')->ios_url['sp_url'] }}" target="__blank"><span>Apply Now</span></a>
       </div>
     </div>
   </div>
 </section>
  <script src="{{ asset('assets/healtcaremydoctor/js/intlTelInput.js') }}"></script>
  <script>
      var input = document.querySelector("#phone");
      window.intlTelInput(input, {
          utilsScript: "{{ asset('assets/healtcaremydoctor/js/utils.js') }}",
          preferredCountries: ["ca","us"],
      });
      if($('#phone').get(0)!==undefined){
          var iti = intlTelInput($('#phone').get(0));
      }
  </script>
  @endsection
