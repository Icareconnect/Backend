@extends('layouts.vertical', ['title' => 'Edit Page'])

@section('css')
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/summernote/summernote.min.css')}}" rel="stylesheet" type="text/css" />

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
                        <li class="breadcrumb-item active">Edit Page</li>
                    </ol>
                </div>
            </div>
        </div>
    </div> 
<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">Edit Page</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ url('admin/pages').'/'.$page->id}}" method="post">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="put">
        <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
          <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" disabled class="form-control" value="{{ $page->slug }}" id="slug">
          </div>
          <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" name="title" value="{{old('title') ?? $page->title }}" id="title" placeholder="Enter Title">
            @if ($errors->has('title'))
                    <span class="text-danger">{{ $errors->first('title') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="page_body">Body</label>
            <textarea class="page_body" id="summernote-basic" name="body" placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{{old('body') ?? $page->body }}}</textarea>
            @if ($errors->has('body'))
                    <span class="text-danger">{{ $errors->first('body') }}</span>
            @endif
          </div>
          <div class="row">
            <div class="col-sm-3">
              <!-- select -->
              <div class="form-group">
                  <label for="page_body">status</label>
                  <select class="form-control" name="status">
                      <option value="publish" <?php echo ($page->status=='publish')?"selected":'' ?>>Publish</option>
                      <option value="draft" <?php echo ($page->status=='draft')?'selected':'' ?>>Draft</option>
                    </select>
                    @if ($errors->has('status'))
                      <span class="text-danger">{{ $errors->first('status') }}</span>
                    @endif
                 </div>
            </div>
            <div class="col-sm-6">
            </div>
          </div>
          
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
  </div>
</div>
@endsection

@section('script')
    <!-- Plugins js-->
    <script src="{{asset('assets/libs/summernote/summernote.min.js')}}"></script>

    <!-- Page js-->
    <script src="{{asset('assets/js/pages/form-summernote.init.js')}}"></script>
@endsection