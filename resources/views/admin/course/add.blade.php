@extends('layouts.vertical', ['title' => 'Create Course'])
@section('content')
<?php
  $sessionatcentre = false;
  if(Config('client_connected') && (Config::get("client_data")->domain_name=="iedu") && $category->id===2)
    $sessionatcentre = true;
 ?>
 <!-- Start Content-->
<div class="container-fluid">
  <div class="row">
    <div class="col-lg-8">
      <div class="card">
          <div class="card-header">
            
              <h3>Create Course</h3>
           
          </div>

          <div class="card-body">
            <form action="{{ route('course.store') }}" method="POST" enctype="multipart/form-data">
              @csrf
              
                <div class="form-group row">
                  <div class="col-md-6">
                  <label>Title</label><br>
                  <textarea class="form-control" placeholder="Title"  name="title" row="5" required>{{ old('title') }}</textarea>
                  @if ($errors->has('title'))
                      <span class="text-danger">{{ $errors->first('title') }}</span>
                  @endif
                </div>
              </div>
              <div class="form-group row">
                
                <div class="col-md-6">
                  <label>Color Code:</label>
                  <input id="example-color" type="color" name="color_code" value="{{ old('color_code') }}" class="form-control" required>
                   @if ($errors->has('color_code'))
                    <span class="text-danger">{{ $errors->first('color_code') }}</span>
                  @endif
                </div>
              </div>
              <div class="form-group">
                  <label for="exampleInputFile">Image</label>
                  <div class="input-group">
                    <div >
                      <input type="file" value="{{ old('image_icon') }}" name="image_icon" id="exampleInputFile">
                    </div>
                  </div>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-primary">Create</button>
              </div>
            </form>
          </div>
        </div>
  </div>
  </div>      
</div>
@endsection
@section('script')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAV4UFmQuEWaFEnrA5Q7Q0rxwVr5jOqR4Y&amp;libraries=places"></script>
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
</script>
@endsection