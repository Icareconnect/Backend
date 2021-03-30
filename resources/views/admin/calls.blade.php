	@extends('adminlte::page')

	@section('title', 'Call Requests')

	@section('content_header')
	<h1>Call Requests</h1>
	@stop
	@section('content')
		<div class="row">
		    <div class="col-12">
		      <div class="card">
		        <div class="card-header">
		          <h3 class="card-title">Call Requests Listing</h3>
		        </div>
		        <!-- /.card-header -->
		        <div class="card-body">
		          	<table id="customers_pagination" class="table table-bordered table-striped">
		            <thead>
		            <tr >
		            	<th>Sr No.</th>
		            	<th>Booking Date</th>
		            	<th>Raised By</th>
		            	<th>Consultant</th>
		            	<th>Status</th>
		            	<th>Duration</th>
		            	<th>Charges</th>
		            	<th>Action</th>
		            </tr>
		            </thead>
		            <tbody>
		             @foreach($calls as $index => $call)
			            <tr>
			              <td>{{ $index+1 }}</td>
			              <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $call->booking_date)->tz('Asia/Calcutta') }}</td>
			              <td>{{ $call->cus_info->name }}</td>
			              <td>{{ $call->sr_info->name }}</td>
			              <td>{{ $call->requesthistory->status }}</td>
			              <td>{{ $call->requesthistory->duration }}</td>
			              <td>@if(isset($call->transaction->amount)){{ $call->transaction->amount }}@endif</td>
			              <td></td>
			            </tr>
			         @endforeach   
		        	</tbody>
		            <tfoot>
		            <tr>
		            	<th>Sr No.</th>
		            	<th>Name</th>
		            	<th>Email</th>
		            	<th>Phone</th>
		            	<th>Status</th>
		            	<th>Duration</th>
		            	<th>Charges</th>
		            	<th>Action</th>
		            </tr>
		            </tfoot>
		          </table>
				</div>
	<!-- /.card-body -->
	</div>
	<!-- /.card -->
	</div>
	<!-- /.col -->
	</div>
	<!-- ./wrapper -->
	<!-- page script -->
	@stop