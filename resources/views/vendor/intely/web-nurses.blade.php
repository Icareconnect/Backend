@extends('vendor.intely.layouts.index', ['title' => 'Nurses','after_signup'=>true])

@section('content')
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-2">
          <h1 class="mb-0">Nurses</h1>
        </div>
        <div class="col-10 d-flex align-items-center justify-content-end">

          <div class="search_box d-flex align-items-center justify-content-center mr-4">
            <i class="fas fa-search"></i>
            <input type="" placeholder="Search">
            <button class="btn" type=""> <span> Search</span></button>
          </div>

          <div class="dropdown_toggle position-relative text-left">
            <a class="toggle-btn" href="javascript:void(0)">Sort by</a>
            <div class="dropdown_list social-dropdown p-0">
              <ul class="m-0">
                <li class="active">Location</li>
                <li>Rate</li>
                <li>Experience</li>
              </ul>
            </div>
          </div>
          
        </div>
      </div>

          <div class="row">
            <div class="col-12">
              
        <div class="nurses-content mt-4 p-4">
          <div class="review-wrapper">
            <div class="review_box p-3 mb-lg-4 mb-3">
              <div class="row no-gutters row-spacing">
                <div class="col-2 text-center">
                  <img src="images/admin1.png" alt="">
                </div>
                <div class="col-10">
                  <div class="review-heading d-flex align-items-center justify-content-between">
                    <h6 class="m-0">Terry Oliver</h6>
                    <ul class="review_star d-flex align-items-center m-0">
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li class="review_no text-14 ml-3 mt-1">123 Reviews</li>
                    </ul>
                  </div>
                  <p class="text-14">Dermatologist · MBBS, MD </p>
                  <div class="d-flex align-items-center justify-content-between">
                    <p class="text-14 m-0">9 yrs exp, Schowalter Ridge, <br> Sector 24, Chandigarh</p>
                    <a class="book_btn" href="#">Book Now</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="review_box p-3 mb-lg-4 mb-3">
              <div class="row no-gutters row-spacing">
                <div class="col-2 text-center">
                  <img src="{{ asset('assets/intely/images/admin1.png') }}" alt="">
                </div>
                <div class="col-10">
                  <div class="review-heading d-flex align-items-center justify-content-between">
                    <h6 class="m-0">Terry Oliver</h6>
                    <ul class="review_star d-flex align-items-center m-0">
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li class="review_no text-14 ml-3 mt-1">123 Reviews</li>
                    </ul>
                  </div>
                  <p class="text-14">Dermatologist · MBBS, MD </p>
                  <div class="d-flex align-items-center justify-content-between">
                    <p class="text-14 m-0">9 yrs exp, Schowalter Ridge, <br> Sector 24, Chandigarh</p>
                    <a class="book_btn" href="#">Book Now</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="review_box p-3 mb-lg-4 mb-3">
              <div class="row no-gutters row-spacing">
                <div class="col-2 text-center">
                  <img src="{{ asset('assets/intely/images/admin1.png') }}" alt="">
                </div>
                <div class="col-10">
                  <div class="review-heading d-flex align-items-center justify-content-between">
                    <h6 class="m-0">Terry Oliver</h6>
                    <ul class="review_star d-flex align-items-center m-0">
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li class="review_no text-14 ml-3 mt-1">123 Reviews</li>
                    </ul>
                  </div>
                  <p class="text-14">Dermatologist · MBBS, MD </p>
                  <div class="d-flex align-items-center justify-content-between">
                    <p class="text-14 m-0">9 yrs exp, Schowalter Ridge, <br> Sector 24, Chandigarh</p>
                    <a class="book_btn" href="#">Book Now</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="review_box p-3 mb-lg-4 mb-3">
              <div class="row no-gutters row-spacing">
                <div class="col-2 text-center">
                  <img src="{{ asset('assets/intely/images/admin1.png') }}" alt="">
                </div>
                <div class="col-10">
                  <div class="review-heading d-flex align-items-center justify-content-between">
                    <h6 class="m-0">Terry Oliver</h6>
                    <ul class="review_star d-flex align-items-center m-0">
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li class="review_no text-14 ml-3 mt-1">123 Reviews</li>
                    </ul>
                  </div>
                  <p class="text-14">Dermatologist · MBBS, MD </p>
                  <div class="d-flex align-items-center justify-content-between">
                    <p class="text-14 m-0">9 yrs exp, Schowalter Ridge, <br> Sector 24, Chandigarh</p>
                    <a class="book_btn" href="#">Book Now</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="review_box p-3 mb-lg-4 mb-3">
              <div class="row no-gutters row-spacing">
                <div class="col-2 text-center">
                  <img src="{{ asset('assets/intely/images/admin1.png') }}" alt="">
                </div>
                <div class="col-10">
                  <div class="review-heading d-flex align-items-center justify-content-between">
                    <h6 class="m-0">Terry Oliver</h6>
                    <ul class="review_star d-flex align-items-center m-0">
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li><img src="{{ asset('assets/intely/images/star-icon.png')}}" alt=""></li>
                      <li class="review_no text-14 ml-3 mt-1">123 Reviews</li>
                    </ul>
                  </div>
                  <p class="text-14">Dermatologist · MBBS, MD </p>
                  <div class="d-flex align-items-center justify-content-between">
                    <p class="text-14 m-0">9 yrs exp, Schowalter Ridge, <br> Sector 24, Chandigarh</p>
                    <a class="book_btn" href="#">Book Now</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>  
        </div>
      </div>


    </div>
    <!-- /.container-fluid-->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fa fa-angle-up"></i>
    </a>
  </div>




  <!-- Jobs Modal -->
  <div class="modal fade" id="jobs" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header border-0 p-0">
          <a href="javascript:void(0)" type="" class="close position-absolute" data-dismiss="modal" aria-label="Close">
            <img src="images/ic_close.svg" alt="">
          </a>
        </div>
        <div class="modal-body border-0 p-0">
          <h4 class="text-center">Job Opening form</h4>
          <form class="jobs_form mt-lg-5 mt-4">
            <div class="form-group">
              <div class="row row-spacing no-gutters">
                <div class="col">
                  <label>Job Title</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row row-spacing no-gutters">
                <div class="col">
                  <label>Job Description</label>
                  <textarea class="form-control" name="" id="" cols="30" rows="5"></textarea>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row row-spacing no-gutters">
                <div class="col">
                  <label>Qualification</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label>Experience</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
                <div class="col-md-6">
                  <label>Category</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label>Services required</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
                <div class="col-md-6">
                  <label> No. of openings</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label>Location</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
                <div class="col-md-6">
                  <label>Urgency</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label>Start Date</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
                <div class="col-md-6">
                  <label>Duration</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label>Timings</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
                <div class="col-md-6">
                  <label>Due date</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col">
                  <label>Preferences</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
              </div>
            </div>

            <div class="form-group mt-lg-5 mt-4 text-center">
              <button class="btn form-btn"><span>Submit</span></button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
