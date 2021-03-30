@extends('layouts.vertical', ['title' => 'Update '.__('text.Vendor')])
@section('css')
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">Update {{ __('text.Vendor') }}</h3>
    </div>
    <form role="form" action="{{ url('admin/consultants').'/'.$consultant->id}}" method="post">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="PUT">
        <div class="card-body">
        @if (session('status'))
          <div class="alert alert-success">
              {{ session('status') }}
          </div>
        @endif
          <div class="form-group">
            <label for="exampleInputEmail1">Email address</label>
            <input type="email" class="form-control" name="email" value="{{ $consultant->email }}" id="exampleInputEmail1" placeholder="Enter email">
            @if ($errors->has('email'))
                    <span class="text-danger">{{ $errors->first('email') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" value="{{ $consultant->name }}" id="name" placeholder="name">
            @if ($errors->has('name'))
              <span class="text-danger">{{ $errors->first('name') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ $consultant->phone }}" id="phone" placeholder="phone">
            @if ($errors->has('phone'))
                    <span class="text-danger">{{ $errors->first('phone') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="experience">Experience</label>
            <input type="text" name="experience" class="form-control" value="{{ ($consultant->profile && $consultant->profile->experience?$consultant->profile->experience:'') }}" id="experience" placeholder="Experience">
            @if ($errors->has('experience'))
              <span class="text-danger">{{ $errors->first('experience') }}</span>
            @endif
          </div>
          @if(config('client_connected') && (Config::get("client_data")->domain_name=="healtcaremydoctor"))
          <div class="row pb-3">
            <div class="col-md-6">
              <div class="form-group">
                 <label for="dob">DOB</label>
                <input type="date" class="form-control" id="dob" placeholder="y-m-d" name="dob" value="{{($consultant->profile)?$consultant->profile->dob:''}}">
                <span class="alert-danger dob_error"></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                 <label for="dob">Working Since</label>
                <input type="date" class="form-control" id="working_since" placeholder="y-m-d" name="working_since" value="{{ ($consultant->profile)?$consultant->profile->working_since:'' }}">
                <span class="alert-danger working_since_error"></span>
              </div>
            </div>
          </div>

          <div class="row pb-3">
            <div class="col-md-6">
              <div class="form-group">
                 <label for="qualification">Qualification</label>
                <input type="text" class="form-control" id="qualification" placeholder="qualification" name="qualification" value="{{ ($qualification)?$qualification->field_value:'' }}">
                <span class="alert-danger qualification_error"></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                 <label for="bio">Bio</label>
                <input type="text" class="form-control" id="bio" placeholder="Bio" name="bio" value="{{($consultant->profile)?$consultant->profile->about:''}}">
                <span class="alert-danger bio_error"></span>
              </div>
            </div>
          </div>
          <div class="row pb-3">
            @foreach($master_preferences as $master_preference)
            <div class="col-md-6">
              <div class="form-group">
                <label for="{{ $master_preference['preference_name'] }}">{{ $master_preference['preference_name'] }}</label>
                <select  class="form-control" id="{{ $master_preference['preference_name'] }}" name="master_preferences[{{ $master_preference['id'] }}][]" {{ ($master_preference['is_multi']=='1')?'multiple=""':''}}>
                    <option value="">-- Select {{ $master_preference['preference_name'] }} --</option>
                    @foreach($master_preference['options'] as $options)
                      <option value="{{ $options['id'] }}" {{ isset($options['isSelected'])?"selected":"" }}>{{ $options['option_name'] }}</option>
                    @endforeach
                 </select>
                <span class="alert-danger dob_error"></span>
              </div>
            </div>
            @endforeach
            <div class="col-md-6">
              <div class="form-group">
                 <label for="dob">Catgeory</label>
                    <select class="form-control" id="category_id" name="category">
                      @if (isset($parentCategories))
                      @foreach ($parentCategories as $category)
                      @if(count($category->subcategory) <= 0)
                        <option value="{{ $category->id }}" {{ ($consultant->category && $consultant->category->id == $category->id)?'selected':'' }}>{{ $category->name }}</option>
                      @else
                      <optgroup label="{{ $category->name }}">
                          @foreach ($category->subcategory as $sub)
                            <option value="{{ $sub->id }}" {{ ($consultant->category && $consultant->category->id == $sub->id)?'selected':'' }}>{{ $sub->name }}</option>
                          @endforeach
                      </optgroup>
                      @endif

                      @endforeach
                      @endif
                  </select>
                <span class="alert-danger working_since_error"></span>
              </div>
            </div>
          </div>
          @endif
          @if(config('client_connected') && (Config::get("client_data")->domain_name=="mp2r" || Config::get("client_data")->domain_name=="food"))
          <div class="row pb-3">
            <div class="col-md-6">
              <div class="form-group">
                 <label for="pwd">Address</label>
                <input type="text" class="form-control" id="pac-input" placeholder="204, Eloisa Village Apt. 827" name="address" value="{{$consultant->profile->address}}">
                <span class="alert-danger address_error"></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                 <label for="state">State</label> 
                 <input type="hidden" id="state_name" value="{{$consultant->profile->state}}">           
                 <input type="hidden" id="city_name" value="{{$consultant->profile->city}}">           
                  <select  class="form-control" id="state" name="state">
                    <option value="">Select State</option>
                    @foreach($states as $id=>$name)
                      <option <?php echo (old('state')=='1' || $consultant->profile->state == $name) ? "selected":'' ?> value="{{ $name }}">{{ $name }}</option>
                    @endforeach
                 </select>
                 <span class="alert-danger state_error"></span>         
              </div>
            </div>
          </div>
          <div class="row pb-3">
          <div class="col-md-6">
            <div class="form-group">
               <label for="state">City</label>             
                <select  class="form-control" id="city" name="city">
                  <option></option>
               </select>
               <span class="alert-danger city_error"></span>            
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
            <label for="email">Zip Code</label>
             <input type="text" class="form-control" id="zipcodee" onkeypress="return isNumber(event)"   placeholder="Zip Code" name="zip_code" maxlength="6" value="{{$zip_code ? $zip_code->field_value : ''}}">
             <span class="alert-danger zip_code_error"></span>
            </div>
          </div>
        </div>
        <div class="row pb-3">
          <div class="col-md-6">
            <div class="form-group">
               <label for="pwd">Education</label>
              <input type="text" class="form-control" placeholder="MD" name="education" value="{{$education ? $education->field_value : ''}}">
              <span class="alert-danger education_error"></span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
            <label for="pwd">Your Accepted Insurance</label>
             <select class="form-control" id="insurances2" name="insurances[]" multiple="">
              @foreach($insurances as $id=> $name)
                <option <?php echo (in_array($id, $user_insurances_ids)) ? "selected":'' ?> value="{{ $id }}">{{ $name }}</option>
              @endforeach
              </select> 
              <span class="alert-danger insurances_error"></span>
            </div>
          </div>
        </div>
          <div class="form-group">
            <label for="experience">NPI Number</label>
            <input type="text" name="npi_number" class="form-control" value="{{ ($consultant->npi_id && $consultant->npi_id ? $consultant->npi_id :'') }}" placeholder="NPI Number">
            @if ($errors->has('npi_number'))
              <span class="text-danger">{{ $errors->first('npi_number') }}</span>
            @endif
          </div>
          @endif
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
  </div>
   @endsection
@section('script')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAV4UFmQuEWaFEnrA5Q7Q0rxwVr5jOqR4Y&amp;libraries=places"></script>
<script type="text/javascript">
  google.maps.event.addDomListener(window, 'load', initialize);
    function initialize() {
      var input = document.getElementById('pac-input');
      var autocomplete = new google.maps.places.Autocomplete(input);
      autocomplete.setComponentRestrictions({
        country: ["us"],
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
      $('.search_latitude1').val(place.geometry.location.lat());
      $('.search_longitude1').val(place.geometry.location.lng());
      $('#city').val(place.name);
      $('#state').val(state);
      getCity(state);
      function getCity(state_id){
        $("#city").find("option:gt(0)").remove();
          $("#city").find("option:first").text("Loading...");
          $.getJSON(base_url+"/get/cities", {
              state_id: state_id
          }, function (json) {
              $("#city").find("option:first").remove();
              for (var i = 0; i < json.length; i++) {
                  $("<option/>").attr("value", json[i].id).text(json[i].name).appendTo($("#city"));
              }
          });
      }
      $('#zipcodee').val(zip);
      });
    }
  $(document).ready(function() {
    
    setTimeout(function(){ $('#state').trigger('change'); }, 200);
    function getCity(state_name){
        $("#city").find("option:gt(0)").remove();
        $("#city").find("option:first").text("Loading...");
        var city_name = $('#city_name').val();
        $.getJSON(base_url+"/get/city_deatils", {
            state_name: state_name
        }, function (json) {
            $("#city").find("option:first").remove();
            for (var i = 0; i < json.length; i++) {
                $("<option/>").attr("value", json[i].name).text(json[i].name).appendTo($("#city"));
            }
            if(city_name){
              $('#city option[value="'+city_name+'"]').attr("selected", "selected");
            }
        });
    }
    $(document).on('change', '#state', function(){  
        getCity($(this,':selected').val());
    });
  });

</script>
@endsection