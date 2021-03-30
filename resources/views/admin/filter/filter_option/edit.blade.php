@extends('layouts.vertical', ['title' => 'Marketing'])
@section('css')
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />

@endsection
@section('content')
<div class="row justify-content-center">
   <div class="col-md-12">
   		<div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ url('admin/categories/'.$filter_option->category_id.'/filters/'.$filter_option->filter_type_id.'/edit')}}">Back</a></li>
                    <li class="breadcrumb-item active">Custom Info</li>
                </ol>
            </div>
             
        </div>
      <div class="card">
         <div class="card-header">
            <h3>Update Info </h3>
         </div>
         <div class="card">
            <div class="card-body">
               <form role="form" action="{{ url('admin/filter_option/update').'/'.$filter_option->id}}" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="hidden" name="_method" value="post">
                  @if (session('status'))
                  <div class="alert alert-success">
                     {{ session('status') }}
                  </div>
                  @endif
                  <div class="form-group row">
                     <div class="col-md-6">
                        <label for="name">Description</label>
                        <input type="text" class="form-control" name="description" value="{{ $filter_option->description }}" placeholder="Description" id="description">
                        @if ($errors->has('description'))
                        <span class="text-danger">{{ $errors->first('description') }}</span>
                        @endif
                     </div>
                     <div class="col-md-6">
                        <label for="exampleInputFile2">Video</label>
                        <div class="input-group">
                           <div >
                              <input id="exampleInputFile2" type="file" value="{{old('video') ?? $filter_option->video }}" name="video" id="ct-img-file" accept="video/*">
                              <video width="200" height="200" controls>
	                              <source src="{{ Storage::disk('spaces')->url('video/'.$filter_option->video) }}"
	                                    type="video/webm">
	                            <source src="{{ Storage::disk('spaces')->url('video/'.$filter_option->video) }}"
	                                    type="video/mp4">
	                              Your browser does not support the video tag.
	                            </video>
                              @if ($errors->has('video'))
                              <span class="text-danger">{{ $errors->first('video') }}</span>
                              @endif
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="form-group row">
                     <div class="col-md-6">
                        <label for="banner">Session Banner</label>
                        <div class="input-group">
                           <input type="file"  value="{{ $filter_option->banner }}" name="banner" id="banner">
                           <img src="{{ Storage::disk('spaces')->url('uploads/'.$filter_option->banner) }}" id="profile-img-tag-icon" width="200px" height="200px" />
                           @if ($errors->has('banner'))
                           <span class="text-danger">{{ $errors->first('banner') }}</span>
                           @endif
                        </div>
                     </div>
                  </div>
                  <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
               </form>
            </div>
            <!-- end card-body-->
         </div>
         <!-- end card-->
      </div>
   </div>
</div>
@endsection
@section('script')
    <script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
@endsection
