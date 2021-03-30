@extends('layouts.vertical', ['title' => 'Create Service Type'])
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3>Add Service Type</h3>
            </div>

            <div class="card-body">
              <form action="{{ route('services') }}" method="POST" enctype="multipart/form-data">
                @csrf
                 <div class="form-group">
                  <div class="row">
                  <div class="col-sm-4">
                    <label>Service Name</label>
                    <input id="service__name" type="text" name="service_name" class="form-control" value="{{ old('service_name') }}" placeholder="Service Name">
                    @if ($errors->has('service_name'))
                      <span class="text-danger">{{ $errors->first('service_name') }}</span>
                    @endif
                    <span class="text-danger service__name_error"></span>
                  </div>
                  <div class="col-sm-4">
                    <label>Description</label>
                    <input name="description" type="text" value="{{ old('description') }}" class="form-control" placeholder="description">
                     @if ($errors->has('description'))
                      <span class="text-danger">{{ $errors->first('description') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="form-group">
                  <div class="row">
                    <div class="col-sm-4">
                      <label>Need Availability </label>
                        <select  class="form-control" name="need_availability">
                          <option value="">--Select Status--</option>
                          <option value="1" <?php echo (old('need_availability')=='true')?"selected":'' ?>>True</option>
                          <option value="0" <?php echo (old('need_availability')=='false')?"selected":'' ?>>False</option>
                        </select>
                        @if ($errors->has('need_availability'))
                          <span class="text-danger">{{ $errors->first('need_availability') }}</span>
                        @endif
                    </div>
                    <div class="col-sm-4">
                      <label>Color picker:</label>
                      <input name="color_code" type="color" value="{{ old('color_code') }}" class="form-control my-colorpicker1">
                       @if ($errors->has('color_code'))
                        <span class="text-danger">{{ $errors->first('color_code') }}</span>
                      @endif
                  </div>
                </div>
            </div>
            <div class="form-group">
                  <div class="row">
                      <div class="col-sm-4">
                        <label>Service Type </label>
                          <select class="form-control" name="service_type">
                              <option value="">--Select Service--</option>
                              @foreach($service_types as $cat_key=>$service_type)
                              <option <?php echo (old('service_type')==$service_type->name)?"selected":'' ?>  value="{{ $service_type->name }}">{{ $service_type->name }}</option>
                              @endforeach
                            </select>
                          @if ($errors->has('service_type'))
                            <span class="text-danger">{{ $errors->first('service_type') }}</span>
                          @endif
                      </div>
                </div>
            </div>
                <div class="form-group">
                  <button type="submit" class="btn btn-primary">Create</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    </div>
@endsection
@section('script')
@endsection