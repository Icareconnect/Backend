@extends('layouts.vertical', ['title' => 'Add '.__('text.Cat. Service Type')])
@section('css')
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/ion-rangeslider/ion-rangeslider.min.css')}}" rel="stylesheet" type="text/css" />

@endsection
@section('content')
   <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="{{ url('admin/services') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="{{ url('admin/categories').'/'.$category->id.'/edit'}}" >{{ $category->name }} Add {{ __('text.Cat. Service Type')}}</a></li>
              </ol>
            </div>
          </div>
    </div><!-- /.container-fluid -->
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3>Add {{ __('text.Cat. Service Type')}}</h3>
            </div>

            <div class="card-body">
              <form action="{{ url('admin/categories').'/'.$category->id.'/service/create'}}" method="POST" enctype="multipart/form-data">
                @csrf
                 <div class="form-group">
                  <div class="row">
                  <div class="col-sm-4">
                    <label>Category Name</label>
                    <input type="text"  disabled="" class="form-control" value="{{ $category->name }}" readonly="">
                    <input type="hidden" class="form-control" value="{{ $category->id }}">
                  </div>
                  <div class="col-sm-4">
                    <label>Select Service</label>
                    <select  class="form-control" name="service_id">
                      <option value="">--Select Status--</option>
                      @foreach($services as $key=>$service)
                        <option <?php echo (in_array($service->id, $add_services))?"disabled":'' ?> <?php echo (old('service_id')==$service->id)?"selected":'' ?> value="{{ $service->id }}">{{ $service->type }}</option>
                      @endforeach
                    </select>
                    @if ($errors->has('service_id'))
                      <span class="text-danger">{{ $errors->first('service_id') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="form-group">
                  <div class="row">
                  <div class="col-sm-4">
                    <label>Booking Gap Duration</label>
                    <input type="number" min="5" name="gap_duration" class="form-control" value="{{ old('gap_duration')}}" placeholder="Booking Gap Duration in seconds">
                    @if ($errors->has('gap_duration'))
                          <span class="text-danger">{{ $errors->first('gap_duration') }}</span>
                    @endif
                  </div>
                  <div class="col-sm-4">
                   <label>Minimum Duration</label>
                        <input type="number" min="5" name="minimum_duration" class="form-control" value="{{ old('minimum_duration') }}" placeholder="Minimum Duration in seconds">
                        @if ($errors->has('minimum_duration'))
                          <span class="text-danger">{{ $errors->first('minimum_duration') }}</span>
                        @endif
                  </div>
                  <div class="col-sm-4">
                   <label>Price Calculation Active</label>
                       <select  class="form-control" name="price_calculation_active">
                            <option value="fixed_price" <?php echo (old('price_calculation_active')=='fixed_price')?"selected":'' ?>>Fixed Price</option>
                            <option value="price_range" <?php echo (old('price_calculation_active')=='price_range')?"selected":'' ?>>Price Range</option>
                        </select>
                        @if ($errors->has('price_calculation_active'))
                          <span class="text-danger">{{ $errors->first('price_calculation_active') }}</span>
                        @endif
                  </div>
                </div>
              </div>
              <div class="form-group">
                  <div class="row">
                  <div class="col-sm-4">
                    <label>Price Fixed</label>
                    <input type="number" min="0" name="fixed_value" class="form-control" value="{{ old('fixed_value') }}" placeholder="Add Fixed Price ">
                    @if ($errors->has('fixed_value'))
                          <span class="text-danger">{{ $errors->first('fixed_value') }}</span>
                    @endif
                  </div>
                  <div class="col-sm-4">
                   <label>Price Range</label>
                      <div class="slider-green">
                        <input value="{{ old('price_range') }}" id="range_04" type="text" name="price_range"  class="form-control">
                      </div>
                        @if ($errors->has('price_range'))
                          <span class="text-danger">{{ $errors->first('price_range') }}</span>
                        @endif
                  </div>
                  <div class="col-sm-4">
                   <label>Active</label>
                       <select  class="form-control" name="is_active">
                            <option value="1" <?php echo (old('is_active')=='1')?"selected":'' ?>>True</option>
                            <option value="0" <?php echo (old('is_active')=='0')?"selected":'' ?>>False</option>
                        </select>
                        @if ($errors->has('is_active'))
                          <span class="text-danger">{{ $errors->first('is_active') }}</span>
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
    <!-- Plugins js-->
    <script src="{{asset('assets/libs/ion-rangeslider/ion-rangeslider.min.js')}}"></script>
    <script type="text/javascript">
      $(document).ready(function () {
          $("#range_04").ionRangeSlider({
              type: "double",
              grid: true,
              min: 5,
              max: 100,
              from: 10,
              to: 20
          });
      });
    </script>
@endsection