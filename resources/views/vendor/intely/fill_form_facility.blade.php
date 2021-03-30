@extends('vendor.intely.layouts.index', ['title' => 'Facility Signup'])
@section('content')
  <!-- Bannar Section -->
  <section class="sub-page-banner fill-banner d-flex align-items-center">
    <div class="container">
      <div class="row">
        <div class="col text-center">
          <div class="banner-heading">Request a Demo Form</div>
        </div>
      </div>
    </div>
  </section>
  <!-- Sub-Page Content Area -->
  <div class="sub-page-content">
    <!-- Fill Form Content Section -->
    <section class="fill-form-content py-md-5 py-4 mt-4 mb-lg-5">
      <div class="container">
        <div class="row">
          <div class="col-12 text-center mb-lg-5 mb-4">
            <h1 class="mb-4">Request a Demo of iCareConnect Platform for Nursing Facilities</h1>
            <p class="text-22">Tired of the long and complex process involved in filling shifts? <br class="d-md-block d-none">
              Ready for a quick and stress-free way of filling advance or last minute shifts in your facility? <br class="d-md-block d-none">
              Connect with us today and request a demo of our iCareConnect platform. </p>
          </div>
          <div class="offset-lg-2 col-lg-8">
            <form class="fill-form px-lg-5" id="request_demo">
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <label>First Name</label>
                    <input class="form-control" type="text" name="first_name" id="first_name" placeholder="" required="">
                  </div>
                  <div class="col-md-6">
                    <label>Last Name</label>
                    <input class="form-control" type="text" name="last_name" id="last_name" placeholder="" required="">
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <label>Email Address</label>
                    <input class="form-control" type="email" name="email" id="email" placeholder="" required="">
                  </div>
                  <div class="col-md-6">
                    <label>Phone Number</label>
                    <input class="form-control" type="text" name="number" id="number" placeholder="" required="">
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="row row-spacing no-gutters">
                  <div class="col">
                    <label>Facility Name</label>
                    <input class="form-control" type="text" name="facility_name" id=""  required="" placeholder="">
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="row row-spacing no-gutters">
                  <div class="col">
                    <label>Job Title</label>
                    <input class="form-control" type="text" name="job_title" required="" id="" placeholder="">
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <label>City</label>
                    <input class="form-control" type="text" name="city" required="" id="" placeholder="">
                  </div>
                  <div class="col-md-6">
                    <label>Province</label>
                    <input class="form-control" type="text" name="province" required=""  id="" placeholder="">
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="row row-spacing no-gutters">
                  <div class="col">
                    <label>Comments</label>
                    <textarea class="form-control" name="comment" id="" cols="30" rows="5"></textarea>
                  </div>
                </div>
              </div>
              <div class="form-group mt-4 pt-3 text-center">
                <button class="btn form-btn"><span id="btn_text_val">Submit</span></button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>
   @endsection