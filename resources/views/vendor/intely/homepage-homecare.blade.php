@extends('vendor.intely.layouts.index', ['title' => 'Home Care'])
@section('content')
  <section class="bannar-section banner-secound d-flex align-items-center">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12 banner-content text-center">
          <h2 class="">iCareConnect</h2>
          <h2 class="my-4 pb-3">Quality care by caring hearts</h2>
          <a class="btn apply-now d-inline-block" href="{{ config::get('builds')->ios_url['user_url'] }}" target="__blank"><span>Apply Now</span></a>
          <!-- <button class="btn"><span>Apply now</span></button> -->
        </div>
      </div>
    </div>
  </section>

  <!-- Content Area Begin From Here -->
  <div class="content-area">
    <!-- First Section Strat -->
    <section class="home-first-section pb-0">
      <div class="container">
        <h1 class="text-38 text-center mb-lg-5 mb-4">How it works?</h1>
        <div class="row align-items-center">
          <div class="col-lg-4">
            <div class="work_text d-flex align-items-center">
              <label class="no-left">1</label>
              <h3 class="text-20">Download our  mobile app</h3>
            </div>
            <div class="work_text d-flex align-items-center">
              <label class="no-left">3</label>
              <h3 class="text-20">Enter your shift requirements</h3>
            </div>
            
            <div class="work_text d-flex align-items-center">
              <label class="no-left">5</label>
              <h3 class="text-20">Book your preferred healthcare professional</h3>
            </div>
            <div class="work_text d-flex align-items-center">
              <label class="no-left">7</label>
              <h3 class="text-20">In app call functionality to contact your nurse for last minute updates</h3>
            </div>
          </div>
          <div class="col-lg-4 text-center">
            <img class="img-fluid" src="{{ asset('assets/intely/images/ic_howitwork.png') }}" alt="">
          </div>
          <div class="col-lg-4">
            <div class="work_text d-flex align-items-center">
              <label class="no-left">2</label>
              <h3 class="text-20">Answer basic personal information </h3>
            </div>
            <div class="work_text d-flex align-items-center">
              <label class="no-left">4</label>
              <h3 class="text-20">View full profiles, credentials and rates of all healthcare support staff on our team </h3>
            </div>
            <div class="work_text d-flex align-items-center">
              <label class="no-left">6</label>
              <h3 class="text-20">Relax, our GPS notification lets you know when your nurse is on their way</h3>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Our Servcies Section Begin Form Here -->
    <section class="our_servcies bg-light-gray p-75 pt-0">
      <div class="container">
        <div class="row">
          <div class="col text-center">
              <div class="text-38 mb-lg-5 mb-4">Our Servcies</div>
              <p class="text-22">For families, iCareConnect offers a full range of exceptional quality support and home care services for those with physical, medical, or cognitive impairment. We recognize that specialized care is required in certain situations and we offer selected and trained personnel who will make a meaningful difference to your well-being and recovery.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Home Update Section -->
    <section class="real-time bg-light-gray p-75 pt-0">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6 pr-lg-5">
            <div class="text-38 mb-4">Specialized Care</div>
            <p class="text-22">Specialized care is an important part of our overall health-care package and we offer specialized and trained nurses for diabetes, palliative, arthritis, cancer, car accidents, disabilities, wound care, post-surgical care and more.</p>
          </div>
          <div class="col-md-6 pl-lg-5">
            <img class="img-fluid" src="{{ asset('assets/intely/images/ic_specialized_care.png') }}" alt="">
          </div>
        </div>
      </div>
    </section>

    <!-- Audit Preparation Section -->
    <section class="audit-preparation p-0">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6 pr-lg-5 text-center">
            <img class="img-fluid" src="{{ asset('assets/intely/images/ic_palliative_care.png') }}" alt="">
          </div>
          <div class="col-md-6 pl-lg-5">
            <div class="text-38 mb-4">Hospice & Palliative Care </div>
            <p class="text-22">Our Hospice Palliative care team provides support and care for patients who have a life-limiting illness. Our nurses provide a compassionate and comprehensive care to improve the quality of life for our patients and their families as they deal with end of life issues and situations.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Approve Process Section -->
    <section class="approve-process bg-light-gray p-75">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6 pr-lg-5">
            <div class="text-38 mb-4">Companionship</div>
            <p class="text-22">Our healthcare companionship team will provide extra support services when you need it the most. This could allow you to take some time to yourself or can provide you with some extra support while you want to enjoy a much-needed vacation.</p>
          </div>
          <div class="col-md-6 pl-lg-5">
            <img class="img-fluid" src="{{ asset('assets/intely/images/ic_companionship.png') }}" alt="">
          </div>
        </div>
      </div>
    </section>

   <!-- Audit Preparation Section -->
    <section class="audit-preparation p-75 pb-0">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6 pr-lg-5 text-center">
            <img class="img-fluid" src="{{ asset('assets/intely/images/ic_personal_support.png') }}" alt="">
          </div>
          <div class="col-md-6 pl-lg-5">
            <div class="text-38 mb-4">Personal Support</div>
            <p class="text-22">We provide personal care support ranging from dressing, bathing, oral care, bathroom and meal support. We also provide support for active daily living (ADLs), post-surgical, and bed bound clients.</p>
          </div>
        </div>
      </div>
    </section>

  </div>
  
 <!-- No Fees -->
 <section class="no-fees-cmitmnt">
   <div class="container">
     <div class="row">
       <div class="col-md-12 text-center">
         <h3>Get ready to experience our quality care</h3>
         <p class="text-28 my-lg-4 my-3 pb-lg-2">Sign up today!</p>
         <a class="mr-3" target="__blank" href="{{ config::get('builds')->android_url['user_url'] }}"><img src="{{ asset('assets/intely/images/ic_google.png') }}" alt=""></a>
         <a class="mr-3" target="__blank" href="{{ config::get('builds')->ios_url['user_url'] }}"><img src="{{ asset('assets/intely/images/ic_apple.png') }}" alt=""></a>
       </div>
     </div>
   </div>
 </section>
  @endsection
