@extends('layouts.vertical', ['title' => 'Edit Subscription'])
@section('css')
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">New Subscriptions</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ route('edit-subscription',['subscription_id'=>$subscription->id])}}" method="post">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="put">
        <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
          <div class="form-group">
            <div class="row">
              <div class="col-sm-6">
                <label for="name">Name</label>
                <input  type="text" class="form-control" name="name" value="{{old('name')?? $subscription->name}}" placeholder="Enter Name" required="">
                @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
              </div>
              <div class="col-sm-4">
                <label for="app_name">Type</label>
                <select class="form-control" name="type">
                      <option value="">--Select--</option>
                      <option <?php echo (old('type')?? $subscription->type=='monthly')?"selected":'' ?> value="monthly">Monthly</option>
                      <option <?php echo (old('type')?? $subscription->type=='weekly')?"selected":'' ?> value="weekly">Weekly</option>
                  </select>
                  @if ($errors->has('type'))
                    <span class="text-danger">{{ $errors->first('type') }}</span>
                  @endif 
                </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-sm-6">
                <label for="name">Price</label>
                <input  type="number" class="form-control" name="price" value="{{ old('price')?? $subscription->price }}" placeholder="Price" required="">
                @if ($errors->has('price'))
                        <span class="text-danger">{{ $errors->first('price') }}</span>
                @endif
              </div>
              <div class="col-sm-4">
                <label for="app_name">Global Subscription</label>
                <select class="form-control" name="global_subscription">
                      <option value="">--Select--</option>
                      <option <?php echo (old('global_subscription') ?? $subscription->global_type=='yes')?"selected":'' ?> value="yes">Yes</option>
                      <option <?php echo (old('global_subscription') ?? $subscription->global_type=='no')?"selected":'' ?> value="no">No</option>
                  </select>
                  @if ($errors->has('global_subscription'))
                    <span class="text-danger">{{ $errors->first('global_subscription') }}</span>
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