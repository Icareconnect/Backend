@extends('layouts.vertical', ['title' => 'Add '.__('text.Vendor')])

@section('css')

@endsection

@section('content')
<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">Add {{ __('text.Vendor') }}</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ url('admin/consultants')}}" method="post">
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
            <input type="email" class="form-control" name="email" value="{{ old('email') }}" id="exampleInputEmail1" placeholder="Enter email">
            @if ($errors->has('email'))
                    <span class="text-danger">{{ $errors->first('email') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" id="name" placeholder="name">
            @if ($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="name">Password</label>
            <input type="password" name="password" class="form-control" value="{{ old('password') }}" id="password" placeholder="password">
            @if ($errors->has('password'))
                    <span class="text-danger">{{ $errors->first('password') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" id="phone" placeholder="phone">
            @if ($errors->has('phone'))
                    <span class="text-danger">{{ $errors->first('phone') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="experience">Experience</label>
            <input type="text" name="experience" class="form-control" value="{{ old('experience') }}" id="experience" placeholder="Experience">
            @if ($errors->has('experience'))
                    <span class="text-danger">{{ $errors->first('experience') }}</span>
            @endif
          </div>
          <!-- <div class="form-group">
            <label for="chat_price">Chat Price/Minute</label>
            <input type="number" min="5" name="chat_price" class="form-control" value="{{ old('chat_price') }}" id="chat_price" placeholder="chat price in per minute">
            @if ($errors->has('chat_price'))
                    <span class="text-danger">{{ $errors->first('chat_price') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="call_price">Call Price/Second</label>
            <input type="number" min="1" name="call_price" class="form-control" value="{{ old('call_price') }}" id="call_price" placeholder="call price per minute">
            @if ($errors->has('call_price'))
                    <span class="text-danger">{{ $errors->first('call_price') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="speciality">Speciality</label>
            <input type="text" name="speciality" class="form-control" value="{{ old('speciality') }}" id="speciality" placeholder="speciality">
            @if ($errors->has('speciality'))
                    <span class="text-danger">{{ $errors->first('speciality') }}</span>
            @endif
          </div> -->
          <div class="form-group">
            <label for="page_body">Category</label>
            <select class="form-control" name="category">
                <option value="">--Select Status--</option>
                @foreach($parentCategories as $cat_key=>$parentCategory)
                <option <?php echo (old('category') ==$parentCategory->id)?"selected":'' ?>  value="{{ $parentCategory->id }}">{{ $parentCategory->name }}</option>
                @endforeach
              </select>
              @if ($errors->has('category'))
                <span class="text-danger">{{ $errors->first('category') }}</span>
              @endif
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