@extends('layouts.vertical', ['title' => 'Add Appointment'])

@section('css')

@endsection

@section('content')
<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">Add Appointment</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ url('admin/appointment/create') }}" method="post">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="POST">
        <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
          <div class="form-group">
            <label for="exampleInputEmail1">Patient</label>
            <select name="patient" required="" class="form-control">
              <option>--Select Patient--</option>
              @foreach($customers as $customer)
              <option value="{{ $customer->id }}">{{ $customer->name }}</option>
              @endforeach
            </select>
            @if ($errors->has('patient'))
                    <span class="text-danger">{{ $errors->first('patient') }}</span>
            @endif
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">Physiotherapist</label>
            <select name="physiotherapist" required="" class="form-control">
              <option>--Choose Physiotherapist--</option>
              @foreach($doctors as $doctor)
              @php $d_detail = json_decode($doctor->raw_detail); @endphp
              <option value="{{ $doctor->id }}">{{ $d_detail->first_name.' '.$d_detail->last_name }}</option>
              @endforeach
            </select>
            @if ($errors->has('physiotherapist'))
                    <span class="text-danger">{{ $errors->first('physiotherapist') }}</span>
            @endif
          </div>
          <div class="form-group">
               <label for="appointment_date">Appointment Date/Time</label>
              <input type="datetime-local" required="" class="form-control" id="appointment_date" placeholder="y-m-d" name="appointment_date">
              @if ($errors->has('appointment_date'))
                    <span class="text-danger">{{ $errors->first('appointment_date') }}</span>
              @endif
          </div>
          <!-- <div class="form-group">
             <label for="service">Reason For Appoitment</label>
            <textarea required=""  class="form-control" id="service" placeholder="Service" name="service"></textarea>
            @if ($errors->has('service'))
                    <span class="text-danger">{{ $errors->first('service') }}</span>
              @endif
          </div> -->
        <!-- /.card-body -->
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
  </div>

   @endsection

@section('script')
@endsection