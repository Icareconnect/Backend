@extends('layouts.vertical', ['title' => 'Update Service'])
@section('css')
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
                          <li class="breadcrumb-item active">Update Service Type</li>
                      </ol>
                  </div>
              </div>
          </div>
      </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3>Update Service Type</h3>
            </div>

            <div class="card-body">
              <form action="{{ url('admin/services').'/'.$service->id}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="service_id" value="{{ $service->id }}">
                 <div class="form-group">
                  <div class="row">
                  <div class="col-sm-4">
                     <label>Service Name</label>
                    <input id="service__name" type="text" name="service_name" class="form-control" value="{{ old('service_name')?? $service->type }}" placeholder="Service Name">
                    @if ($errors->has('service_name'))
                      <span class="text-danger">{{ $errors->first('service_name') }}</span>
                    @endif
                    <span class="text-danger service__name_error"></span>
                  </div>
                  <div class="col-sm-4">
                    <label>Description</label>
                    <input  type="text" name="description" value="{{ old('description')?? $service->description }}" class="form-control" placeholder="Description">
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
                          <option value="1" <?php echo (old('need_availability') ?? $service->need_availability =='1')?"selected":'' ?>>True</option>
                          <option value="0" <?php echo (old('need_availability') ?? $service->need_availability =='0')?"selected":'' ?>>False</option>
                        </select>
                        @if ($errors->has('need_availability'))
                          <span class="text-danger">{{ $errors->first('need_availability') }}</span>
                        @endif
                    </div>
                    <div class="col-sm-4">
                      <label>Color picker:</label>
                      <input id="example-color" name="color_code" type="color" placeholder="color code" value="{{ old('color_code')??$service->color_code }}" class="form-control">
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
                              <option <?php echo (old('service_type')==$service_type->name) || $service->service_type== $service_type->name?"selected":'' ?>  value="{{ $service_type->name }}">{{ $service_type->name }}</option>
                              @endforeach
                            </select>
                          @if ($errors->has('service_type'))
                            <span class="text-danger">{{ $errors->first('service_type') }}</span>
                          @endif
                      </div>
                  </div>
              </div>
                <div class="form-group">
                  <button type="submit" class="btn btn-primary">Update</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    </div>
  </div>
@endsection