@extends('layouts.vertical', ['title' => $text])

@section('css')

@endsection

@section('content')
<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">{{ $text }}</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ url($action_url.'/'.$customField->id)}}" method="post">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="PUT">
        <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
          <div class="form-group">
            <div class="row">
                  <div class="col-sm-4">
                      <label for="field_name">Field Name</label>
                      <input type="text" name="field_name" class="form-control" value="{{ old('field_name')??$customField->field_name }}" id="field_name" placeholder="Field Name" required="">
                      @if ($errors->has('field_name'))
                              <span class="text-danger">{{ $errors->first('field_name') }}</span>
                      @endif
                </div>
            </div>    
          </div>
          <div class="form-group">
            <div class="row">
                  <div class="col-sm-4">
                      <label for="field_type">Field Type</label>
                      <select  class="form-control" name="field_type">
                        <option value="textbox" <?php echo (old('field_type')?? $customField->field_type=='textbox')?"selected":'' ?>>Textbox</option>
                        <option value="multiple" <?php echo (old('field_type')?? $customField->field_type=='multiple')?"selected":'' ?>>Multiple</option>
                      </select>
                      @if ($errors->has('field_type'))
                              <span class="text-danger">{{ $errors->first('field_type') }}</span>
                      @endif
                </div>
            </div>    
          </div>
          <div class="form-group">
              <div class="custom-control custom-switch">
                <input type="checkbox" name="required_sign_up" class="custom-control-input" id="customSwitch1" <?php echo($customField->required_sign_up=='1'?'checked':'') ?>>
                <label class="custom-control-label" for="customSwitch1">Show On Sign-Up</label>
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