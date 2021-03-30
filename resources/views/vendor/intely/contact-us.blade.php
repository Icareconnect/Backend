@extends('vendor.intely.layouts.index', ['title' => 'Nursing Professionals'])
@section('content')
  <div class="offset-top"></div>
  <!-- Sub-Page Content Area -->
  <div class="sub-page-content">
    <!-- Contact Us Content Section -->
    <section class="contact-content py-md-5 py-4 mt-2 mb-lg-5">
      <div class="container">
        <div class="row">
          <div class="col-12 text-center mb-lg-5 mb-4">
            <h1>Contact Us</h1>
          </div>
          <div class="offset-lg-1 col-lg-10">
            <div class="row">
              <div class="col-lg-6">
                <div class="contact-img">
                  <img src="{{ asset('assets/intely/images/ic_contact_us.png') }}" alt="">
                </div>
                <div class="">
                  <h3>CANADA</h3>
                  <p class="mb-3"> <strong> Our Address: </strong> <br>
                    iCareConnect Ltd </p>
                    
                    <p>
                    2 Bloor Street East <br>
                    Toronto, Ontario <br>
                    M4W 1A8
                    </p>
                    <div class="d-flex align-items-center">
                      <p class="mr-4 pr-2"> <strong>Phone Number: <br>
                        </strong> <a href="tel:+18558302273"></a> 1-855-830-(care) 2273<br>
                        <a href="tel:+1647670273"></a> 1-647-670-(care) 2273
                      </p>
                      <p> <strong>Email: <br>
                        </strong> <a class="text-black" href="mailto:support@icareconnect.ca">support@icareconnect.ca</a></p>
                    </div>
                </div>
              </div>
              <div class="col-lg-6">
                <form id="query_post">
                  <div class="form-group">
                    <div class="row row-spacing no-gutters">
                      <div class="col-md-6">
                        <input type="hidden" name="to_email" value="support@icareconnect.ca">
                        <label>First Name*</label>
                        <input required="" class="form-control" type="text" name="first_name" id="first_name" placeholder="">
                      </div>
                      <div class="col-md-6">
                        <label>Last Name*</label>
                        <input required="" class="form-control" type="text" name="last_name" id="last_name" placeholder="">
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="row row-spacing no-gutters">
                      <div class="col">
                        <label>Your Email*</label>
                        <input required="" class="form-control" type="email" name="email" id="email" placeholder="">
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="row row-spacing no-gutters">
                      <div class="col">
                        <label>Subject*</label>
                        <input required="" class="form-control" type="text" name="subject" id="subject" placeholder="">
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="row row-spacing no-gutters">
                      <div class="col">
                        <label>Your Message</label>
                        <textarea required="" class="form-control" name="query_data" id="query_data" cols="30" rows="5"></textarea>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group mt-4 pt-3">
                    <button class="btn"><span id="btn_text_val">Submit</span></button>
                  </div>

                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  @endsection