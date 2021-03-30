@extends('layouts.vertical', ['title' => 'Create Support Package'])
@section('content')
 <!-- Start Content-->
<div class="container-fluid">
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
          <div class="card-header">
              <h3>Create Support Package</h3>
          </div>

          <div class="card-body">
            <form action="{{ route('support_packages.store') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="form-group row">
                <div class="col-md-6">
                  <label>Title:</label>
                  <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="Title" required>
                  @if ($errors->has('title'))
                    <span class="text-danger">{{ $errors->first('title') }}</span>
                  @endif
                </div>
                <div class="col-md-6">
                  <label>Color picker:</label>
                  <input id="example-color" type="color" name="color_code" value="{{ old('color_code') }}" class="form-control">
                   @if ($errors->has('color_code'))
                    <span class="text-danger">{{ $errors->first('color_code') }}</span>
                  @endif
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-6">
                    <label for="name">Price</label>
                   <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}" placeholder="Price" required>
                    @if ($errors->has('price'))
                      <span class="text-danger">{{ $errors->first('price') }}</span>
                    @endif
                </div>
                <div class="col-md-6">
                  <label>Description</label><br>
                  <textarea name="description" row="5" required="">{{ old('description') }}</textarea>
                  @if ($errors->has('description'))
                      <span class="text-danger">{{ $errors->first('description') }}</span>
                  @endif
                </div>
              </div>
              <div class="form-group">
                  <label for="exampleInputFile">Icon</label>
                  <div class="input-group">
                    <div >
                      <input type="file" value="{{ old('image_icon') }}" name="image_icon" id="exampleInputFile" required="">
                    </div>
                  </div>
                   @if ($errors->has('image_icon'))
                      <span class="text-danger">{{ $errors->first('image_icon') }}</span>
                  @endif
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
@endsection