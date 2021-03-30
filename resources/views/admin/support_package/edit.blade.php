@extends('layouts.vertical', ['title' => 'Update Support Package'])
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
                     <h4 class="page-title">Update Support Package</h4>
                </div>
            </div>
        </div>
         <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3 header-title">Update</h4>
                        <form role="form" action="{{ url('admin/support_packages').'/'.$masterpackage->id}}" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                              <input type="hidden" name="_method" value="put">
                                @if(session()->has('message'))
                                  <div class="alert alert-success">
                                      {{ session()->get('message') }}
                                  </div>
                              @endif
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="title">Title</label>
                                        <input type="text" class="form-control" name="title" value="{{ $masterpackage->title }}" id="title">
                                        @if ($errors->has('title'))
                                                <span class="text-danger">{{ $errors->first('title') }}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="example-color">Color Code</label><br>
                                        <input id="example-color" type="color" name="color_code" value="{{old('color_code') ??$masterpackage->color_code }}" placeholder="Color Code">
                                        @if ($errors->has('color_code'))
                                                <span class="text-danger">{{ $errors->first('color_code') }}</span>
                                        @endif
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                      <div class="col-md-6">
                                        <label for="page_body__">Description</label><br>
                                        <textarea rows="5"  name="description"  id="page_body__" placeholder="Place some text here">{{{old('description') ?? $masterpackage->description }}}</textarea>
                                        @if ($errors->has('description'))
                                                <span class="text-danger">{{ $errors->first('description') }}</span>
                                        @endif
                                      </div>
                                      <div class="col-md-6">
                                        <label for="price">Price</label>
                                        <input type="number" class="form-control" name="price" value="{{ $masterpackage->price }}" id="price">
                                        @if ($errors->has('price'))
                                                <span class="text-danger">{{ $errors->first('price') }}</span>
                                        @endif
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <div class="col-md-6">
                                      <label for="image_icon">Icon</label>
                                      <div class="input-group">
                                          <input type="file" value="{{old('image_icon') ?? $masterpackage->image_icon }}" name="image_icon" id="image_icon">
                                          <img src="{{ Storage::disk('spaces')->url('uploads/'.$masterpackage->image_icon) }}" id="profile-img-tag-icon" width="200px" />
                                          @if ($errors->has('image_icon'))
                                                <span class="text-danger">{{ $errors->first('image_icon') }}</span>
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
    <script src="{{asset('assets/libs/pdfmake/pdfmake.min.js')}}"></script>

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
            $("#image_icon").change(function(){
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