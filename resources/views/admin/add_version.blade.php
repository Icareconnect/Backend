@extends('layouts.vertical', ['title' => 'Add APP Version'])

@section('css')

@endsection

@section('content')
<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">Add APP Version</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ url('admin/app_version/create')}}" method="post">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="POST">
        <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
          <div class="form-group">
            <div class="row">
                  <div class="col-sm-4">
                      <label for="name">Device Type</label>
                       <select class="form-control" name="device_type">
                        <option value="1" <?php echo (old('device_type')=='1'?"selected":'') ?> >IOS</option>
                        <option value="2" <?php echo (old('device_type')=='2'?"selected":'') ?> >ANDROID</option>
                      </select>
                      @if ($errors->has('device_type'))
                        <span class="text-danger">{{ $errors->first('device_type') }}</span>
                      @endif
                </div>
                <div class="col-sm-4">
                      <label for="name">APP Type</label>
                       <select class="form-control" name="app_type">
                        <option value="1" <?php echo (old('app_type')=='1'?"selected":'') ?> >User</option>
                        <option value="2" <?php echo (old('app_type')=='2'?"selected":'') ?> >Vendor</option>
                      </select>
                      @if ($errors->has('app_type'))
                        <span class="text-danger">{{ $errors->first('app_type') }}</span>
                      @endif
                </div>
            </div>    
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-sm-4">
                <label for="version_number">Version Number</label>
                  <input required=""  type="number" min="1" name="version_number" class="form-control" value="{{ old('version_number') }}" id="version_number" placeholder="Version Number">
                  @if ($errors->has('version_number'))
                          <span class="text-danger">{{ $errors->first('version_number') }}</span>
                  @endif
              </div>
              <div class="col-sm-4">
                    <label for="version_name">Version Name</label>
                  <input required="" type="text" name="version_name" class="form-control" value="{{ old('version_name') }}" id="version_name" placeholder="Version Name">
                  @if ($errors->has('version_name'))
                          <span class="text-danger">{{ $errors->first('version_name') }}</span>
                  @endif
              </div>
          </div>
        </div>
        <div class="form-group">
            <div class="row">
              <div class="col-sm-4">
                  <label for="update_type">Update Type</label>
                   <select class="form-control" id="update_type" name="update_type">
                    <option value="0" <?php echo (old('update_type')=='0'?"selected":'') ?> >No-Update</option>
                    <option value="1" <?php echo (old('update_type')=='1'?"selected":'') ?> >Minor-Update</option>
                    <option value="2" <?php echo (old('update_type')=='2'?"selected":'') ?> >Major-Update</option>
                  </select>
                  @if ($errors->has('update_type'))
                    <span class="text-danger">{{ $errors->first('update_type') }}</span>
                  @endif
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
   @endsection
@section('script')
@endsection