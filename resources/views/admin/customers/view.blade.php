@extends('layouts.vertical', ['title' => 'View '.__('text.User')])

@section('css')
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
            <div class="col-md-4">

               <div class="card-header p-2">
                  <ul class="nav nav-pills">
                    <li class="nav-item"></li>
                  </ul>
                </div><!-- /.card-header -->

              <!-- Profile Image -->
              <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <div class="text-center">
                          <img class="profile-user-img img-fluid img-circle" src="{{ Storage::disk('spaces')->url('thumbs/'.$customer->profile_image) }}" alt="User Image">
              
                  </div>

                  <h3 class="profile-username text-center">{{ ($customer->name)?$customer->name:'unknown' }}</h3>
                  <?php $requests = $customer->getReqAnaliticsByCustomer($customer->id); ?>

                  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                      <b>Email:</b> {{ $customer->email }}
                    </li>
                    <li class="list-group-item">
                      <b>Phone:</b> {{ $customer->country_code }}-{{ $customer->phone }}
                    </li>
                    <li class="list-group-item">
                      <b>Total Requests:</b> {{ $requests->totalRequest }} 
                    </li> 
                    <li class="list-group-item">
                      <b>About:</b> {{ ($customer->profile && $customer->profile->about)?$customer->profile->about:'' }} 
                    </li>
                    <li class="list-group-item">
                      <b>Address:</b> {{ ($customer->profile && $customer->profile->location_name)?$customer->profile->location_name:'' }} 
                    </li>
                  </ul>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->

              <!-- About Me Box -->
              
              <!-- /.card -->
            </div>
            <!-- /.col -->
            <div class="col-md-8">
              <div class="card">
                <div class="card-header">
                </div><!-- /.card-header -->
                <div class="card-body">
                    <ul class="nav nav-tabs nav-bordered">
                      <li class="nav-item">
                          <a href="#activity" data-toggle="tab" aria-expanded="false" class="nav-link px-3 py-2 active">
                              <i class="mdi mdi-pencil-box-multiple font-18 d-md-none d-block"></i>
                              <span class="d-none d-md-block">Reviews</span>
                          </a>
                      </li>
                  </ul>
                  <div class="tab-content">
                    <div class="active tab-pane" id="activity">

                      <!-- Post -->
                      @foreach($customer->givenReviewByUser($customer->id) as $key=>$review)
                      <?php if($review->consultant->profile_image)
                          $review->consultant->profile_image = Storage::disk('spaces')->url('thumbs/'.$review->consultant->profile_image);
                        else
                          $review->consultant->profile_image = url('/').'/default/user.jpg';
                      ?>
                      <div class="post clearfix">
                        <div class="user-block">
                          <img class="img-circle img-bordered-sm" src="{{ $review->consultant->profile_image }}" alt="User Image" height="20px" width="20px">
                          <span class="username">
                            <a href="#">{{  ($review->consultant->name)?$review->consultant->name:'unknown'}}</a>
                            <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                          </span>
                          <span class="description">Sent a review by You - {{ Carbon\Carbon::parse($review->created_at)->diffForHumans() }}</span>
                        </div>
                        <br>
                        <p>
                          {{ $review->comment }}
                        </p>
                        <strong>Rating : {{ $review->rating }}</strong>
                        <hr>
                      </div>
                      @endforeach
                      <!-- /.post -->
                    </div>
                    <!-- /.tab-pane -->
                  </div>
                  <!-- /.tab-content -->
                </div><!-- /.card-body -->
              </div>
              <!-- /.nav-tabs-custom -->
            </div>
            <!-- /.col -->
    </div>
@endsection
@section('script')
<script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>