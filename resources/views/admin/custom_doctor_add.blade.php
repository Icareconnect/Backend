@extends('layouts.vertical', ['title' => 'New Physiotherapist'])
@section('content')
 <!-- Start Content-->
<div class="container-fluid">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
          <div class="card-header">
              <h3>New Physiotherapist</h3>
          </div>

          <div class="card-body">
            <form action="{{ url('admin/centre/doctor/create') }}" method="POST">
              @csrf
              <div class="form-group row">
                <div class="col-md-6">
                  <label>First Name</label>
                  <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" class="form-control">
                   @if ($errors->has('first_name'))
                    <span class="text-danger">{{ $errors->first('first_name') }}</span>
                  @endif
                </div>
                <div class="col-md-6">
                   <label for="last_name">Last Name</label>
                   <input type="text" class="form-control" value="{{old('last_name')}}" name="last_name" id="last_name">
                   @if ($errors->has('last_name'))
                      <span class="text-danger">{{ $errors->first('last_name') }}</span>
                  @endif
                </div>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-primary">Add</button>
              </div>
            </form>
          </div>
        </div>
  </div>
  </div>      
</div>
@endsection
@section('script')
 <script type="text/javascript">
        $(function () {
            function readURL2(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    
                    reader.onload = function (e) {
                        $('#profile-img-tag-icon').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $("#app_logo").change(function(){
                readURL2(this);
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
@endsection