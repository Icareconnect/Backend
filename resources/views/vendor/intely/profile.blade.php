@extends('vendor.intely.layouts.index', ['title' => 'Home','after_signup'=>true,'nurse'=>'true'])
@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-2">
          <h1 class="mb-0">Profile</h1>
        </div>
      </div>
      <div class="row">
      	<div class="col-md-8 tabcontent" id="profile_detail">

                <section class="wrapper2">
                    <div class="row align-items-center pt-2 pb-2">
                        <div class="col">
                            <h2 class="edit-name">{{ isset($user->name) ? $user->name : 'N/A' }}</h2>
                        </div>
                    </div>
                    <hr>
                    <div class="row align-items-center pt-3">
                        <div class="col-md-6 col-lg-6 ">
                            <p class="second-name2">Email ID</p>
                            <p class="first-name">{{ $user->email }}</p>
                        </div>
                        <div class="col-md-6 col-lg-6 ">
                            <p class="second-name2">Phone Number</p>
                            <p class="first-name">
                                {{ isset($user->phone) ? $user->country_code . '' . $user->phone : 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row align-items-center pt-3">
                        <div class="col-md-6 col-lg-6 ">
                            <p class="second-name2">Category</p>
                            <p class="first-name">{{ ($user->getCategoryData($user->id) && $user->getCategoryData($user->id)->id?($user->filter)?$user->filters_name:$user->getCategoryData($user->id)->name:'NA') }}</p>
                        </div>
                        <div class="col-md-6 col-lg-6 ">
                            <p class="second-name2">Zip</p>
                            <p class="first-name">{{ isset($user_zip_code->field_value) ? $user_zip_code->field_value : 'N/A' }}</p>
                        </div>
                    </div>
                    
                </section>

            </div>
      </div>
	</div>
</div>  
@endsection