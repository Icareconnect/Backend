@extends('layouts.vertical', ['title' => 'Update Category'])
@section('css')

<?php
// print_r($category->user);die;
  $sessionatcentre = false;
  if(Config('client_connected') && (Config::get("client_data")->domain_name=="physiotherapist") && $category->parent_id===2)
    $sessionatcentre = true;
 ?>
    <!-- Plugins css -->
     <link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />

@endsection
@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                            <li class="breadcrumb-item active">{{ ($sessionatcentre)?'Update Centre':'Update Category'}} </li>
                        </ol>
                    </div>
                     <h4 class="page-title">{{ ($sessionatcentre)?'Update Centre':'Update Category'}}</h4>
                </div>
            </div>
        </div>
         <div class="row">
          @if($sessionatcentre)
            <div class="col-lg-12">
          @else
            <div class="col-lg-6">
          @endif
                <div class="card">
                    <div class="card-body">
                        <form role="form" action="{{ url('admin/categories').'/'.$category->id}}" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                              <input type="hidden" name="_method" value="put">
                                @if (session('status'))
                                    <div class="alert alert-success">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="name">{{ ($sessionatcentre)?'Centre Name':'Category Name'}}</label>
                                        <input type="text" class="form-control" name="name" value="{{ $category->name }}" placeholder="{{ ($sessionatcentre)?'Centre Name':'Category Name'}}" id="name">
                                        @if ($errors->has('name'))
                                                <span class="text-danger">{{ $errors->first('name') }}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="example-color">Color Code</label><br>
                                        <input id="example-color" type="color" name="color_code" value="{{old('color_code') ?? $category->color_code }}" placeholder="Color Code">
                                        @if ($errors->has('color_code'))
                                                <span class="text-danger">{{ $errors->first('color_code') }}</span>
                                        @endif
                                    </div>
                                  </div>
                                  @if($sessionatcentre)
                                  <input type="hidden" id="search_latitude" name="lat">
                                  <input type="hidden" id="search_longitude" name="long">
                                  <input type="hidden" id="city" name="city">
                                  <input type="hidden" id="state" name="state">
                                  <div class="form-group row">
                                      <div class="col-md-6">
                                          <label for="name">Centre Price</label>
                                           <input type="number" placeholder="Centre Price Per Session" class="form-control" value="{{ old('price')??$category->price }}" name="price" required="">
                                          @if ($errors->has('price'))
                                                  <span class="text-danger">{{ $errors->first('price') }}</span>
                                          @endif
                                      </div>
                                      <div class="col-md-6">
                                          <label for="name">Centre Email</label>
                                           <input type="email" placeholder="Centre Contact Email" class="form-control" value="{{ ($category->user)?$category->user->email:'' }}" name="email" required="">
                                          @if ($errors->has('email'))
                                                  <span class="text-danger">{{ $errors->first('email') }}</span>
                                          @endif
                                      </div>
                                  </div>
                                  <div class="form-group row">
                                    <div class="col-md-6">
                                      <label for="exampleInputFile2">Video</label>
                                        <div class="input-group">
                                          <div >
                                            <input id="exampleInputFile2" type="file" value="{{old('video') ?? $category->video }}" name="video" id="ct-img-file" accept="video/*">
                                            <video width="200" height="200" controls>
                                              <source src="{{ Storage::disk('spaces')->url('video/'.$category->video) }}"
                                                    type="video/webm">
                                            <source src="{{ Storage::disk('spaces')->url('video/'.$category->video) }}"
                                                    type="video/mp4">
                                              Your browser does not support the video tag.
                                            </video>
                                            @if ($errors->has('video'))
                                              <span class="text-danger">{{ $errors->first('video') }}</span>
                                            @endif
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <label for="page_body__2">Description</label><br>
                                        <textarea rows="5" class="form-control"  name="description_text"  id="page_body__2" placeholder="Description">{{{old('description_text') ?? $category->description_text }}}</textarea>
                                        @if ($errors->has('description_text'))
                                                <span class="text-danger">{{ $errors->first('description_text') }}</span>
                                        @endif
                                      </div>
                                  </div>
                                  @endif
                                  <div class="form-group row">
                                      <div class="col-md-6">
                                        <label for="page_body__">{{ ($sessionatcentre)?'Centre Address':'Description'}}</label><br>
                                        <textarea rows="5" class="form-control"  name="description"  id="{{ ($sessionatcentre)?'page_body__search':'page_body__' }}" placeholder="{{ ($sessionatcentre)?'Centre Address':'Description'}}">{{{old('description') ?? $category->description }}}</textarea>
                                        @if ($errors->has('description'))
                                                <span class="text-danger">{{ $errors->first('description') }}</span>
                                        @endif
                                      </div>
                                      <div class="col-md-6">
                                          <label for="exampleInputFile">Image</label>
                                          <div class="input-group">
                                            <div >
                                              <input type="file" value="{{old('image') ?? $category->image }}" name="image" id="ct-img-file">
                                              <img src="{{ Storage::disk('spaces')->url('uploads/'.$category->image) }}" id="profile-img-tag" width="200px" />
                                              @if ($errors->has('image'))
                                                <span class="text-danger">{{ $errors->first('image') }}</span>
                                              @endif
                                            </div>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="name">Enable Services</label>
                                        <select class="form-control show-tick" name="enable_service_type">
                                          <option <?php echo (old('enable_service_type')=='1' || $category->enable_service_type=='1')?"selected":'' ?> value="1">Yes</option>
                                          <option <?php echo (old('enable_service_type')=='0' || $category->enable_service_type=='0')?"selected":'' ?> value="0">No</option>
                                        </select>
                                        @if ($errors->has('enable_service_type'))
                                                <span class="text-danger">{{ $errors->first('enable_service_type') }}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                      <label for="image_icon">Icon</label>
                                      <div class="input-group">
                                          <input type="file" value="{{old('image_icon') ?? $category->image_icon }}" name="image_icon" id="image_icon">
                                          <img src="{{ Storage::disk('spaces')->url('uploads/'.$category->image_icon) }}" id="profile-img-tag-icon" width="200px" />
                                          @if ($errors->has('image_icon'))
                                                <span class="text-danger">{{ $errors->first('image_icon') }}</span>
                                        @endif
                                      </div>
                                  </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="name">Enable On Front-End</label>
                                        <select class="form-control show-tick" name="enable">
                                          <option <?php echo (old('enable')=='1' || $category->enable=='1')?"selected":'' ?> value="1">Yes</option>
                                          <option <?php echo (old('enable')=='0' || $category->enable=='0')?"selected":'' ?> value="0">No</option>
                                        </select>
                                        @if ($errors->has('enable'))
                                                <span class="text-danger">{{ $errors->first('enable') }}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                      @if($sessionatcentre)
                                      <label for="banner">Session Banner</label>
                                      <div class="input-group">
                                          <input type="file"  value="{{ $category->banner }}" name="banner" id="banner">
                                          <img src="{{ Storage::disk('spaces')->url('uploads/'.$category->banner) }}" id="profile-img-tag-icon" width="200px" />
                                          @if ($errors->has('banner'))
                                                <span class="text-danger">{{ $errors->first('banner') }}</span>
                                        @endif
                                      </div>
                                      @endif
                                  </div>
                                </div>
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div>
            @if(!$sessionatcentre)
            <!-- end col -->
            
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3 header-title">Sub Category
                          @if(!$category->parent_id) 
                          <a href="{{ url('admin/subcategories/'.$category->id.'/create')}}" class="btn btn-sm btn-info float-right">Add New Subcategory</a>
                          @endif
                        </h4>
                      <!--   <a href="{{ url('admin/categories/create')}}" class="btn btn-sm btn-info float-right">Add New Subcategory</a><br> -->
                        <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
                                <thead>
                                    <tr >
                                        <th>Sr No.</th>
                                        <th>Name</th>
                                        <th>Color Code</th>
                                        <th>Child Cat.</th>
                                        <th>Enable Service Type</th>
                                        <th>Enable On Front-End</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                 @foreach($category->subcategory as $index => $child)
                                    <tr>
                                      <td>{{ $index+1 }}</td>
                                      <td>{{ $child->name }}</td>
                                      <td>{{ $child->color_code }}</td>
                                      <td>{{ $child->subcategory->count() }}</td>
                                      <td>{{ ($child->enable_service_type=='1'?'Yes':'No') }}</td>
                                      <td>{{ ($child->enable=='1'?'Yes':'No') }}</td>
                                      <td><a href="{{ url('admin/categories') .'/'.$child->id.'/edit'}}" class="btn btn-sm btn-info float-left">Edit</a>
                                      </td>
                                    </tr>
                                 @endforeach
                                </tbody>
                        </table>
                    </div>  <!-- end card-body -->
                </div>  <!-- end card -->
            </div>  <!-- end col -->
            <!-- <div class="row"> -->
            
            <!-- <div class="col-lg-6">
            </div>
        </div> -->
            @endif

        </div>
        @if(!$sessionatcentre)
        <div class="row">
            @if(config('client_connected') && (Config::get("client_data")->domain_name=="mp2r" || Config::get("client_data")->domain_name=="iedu"))
            @else
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3 header-title">{{ __('text.Filters') }} <a href="{{ route('filters.create',$category->id) }}" class="btn btn-sm btn-info float-right">Add New</a></h4>
                            <table id="scroll-horizontal-datatable1" class="table w-100 nowrap">
                                  <thead>
                                  <tr >
                                    <th>Sr No.</th>
                                    <th>Name</th>
                                    <th>Pref Name</th>
                                    <th>Multi Select</th>
                                    <th>Options</th>
                                    <th>Action</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                   @foreach($category->filters as $index => $filtertype)
                                    <tr>
                                      <td>{{ $index+1 }}</td>
                                      <td>{{ $filtertype->filter_name }}</td>
                                      <td>{{ $filtertype->preference_name }}</td>
                                      <td><?php echo ($filtertype->is_multi=='1')?"True":'False' ?> </td>
                                      <td>{{ $filtertype->options->pluck('option_name') }}</td>
                                      <td><a href="{{ route('filters.edit',[$category->id,$filtertype->id]) }}" class="btn btn-sm btn-info float-left">Edit</a>
                                      <button type="button" class="btn btn-danger btn-xs delete-filter"  data-filter_id="{{ $filtertype->id }}">Delete</button>
                                      </td>
                                    </tr>
                                 @endforeach 
                                </tbody>
                            </table>
                    </div>  <!-- end card-body -->
                </div>  <!-- end card -->
            </div>  <!-- end col -->
            @endif
            @if(config('client_connected') && (Config::get("client_data")->domain_name=="mp2r" || Config::get("client_data")->domain_name=="iedu"))
            @else
            <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-3 header-title">{{ __('text.Cat. Service Type') }} <a href="{{ url('admin/categories/'.$category->id.'/service/create')}}" class="btn btn-sm btn-info float-right">Add New</a></h4>
                        <table id="scroll-horizontal-datatable2" class="table w-100 nowrap">
                            <thead>
                            <tr >
                                <th>Sr No.</th>
                                <th>Service Name</th>
                                <th>Min. Duration</th>
                                <th>Gap Duration</th>
                                <th>Status</th>
                                <th>Fixed Price</th>
                                <th>Price Min</th>
                                <th>Price Max</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                             @foreach($category->services as $index => $service)
                                <tr>
                                  <td>{{ $index+1 }}</td>
                                  <td>{{ $service->service->type }}</td>
                                  <td>{{ ($service->is_active=='1'?'True':'False') }}</td>
                                  <td>{{ $service->minimum_duration }}</td>
                                  <td>{{ $service->gap_duration }}</td>
                                  <td>{{ $service->price_fixed!==null?$service->price_fixed:'NA' }}</td>
                                  <td>{{ $service->price_minimum }}</td>
                                  <td>{{ $service->price_maximum }}</td>
                                  <td><a href="{{ url('admin/categories') .'/'.$category->id.'/service/'.$service->id.'/edit'}}" class="btn btn-sm btn-info float-left">Edit</a>
                                  </td>
                                </tr>
                             @endforeach   
                            </tbody>
                          </table>
                  </div>
              </div>
            </div>
            @endif
            <!-- end col -->

        </div>
        @endif
        @if(config('client_connected') && (Config::get("client_data")->domain_name=="mp2r" || Config::get("client_data")->domain_name=="iedu"))
        @else
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3 header-title">{{ __('text.Additional Fields') }} <a href="{{ route('additional-details.create',$category->id) }}" class="btn btn-sm btn-info float-right">Add New</a></h4>
                            <table id="scroll-horizontal-datatable3" class="table w-100 nowrap">
                                  <thead>
                                  <tr >
                                    <th>Sr No.</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Enable</th>
                                    <th>Action</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                   @foreach($category->additionals as $index => $additional)
                                    <tr>
                                      <td>{{ $index+1 }}</td>
                                      <td>{{ $additional->name }}</td>
                                      <td>{{ $additional->type }}</td>
                                      <td><?php echo ($additional->is_enable=='1')?"True":'False' ?> </td>
                                      <td><a href="{{ route('additional-details.edit',[$category->id,$additional->id]) }}" class="btn btn-sm btn-info float-left">Edit</a>
                                      <!-- <button type="button" class="btn btn-danger btn-xs delete-filter"  data-filter_id="{{ $additional->id }}">Delete</button> -->
                                      </td>
                                    </tr>
                                 @endforeach 
                                </tbody>
                            </table>
                    </div>  <!-- end card-body -->
                </div>  <!-- end card -->
            </div>  <!-- end col -->
            <div class="col-lg-6">
            </div>

        </div>
        @endif
        
    </div>
@endsection
@section('script')
     <script src="{{asset('assets/libs/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('assets/libs/pdfmake/pdfmake.min.js')}}"></script>
    <script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3p4obmFTNVN9q-hB7VyYLli92iqpu070&amp;libraries=places"></script>
    <script type="text/javascript">
      google.maps.event.addDomListener(window, 'load', initialize);
        function initialize() {
          var input = document.getElementById('page_body__search');
          var autocomplete = new google.maps.places.Autocomplete(input);
          autocomplete.setComponentRestrictions({
            country: ["in"],
          });
          autocomplete.addListener('place_changed', function () {
          var place = autocomplete.getPlace();
          var address = place.address_components;
          var city, state, zip;
          address.forEach(function(component) {
            var types = component.types;
            if (types.indexOf('locality') > -1) {
              city = component.long_name;
            }
            if (types.indexOf('administrative_area_level_1') > -1) {
              state = component.long_name;
            }
            if (types.indexOf('postal_code') > -1) {
              zip = component.long_name;
            }
          });
          $('#city').val(city);
          $('#state').val(state);
          $('#search_latitude').val(place.geometry.location.lat());
          $('#search_longitude').val(place.geometry.location.lng());
      });
    }
        $(document).ready(function() {
             $('#scroll-horizontal-datatable,#scroll-horizontal-datatable1,#scroll-horizontal-datatable2,#scroll-horizontal-datatable3').DataTable({
                "scrollX": true,
                "language": {
                    "paginate": {
                        "previous": "<i class='mdi mdi-chevron-left'>",
                        "next": "<i class='mdi mdi-chevron-right'>"
                    }
                },
                "drawCallback": function () {
                    $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                }
            });
        });
        $(function () {
             function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    
                    reader.onload = function (e) {
                        $('#profile-img-tag').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            function readURL2(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    
                    reader.onload = function (e) {
                        $('#profile-img-tag-icon').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $("#ct-img-file").change(function(){
                readURL(this);
            }); 
            $("#image_icon").change(function(){
                readURL2(this);
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $(".delete-filter").click(function(e){
                  e.preventDefault();
                  var filter_id = $(this).attr('data-filter_id');
                  Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                  }).then((result) => {
                    if (result.value) {
                        $.ajax({
                           type:'DELETE',
                           url:base_url+'/admin/categories/'+"{{ $category->id}}"+'/filters/'+filter_id,
                           data:{id:filter_id},
                           success:function(data){
                              Swal.fire(
                                'Deleted!',
                                'Filter has been deleted.',
                                'success'
                              ).then((result)=>{
                                window.location.reload();
                              });
                           }
                        });
                      }
                  });
            
            });
        });

    </script>

@endsection