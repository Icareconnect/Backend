@extends('layouts.vertical', ['title' => 'Client'])
@section('css')
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">Registranstion APP</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ route('clinet-create')}}" method="post">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="post">
        <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
          <div class="form-group">
            <div class="row">
              <div class="col-sm-6">
                <label for="app_name">APP Name</label>
                <input  type="text" class="form-control" name="app_name" value="{{old('app_name')}}" placeholder="Enter APP Name ">
                @if ($errors->has('app_name'))
                        <span class="text-danger">{{ $errors->first('app_name') }}</span>
                @endif
              </div>
              <div class="col-sm-4">
                <label for="domain_name" id="domain_name_checking">Domain</label>
                <div class="input-group">
                  <input  type="text" class="form-control is-warning" name="domain_name" value="{{old('domain_name')}}" id="domain_name" placeholder="Domain Name">.royoconsult.com
                 <span class="text-danger app_name_domain_error"></span>
                  @if ($errors->has('domain_name'))
                          <span class="text-danger app_name_domain_error">{{ $errors->first('domain_name') }}</span>
                  @endif
                </div>
              </div>
              
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              
              <div class="col-sm-6">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" value="{{old('email')}}" id="email" placeholder="Enter Email">
                @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                @endif
              </div>
              <div class="col-sm-6">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password">
                @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                @endif
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-sm-6">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" name="first_name" value="{{old('first_name')}}" id="first_name" placeholder="Enter First Name">
                @if ($errors->has('first_name'))
                        <span class="text-danger">{{ $errors->first('first_name') }}</span>
                @endif
              </div>
              <div class="col-sm-6">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" name="last_name" value="{{old('last_name')}}" id="last_name" placeholder="Enter Last Name">
                @if ($errors->has('last_name'))
                        <span class="text-danger">{{ $errors->first('last_name') }}</span>
                @endif
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-sm-6">
                <label for="app_name">Country</label>
                <select class="form-control" name="country">
                      <option value="">--Select Country--</option>
                      @foreach($countries as $key=>$country)
                      <option <?php echo (old('country')==$country['sortname'])?"selected":'' ?> value="{!! $country['sortname'] !!}">{!! $country['name'] !!}</option>
                      @endforeach
                  </select>
                  @if ($errors->has('country'))
                    <span class="text-danger">{{ $errors->first('country') }}</span>
                  @endif
              </div>
          
              <div class="col-sm-3">
                    <label for="page_body">Status</label>
                    <select  class="form-control" name="status">
                        <option value="publish" <?php echo (old('status')=='publish')?"selected":'' ?>>Publish</option>
                        <option value="draft" <?php echo (old('status')=='draft')?"selected":'' ?>>Draft</option>
                      </select>
                      @if ($errors->has('status'))
                        <span class="text-danger">{{ $errors->first('status') }}</span>
                      @endif
              </div>
            </div>
          </div>
          
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
  </div>
@endsection
@section('script')
  <script type="text/javascript">
    $(function () {
      $("#domain_name").keyup(function(){
        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        var domain__name = $("#domain_name").val();
        var letters = /^[0-9a-zA-Z_ ]+$/;
        if($("#domain_name").val().match(letters)){
          $('.app_name_domain_error').html('');
        }else{
          $('.app_name_domain_error').html('Please enter alphanumeric value');
          return false;
        }
        if(domain__name.length >= 3){
            $("#domain_name_checking").html('Checking availability...');
            $.ajax({
                 type:'POST',
                 url:base_url+'/godpanel/check-domain',
                 data:{domain:domain__name},
                 success:function(response){
                    $("#domain_name_checking").html('Domain');
                    if(response.status=='success'){
                      $("#domain_name").removeClass("is-warning");
                      $("#domain_name").removeClass("is-invalid");
                      $("#domain_name").addClass("is-valid");
                      $("#domain_name").val(response.domain);
                    }else{
                      $("#domain_name").removeClass("is-warning");
                      $("#domain_name").removeClass("is-valid");
                      $("#domain_name").addClass("is-invalid");
                    }
                 }
              });
        }
      });
    });

  </script>
@endsection