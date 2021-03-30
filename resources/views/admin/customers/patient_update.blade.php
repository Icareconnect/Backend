@extends('layouts.vertical', ['title' => 'Update '.__('text.User')])

@section('css')

@endsection

@section('content')
<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">Update {{ __('text.User') }} Detail</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ url('admin/customers').'/'.$customer->id}}" method="post">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="PUT">
        <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
          <div class="form-group">
            <label for="exampleInputEmail1">Email address</label>
            <input type="email" class="form-control" name="email" value="{{ $customer->email }}" id="exampleInputEmail1" placeholder="Enter email">
            @if ($errors->has('email'))
                    <span class="text-danger">{{ $errors->first('email') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="name"> {{ __('text.User') }} Name</label>
            <input type="text" name="name" class="form-control" value="{{ $customer->name }}" id="name" placeholder="name">
            @if ($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ $customer->phone }}" id="phone" placeholder="phone">
            @if ($errors->has('phone'))
                    <span class="text-danger">{{ $errors->first('phone') }}</span>
            @endif
          </div>
          <div class="form-group">
             <label for="dob">DOB</label>
            <input type="date" class="form-control" id="dob" placeholder="y-m-d" name="dob" value="{{($customer->profile)?$customer->profile->dob:''}}">
            <span class="alert-danger dob_error"></span>
          </div>
          <div class="form-group">
           <label for="source">Source By</label>
           <select name="source" class="form-control" id="source">
            <option value="app" {{ ($customer->source=='app')?'selected':'' }}>App</option>
            <option value="referral" {{ ($customer->source=='referral')?'selected':'' }}>Referral</option>
            <option value="walk_in" {{ ($customer->source=='walk_in')?'selected':'' }}>Walk-In</option>
            <option value="marketing" {{ ($customer->source=='marketing')?'selected':'' }}>Marketing</option>
          </select>
          @if ($errors->has('source'))
                  <span class="text-danger">{{ $errors->first('source') }}</span>
            @endif
        </div>
        <div class="form-group">
          <label for="about">About</label>
            <textarea  class="form-control" id="about" placeholder="About" name="about">{{($customer->profile)?$customer->profile->about:''}}</textarea>
            @if ($errors->has('about'))
                    <span class="text-danger">{{ $errors->first('about') }}</span>
              @endif
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