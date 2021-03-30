@extends('layouts.vertical', ['title' => 'Edit Variable'])
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
          <h3 class="card-title">Update Variable</h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form role="form" action="{{ url('admin/service_enable').'/'.$enableservice->id}}" method="post">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="_method" value="PUT">
          <input type="hidden" name="service_id" value="{{ $enableservice->id }}">
          <div class="card-body">
            <div class="form-group">
              <label>Service Type</label>
              <input type="text" disabled class="form-control" value="{{ $enableservice->type }}">
            </div>
            <div class="form-group">
              @if($enableservice->type=='charges')
                 <div class="form-group">
                    <label for="exampleInputEmail1">Key Name</label>
                    <input type="text" disabled class="form-control" name="key_name" id="exampleInputEmail1" value="{{ $enableservice->key_name }}">
                  </div>
                  <div class="form-group">
                    <label>Value in %</label>
                    <input type="text" class="form-control" name="value" placeholder="%" value="{{ $enableservice->value }}">
                  </div>
              @endif
              @if($enableservice->type=='audio/video')
                <select class="form-control" name="value">
                  <!-- <option value="twillio" <?php echo ($enableservice->value=='twillio')?"selected":'' ?>>Twillio</option>
                  <option value="exotel" <?php echo ($enableservice->value=='exotel')?'selected':'' ?>>Exotel</option>
                  <option value="twilio_video" <?php echo ($enableservice->value=='twilio_video')?'selected':'' ?>>Twilio Video</option> -->
                  <option value="jistimeet_video" <?php echo ($enableservice->value=='jistimeet_video')?'selected':'' ?>>Jistimeet Video</option>
                </select>
              @endif
              @if($enableservice->type=='unit_price')
              <div class="form-group">
                    <label for="exampleInputEmail1">Key Name</label>
                    <input type="text" disabled class="form-control" name="key_name" id="exampleInputEmail1" value="{{ $enableservice->key_name }}">
                </div>
                <div class="form-group">
                    @if(Config('client_connected') && Config::get("client_data")->domain_name=="intely")
                      <label>Value in Hour</label>
                      <input type="number" class="form-control" name="value" placeholder="Hour" value="{{ $enableservice->value/60 }}">
                    @else
                      <label>Value in minute</label>
                      <input type="number" class="form-control" name="value" placeholder="minute" value="{{ $enableservice->value }}">
                    @endif
                  </div>
              @endif
              @if($enableservice->type=='booking_delay')
              <div class="form-group">
                    <label for="exampleInputEmail1">Key Name</label>
                    <input type="text" disabled class="form-control" name="key_name" id="exampleInputEmail1" value="{{ $enableservice->key_name }}">
                </div>
              <div class="form-group">
                    <label>Value in Hour</label>
                    <input type="number" class="form-control" name="value" placeholder="Hour" value="{{ $enableservice->value }}">
                </div>
              @endif

              @if($enableservice->type=='slot_duration')
              <div class="form-group">
                    <label for="exampleInputEmail1">Key Name</label>
                    <input type="text" disabled class="form-control" name="key_name" id="exampleInputEmail1" value="{{ $enableservice->key_name }}">
                </div>
                <select class="form-control" name="value">
                  <option value="15" <?php echo ($enableservice->value=='10')?"selected":'' ?>>15 Minutes</option>
                  <option value="30" <?php echo ($enableservice->value=='30')?'selected':'' ?>>30 Minutes</option>
                  <option value="45" <?php echo ($enableservice->value=='45')?'selected':'' ?>>45 Minutes</option>
                  <option value="60" <?php echo ($enableservice->value=='60')?'selected':'' ?>>60 Minutes</option>
                </select>
              @endif
              @if($enableservice->type=='vendor_approved')
              <div class="form-group">
                    <label for="exampleInputEmail1">Key Name</label>
                    <input type="text" disabled class="form-control" name="key_name" id="exampleInputEmail1" value="{{ $enableservice->key_name }}">
                </div>
                <select class="form-control" name="value">
                  <option value="yes" <?php echo ($enableservice->value=='yes')?"selected":'' ?>>Yes</option>
                  <option value="no" <?php echo ($enableservice->value=='no')?'selected':'' ?>>No</option>
                </select>
              @endif
              @if($enableservice->type=='insurance')
               <label for="exampleInputEmail1">Action</label>
                <select class="form-control" name="value">
                  <option value="yes" <?php echo ($enableservice->value=='yes')?"selected":'' ?>>Yes</option>
                  <option value="no" <?php echo ($enableservice->value=='no')?'selected':'' ?>>No</option>
                </select>
              @endif
              @if($enableservice->type=='currency')
              <div class="form-group">
                    <label for="exampleInputEmail1">Key Name</label>
                    <input type="text" disabled class="form-control" name="key_name" id="exampleInputEmail1" value="{{ $enableservice->key_name }}">
                </div>
                <select class="form-control" name="value">
                    @foreach($currecnies as $currency)
                      <option value="{{ $currency->code }}" <?php echo ($enableservice->value==$currency->code)?"selected":'' ?>>{{ $currency->symbol .' '.$currency->code }}</option>
                    @endforeach
                </select>
              @endif
              @if($enableservice->type=='set_radius')
              <div class="form-group">
                    <label for="exampleInputEmail1">Key Name</label>
                    <input type="text" disabled class="form-control" name="key_name" id="exampleInputEmail1" value="km">
                </div>
                <div class="form-group">
                    <label>Value in KM</label>
                    <input type="number" class="form-control" name="value" placeholder="KM" value="{{ $enableservice->value }}">
                </div>
              @endif
              @if($enableservice->type=='minimum_balance')
              <div class="form-group">
                    <label for="exampleInputEmail1">Key Name</label>
                    <input type="text" disabled class="form-control" name="key_name" id="exampleInputEmail1" value="{{ $enableservice->key_name }}">
                </div>
                <div class="form-group">
                    <label>Value</label>
                    <input type="number" class="form-control" name="value" placeholder="value" value="{{ $enableservice->value }}" required="">
                </div>
              @endif
            </div>
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </form>
      </div>
  </div>
@endsection

@section('script')
@endsection