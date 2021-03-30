@extends('vendor.intely.layouts.index', ['title' => 'Facilities'])
@section('content')
  <!-- Bannar Section -->
  <section class="bannar-section banner-secound d-flex align-items-center">
    <div class="container">
      <div class="row">
        <div class="col-12 banner-content text-center">
          <h2 class="">Canada's leading software platform for your on demand staffing needs</h2>
          <p class="my-4 pb-lg-2">iCareConnect staffing platform enables facilities to augment their staffing needs through our innovative <br class="d-md-block d-none"> and smart technology that matches healthcare professionals and local facilities</p>
          <a class="btn apply-now d-inline-block" href="{{ url('web/facility-form') }}"><span>Request a demo</span></a>
          <!-- <button class="btn"><span>Request a demo</span></button> -->
        </div>
      </div>
    </div>
  </section>

  <!-- Content Area Begin From Here -->
  <div class="content-area">
    <!-- First Section Strat -->
    <section class="home-first-section">
      <div class="container">
        <div class="row">
          <div class="col-lg-4 col-sm-6 text-center thumbnail-text mb-lg-4 mb-3">
            <img src="{{ asset('assets/intely/images/ic_audit.png') }}">
            <h4>Full Coverage</h4>
            <p class="mb-0">Your customized online portal contains work history and <br> records for all your present and past nurses for audit purposes. Just click 'download' to get the report ready in seconds!</p>
          </div>
          
          <div class="col-lg-4 col-sm-6 text-center thumbnail-text mb-lg-4 mb-3">
            <img src="{{ asset('assets/intely/images/ic_billing.png') }}">
            <h4>Simplified billing process</h4>
            <p class="mb-0">We provide one invoice for all nurses that have worked in your unit. We issue one invoice per month with a detailed summary of the nurse's name, date of service and the total number of hours worked. No more paying for multiple invoices!</p>
          </div>
          
          <div class="col-lg-4 col-sm-6 text-center thumbnail-text mb-lg-4 mb-3">
            <img src="{{ asset('assets/intely/images/ic_smart_technology.png') }}">
            <h4>Simplified approval process</h4>
            <p class="mb-0">We offer a technology that enables our team to check-in through our mobile app. Once a nurse completes their shift, a notification is sent for final approval. With our technology, time tracking has never been easier!</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Home Easy Step Section Begin Form Here -->
    <section class="home-easy-step d-flex align-items-center py-4">
      <div class="container">
        <div class="row">
          <div class="col-md-7">
            <h2 class="title-two">Simplified scheduling in four easy steps</h2>
            <ul class="order-list my-lg-5 my-4">
              <li>1) Login to your customized portal</li>
              <li>2) Submit your request and review available healthcare professionals in your area</li>
              <li>3) Book your healthcare professional</li>
              <li>4) Get notified when your healthcare professional is on their way</li>
            </ul>
            <!-- <button class="btn learn-btn"><span>Learn more</span></button> -->
            <a class="btn apply-now d-inline-block" href="{{ url('web/facility-form') }}"><span>Learn more</span></a>
          </div>
        </div>
      </div>
    </section>

    <!-- Home Update Section -->
    <section class="real-time bg-light-gray p-75">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6 pr-lg-5">
            <img class="img-fluid" src="{{ asset('assets/intely/images/desktop.png') }}" alt="">
          </div>
          <div class="col-md-6 pl-lg-5">
            <div class="text-38 mb-4">Efficient schedule management</div>
            <p class="text-22">Our technology allows you to efficiently schedule your workforce and maximize the potential of your staff, fulfill last minute requests and reduce your administrative time</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Audit Preparation Section -->
    <section class="audit-preparation p-75">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6 pr-lg-5">
            <div class="text-38 mb-4">Audit preparation</div>
            <p class="text-22">Our technology allows you to efficiently schedule your workforce and maximize the potential of your staff, fulfill last minute requests and reduce your administrative time</p>
          </div>
          <div class="col-md-6 pl-lg-5">
            <img class="img-fluid" src="{{ asset('assets/intely/images/desktop.png') }}" alt="">
          </div>
        </div>
      </div>
    </section>

    <!-- Approve Process Section -->
    <section class="approve-process bg-light-gray p-75">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6 pr-lg-5">
            <img class="img-fluid" src="{{ asset('assets/intely/images/desktop.png') }}" alt="">
          </div>
          <div class="col-md-6 pl-lg-5">
            <div class="text-38 mb-4">Simplified approval process</div>
            <p class="text-22">Our technology allows you to efficiently schedule your workforce and maximize the potential of your staff, fulfill last minute requests and reduce your administrative time</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Nursing Ratings Section -->
    <section class="nursing-ratings py-lg-5 py-4 bb-85">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-12 text-center">
            <div class="text-38 mb-lg-5 mb-4">Nursing Professional Ratings</div>
          </div>
          <div class="col-md-6 text-center">
            <img class="img-fluid" src="{{ asset('assets/intely/images/ic_ratings.png') }}" alt="">
          </div>
          <div class="col-md-6">
            <div class="text-26 mb-2">We value your feedback</div>
            <p class="text-22">Our technology allows you to efficiently schedule your workforce and maximize the potential of your staff, fulfill last minute requests and reduce your administrative time</p>
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
         <h3>Ready for an iCareConnect Platform in your facility?</h3>
         <p class="mb-3">If you’d like a live, personalized demonstration of our platform’s capabilities, let us know!</p>
         <a class="btn apply-now d-inline-block" href="{{ url('web/facility-form') }}"><span>Request a demo</span></a>
       </div>
     </div>
   </div>
 </section>
 @endsection