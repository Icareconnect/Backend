@extends('layouts.vertical', ['title' => 'Add '.__('text.User')])

@section('css')

@endsection

@section('content')
<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">Add {{ __('text.User') }}</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ url('admin/patient/create') }}" method="post">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="POST">
        <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
          <div class="form-group">
            <label for="exampleInputEmail1">Email address</label>
            <input type="email" class="form-control" required="" name="email" value="{{ old('email') }}" id="exampleInputEmail1" placeholder="Enter email">
            @if ($errors->has('email'))
                    <span class="text-danger">{{ $errors->first('email') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="name">Patient Name</label>
            <input type="text" name="name" required="" class="form-control" value="{{ old('name') }}" id="name" placeholder="name">
            @if ($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="phone">Contact Number</label>
            <input type="text" name="phone" required="" class="form-control" value="{{ old('phone') }}" id="phone" placeholder="phone">
            @if ($errors->has('phone'))
                    <span class="text-danger">{{ $errors->first('phone') }}</span>
            @endif
          </div>
          <div class="form-group">
               <label for="dob">DOB</label>
              <input type="date" required="" class="form-control" id="dob" placeholder="y-m-d" name="dob">
              @if ($errors->has('dob'))
                    <span class="text-danger">{{ $errors->first('dob') }}</span>
              @endif
            </div>
            
          <div class="form-group">
             <label for="source">Source By</label>
             <select name="source" class="form-control" id="source">
              <option value="app">App</option>
              <option value="referral">Referral</option>
              <option value="walk_in">Walk-In</option>
              <option value="marketing">Marketing</option>
            </select>
            @if ($errors->has('source'))
                    <span class="text-danger">{{ $errors->first('source') }}</span>
              @endif
          </div>
          <div class="form-group">
             <label for="about">About</label>
            <textarea  class="form-control" id="about" placeholder="About" name="about"></textarea>
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