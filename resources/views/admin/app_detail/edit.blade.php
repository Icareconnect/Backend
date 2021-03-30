@extends('layouts.vertical', ['title' => 'Update App Detail'])
@section('css')
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
                     <h4 class="page-title">Update App Detail</h4>
                </div>
            </div>
        </div>
         <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3 header-title">Update</h4>
                        <form role="form" action="{{ url('admin/app_detail').'/'.$appDetail->id}}" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                              <input type="hidden" name="_method" value="put">
                                @if(session()->has('message'))
                                  <div class="alert alert-success">
                                      {{ session()->get('message') }}
                                  </div>
                              @endif
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="example-color">Background Color</label><br>
                                        <input id="example-color" type="color" name="background_color" value="{{old('background_color') ?? $appDetail->background_color }}" placeholder="Color Code">
                                        @if ($errors->has('background_color'))
                                                <span class="text-danger">{{ $errors->first('background_color') }}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                      <label for="app_logo">App Logo</label>
                                      <div class="input-group">
                                          <input type="file" value="{{old('app_logo') ?? $appDetail->app_logo }}" name="app_logo" id="app_logo">
                                          <img src="{{ Storage::disk('spaces')->url('uploads/'.$appDetail->app_logo) }}" id="profile-img-tag-icon" width="200px" />
                                          @if ($errors->has('app_logo'))
                                                <span class="text-danger">{{ $errors->first('app_logo') }}</span>
                                        @endif
                                      </div>
                                  </div>
                                  </div>
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div>
            <!-- end col -->
        </div>
    </div>
@endsection
@section('script')
     <script src="{{asset('assets/libs/datatables/datatables.min.js')}}"></script>

     <script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
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