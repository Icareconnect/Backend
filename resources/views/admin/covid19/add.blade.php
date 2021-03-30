@extends('layouts.vertical', ['title' => 'Pandemic'])
@section('css')
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">Pandemic</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ url('admin/covid19')}}" method="post" enctype="multipart/form-data">
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
                      <label >Type</label>
                      <select class="form-control" name="type">
                          <option <?php echo (old('type')=='banner')?"selected":'' ?>  value="banner">Banner</option>
                          <option <?php echo (old('type')=='prevention')?"selected":'' ?>  value="prevention">Prevention</option>
                          <option <?php echo (old('type')=='symptom')?"selected":'' ?>  value="symptom">Symptom</option>
                          <option <?php echo (old('type')=='tips')?"selected":'' ?>  value="tips">Tips</option>
                        </select>
                        @if ($errors->has('type'))
                          <span class="text-danger">{{ $errors->first('type') }}</span>
                        @endif
                </div>
                  <div class="col-sm-4">
                      <label for="title">Title</label>
                      <input type="text" name="title" class="form-control" value="{{ old('title') }}" id="title" placeholder="Title">
                      @if ($errors->has('title'))
                              <span class="text-danger">{{ $errors->first('title') }}</span>
                      @endif
                </div>
           </div>
        </div>
        <div class="form-group">
             <div class="row">
                <div class="col-sm-4">
                    <label >Show Info On Click</label>
                      <select class="form-control" name="on_click_info">
                        <option value="">--Select Status--</option>
                        <option <?php echo (old('on_click_info')=='doctor_list')?"selected":'' ?>  value="doctor_list">Doctor List</option>
                      </select>
                      @if ($errors->has('on_click_info'))
                        <span class="text-danger">{{ $errors->first('on_click_info') }}</span>
                      @endif
              </div>
            </div>
        </div>
           <div class="form-group">
             <div class="row">
                  <div class="col-sm-6">
                    <label for="exampleInputFile">Image Web</label>
                    <div class="input-group">
                      <div >
                        <input type="file" value="{{old('image_web') }}" name="image_web" id="ct-img-file">
                        <img src="" id="profile-img-tag" width="200px" />
                      </div>
                    </div>
                     @if ($errors->has('image_web'))
                                    <span class="text-danger">{{ $errors->first('image_web') }}</span>
                      @endif
                  </div>
                  <div class="col-sm-6">
                     <label for="image_mobile">Image  Mobile</label>
                        <div class="input-group">
                          <div >
                            <input type="file" value="{{old('image_mobile') }}" name="image_mobile" id="image_icon">
                            <img src="" id="profile-img-tag-icon" width="200px" />
                          </div>
                        </div>
                         @if ($errors->has('image_mobile'))
                                        <span class="text-danger">{{ $errors->first('image_mobile') }}</span>
                                @endif
                  </div>
              </div>
          </div>
          <div class="form-group">
          <div class="row">
               <div class="col-sm-6">
                      <div class="custom-control custom-switch">
                        <input type="checkbox" name="enable" value="on" class="custom-control-input" id="customSwitch1">
                        <label class="custom-control-label" for="customSwitch1">Enable</label>
                    </div>
              </div>
          </div>
         </div>

         <div class="form-group">
          <div class="row">
               <div class="col-sm-6">
                      <div class="custom-control custom-switch">
                        <input type="checkbox" name="home_screen" class="custom-control-input" id="customSwitch2">
                        <label class="custom-control-label" for="customSwitch2">Show On Home Screen</label>
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