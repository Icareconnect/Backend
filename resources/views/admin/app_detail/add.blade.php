@extends('layouts.vertical', ['title' => 'App Details'])
@section('content')
 <!-- Start Content-->
<div class="container-fluid">
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
          <div class="card-header">
              <h3>App Details</h3>
          </div>

          <div class="card-body">
            <form action="{{ route('app_detail.store') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="form-group row">
                <div class="col-md-6">
                  <label>Background Color</label>
                  <input id="example-color" type="color" name="background_color" value="{{ old('background_color') }}" class="form-control">
                   @if ($errors->has('background_color'))
                    <span class="text-danger">{{ $errors->first('background_color') }}</span>
                  @endif
                </div>
                <div class="col-md-6">
                   <label for="exampleInputFile">App Logo</label>
                  <div class="input-group">
                   <input type="file" value="{{old('app_logo')}}" name="app_logo" id="app_logo">
                    <img src="" id="profile-img-tag-icon" width="200px" />
                  </div>
                   @if ($errors->has('app_logo'))
                      <span class="text-danger">{{ $errors->first('app_logo') }}</span>
                  @endif
                </div>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-primary">Save</button>
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