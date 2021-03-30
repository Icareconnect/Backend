@extends('layouts.vertical', ['title' => 'View Question'])

@section('content')
<?php if(isset($_COOKIE['royo_timZone'])){ $timeZone = $_COOKIE['royo_timZone'];}else{ $timeZone = 'Asia/Calcutta';} ?>
<div class="card card-primary">
  <div class="card-header">
      <h4 class="card-title">View Appointment</h4>
    </div>
        <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
          <h4>Appointment Info</h4> 
          <div class="form-group row">
              <div class="col-sm-4">
                <label for="">Status: </label> <strong>{{ $request_info->requesthistory->status }}
                  </strong>
              </div>
              <div class="col-sm-4">
                <label for="">Appointment Date: </label> <strong>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $request_info->booking_date)->tz($timeZone)->format('d M Y h:i A') }}</strong>
                
              </div>
          </div>
          <div class="form-group row">
              <div class="col-sm-4">
                <label for="">Total Hours: </label> <strong>{{ $request_info->total_hours }} ({{ $request_info->requesthistory->status }})
                  </strong>
              </div>
              <div class="col-sm-4">
                <label for="">Patient Verify Hours: </label> <strong>{{ $request_info->user_by_hours}} ({{ $request_info->user_status}})</strong>
                
              </div>
              <div class="col-sm-4">
                <label for="">Admin Verify Hours: </label> <strong>{{ $request_info->verified_hours }}({{ $request_info->admin_status}})</strong>
                
              </div>
          </div>
          <h4>Patient Info</h4> 
          <div class="form-group row">
              <div class="col-sm-4">
                <label for="">Patient Name: </label> <strong>{{ $request_info->cus_info->name }}</strong>
              </div>
              <div class="col-sm-4">
                <label for="">Patient Email: </label> <strong>{{ $request_info->cus_info->email }}</strong>
              </div>
              <div class="col-sm-4">
                <label for="">Patient Phone: </label> <strong>{{ $request_info->cus_info->phone }}</strong>
              </div>
          </div>
          <h4>Nurse Info</h4> 
          <div class="form-group row">
              <div class="col-sm-4">
                <label for="">Nurse Name: </label> <strong>{{ $request_info->sr_info->name }}</strong>
              </div>
              <div class="col-sm-4">
                <label for="">Nurse Email: </label> <strong>{{ $request_info->sr_info->email }}</strong>
              </div>
              <div class="col-sm-4">
                <label for="">Nurse Phone: </label> <strong>{{ $request_info->sr_info->phone }}</strong>
              </div>
          </div>
  </div>
@endsection