@extends('layouts.vertical', ['title' => 'Edit Coupon'])
@section('css')
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">Edit Coupon</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
 <form role="form" action="{{ url('admin/coupon').'/'.$coupon->id}}" method="post" enctype="multipart/form-data">
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
                    <label>Date Range:</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="far fa-calendar-alt"></i>
                        </span>
                      </div>
                      <input type="text" name="date_range" value="{{ old('date_range')??$coupon->date_range }}" class="form-control float-right" id="range-datepicker">
                    </div>                     
                    @if ($errors->has('date_range'))
                              <span class="text-danger">{{ $errors->first('date_range') }}</span>
                      @endif
                </div>
                <div class="col-sm-4">
                     <label >Coupon Code</label>
                       <input type="text" style="text-transform: uppercase;"  class="form-control" value="{{ $coupon->coupon_code }}" maxlength="10"  placeholder="Coupon Code" disabled="">
                      @if ($errors->has('coupon_code'))
                              <span class="text-danger">{{ $errors->first('coupon_code') }}</span>
                      @endif
                </div>
            </div>    
          </div>
          <div class="form-group">
            <div class="row">
                  <div class="col-sm-4">
                      <label >Category</label>
                      <select class="form-control" name="category">
                          <option value="">--Select Status--</option>
                          @foreach($categories as $cat_key=>$parentCategory)
                          <option <?php echo (old('category')==$parentCategory->id || $coupon->category_id==$parentCategory->id)?"selected":'' ?>  value="{{ $parentCategory->id }}">{{ $parentCategory->name }}</option>
                          @endforeach
                        </select>
                        @if ($errors->has('category'))
                          <span class="text-danger">{{ $errors->first('category') }}</span>
                        @endif
                </div>
                <div class="col-sm-4">
                      <label >Service</label>
                      <select class="form-control" name="service">
                          <option value="">--Select Status--</option>
                          @foreach($services as $cat_key=>$service)
                          <option <?php echo (old('service')==$service->id || $coupon->service_id==$service->id)?"selected":'' ?>  value="{{ $service->id }}">{{ $service->type }}</option>
                          @endforeach
                        </select>
                        @if ($errors->has('service'))
                          <span class="text-danger">{{ $errors->first('service') }}</span>
                        @endif
                </div>
            </div>
           </div>
           <div class="form-group">
            <div class="row">
                  <div class="col-sm-4">
                      <label >Discount Type</label>
                      <select class="form-control" name="discount_type">
                          <option <?php echo (old('discount_type')=='percentage' || $coupon->discount_type=='percentage')?"selected":'' ?>  value="percentage">Percentage</option>
                          <option <?php echo (old('discount_type')=='currency' || $coupon->discount_type=='currency')?"selected":'' ?>  value="currency">Currency</option>
                        </select>
                        @if ($errors->has('discount_type'))
                          <span class="text-danger">{{ $errors->first('discount_type') }}</span>
                        @endif
                </div>
                <div class="col-sm-4">
                      <label >Discount Value</label>
                       <input type="number" name="discount_value" class="form-control" value="{{ old('discount_value')??$coupon->discount_value }}" id="discount_value" placeholder="Discount Value">
                      @if ($errors->has('discount_value'))
                              <span class="text-danger">{{ $errors->first('discount_value') }}</span>
                      @endif
                </div>
            </div>
           </div>
           <div class="form-group">
            <div class="row">
                <div class="col-sm-4">
                      <label >Minimum Discount On-value</label>
                       <input type="number" name="minimum_value" class="form-control" value="{{ old('minimum_value')??$coupon->minimum_value }}" id="minimum_value" placeholder="Minimum Value">
                      @if ($errors->has('minimum_value'))
                              <span class="text-danger">{{ $errors->first('minimum_value') }}</span>
                      @endif
                </div>
                <div class="col-sm-4">
                      <label >Maximum Discount value</label>
                       <input type="number" name="maximum_value" class="form-control" value="{{ old('maximum_value')??$coupon->maximum_discount_amount }}" id="maximum_value" placeholder="Maximum Discount value">
                      @if ($errors->has('maximum_value'))
                              <span class="text-danger">{{ $errors->first('maximum_value') }}</span>
                      @endif
                </div>
                <div class="col-sm-4">
                      <label >Limit</label>
                       <input type="number" name="limit" class="form-control" value="{{ old('limit') ?? $coupon->limit}}" id="limit" placeholder="Limit">
                      @if ($errors->has('limit'))
                              <span class="text-danger">{{ $errors->first('limit') }}</span>
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
    <!-- Plugins js-->
    <script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>
    <!-- Page js-->
    <!-- <script src="{{asset('assets/js/pages/form-pickers.init.js')}}"></script> -->
    <script type="text/javascript">
      !function ($) {
          "use strict";

          var FormPickers = function () { };

          FormPickers.prototype.init = function () {

              $('#range-datepicker').flatpickr({
                  mode: "range"
              });
              
          },
              $.FormPickers = new FormPickers, $.FormPickers.Constructor = FormPickers

      }(window.jQuery),

          //initializing 
          function ($) {
              "use strict";
              $.FormPickers.init()
          }(window.jQuery);

    </script>
@endsection