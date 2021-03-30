  @extends('adminlte::page')

  @section('title', 'Update Category')

  @section('content_header')
  <h1>Update Category</h1>
  @stop
  @section('content')
<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">Update Category</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ url('admin/categories').'/'.$category->id}}" method="post" enctype="multipart/form-data">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="put">
        <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" value="{{ $category->name }}" id="name">
            @if ($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="color_code">Color Code</label>
            <input type="text" class="form-control my-colorpicker1" name="color_code" value="{{old('color_code') ?? $category->color_code }}" placeholder="Color Code">
            @if ($errors->has('color_code'))
                    <span class="text-danger">{{ $errors->first('color_code') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="page_body">Description</label>
            <textarea  name="description"  id="page_body" placeholder="Place some text here">{{{old('description') ?? $category->description }}}</textarea>
            @if ($errors->has('description'))
                    <span class="text-danger">{{ $errors->first('description') }}</span>
            @endif
          </div>
          <div class="form-group">
              <label for="exampleInputFile">Image</label>
              <div class="input-group">
                <div >
                  <input type="file" value="{{old('image') ?? $category->image }}" name="image" id="ct-img-file">
                  <img src="{{ url('/').'/media/'.$category->image }}" id="profile-img-tag" width="200px" />
                </div>
              </div>
          </div>
          <div class="form-group">
              <label for="image_icon">Icon</label>
              <div class="input-group">
                <div >
                  <input type="file" value="{{old('image_icon') ?? $category->image_icon }}" name="image_icon" id="image_icon">
                  <img src="{{ url('/').'/media/'.$category->image_icon }}" id="profile-img-tag-icon" width="200px" />
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
    @stop