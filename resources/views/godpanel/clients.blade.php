	@extends('layouts.vertical', ['title' => 'Clients'])

	@section('css')
	    <!-- Plugins css -->
	    <link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />

	@endsection

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
                            <li class="breadcrumb-item active">Clients</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div> 
		<div class="row">
		    <div class="col-12">
		      <div class="card">
		        <div class="card-header">
		          <h5 class="card-title">Client Listing</h5>
		           <a href="{{ url('client/create')}}" class="btn btn-sm btn-info float-right">Add New Client</a>
		        </div>
		        <!-- /.card-header -->
		        <div class="card-body">
		          	<table id="scroll-horizontal-datatable" class="table w-100 nowrap">
		            <thead>
		            <tr >
		            	<th>Sr No.</th>
		            	<th>APP Name</th>
		            	<th>Domain</th>
		            	<th>Client Key</th>
		            	<th>Features</th>
		            	<th>Status</th>
		            	<th>Data Base</th>
		            	<th>First Name</th>
		            	<th>Last Name</th>
		            	<th>Email</th>
		            	<th>Action</th>
		            </tr>
		            </thead>
		            <tbody>
		             @foreach($clients as $index => $client)
			            <tr>
			              <td>{{ $index+1 }}</td>
			              <td>{{ $client->name }}</td>
			              <td><a href="{{ 'https://'.$client->domain_name.'.'.env('MAIN_DOMAIN') }}" target="blank">{{ $client->domain_name }}</a></td>
			              <td>{{ $client->client_key }}</td>
			              <td><a href="{{ route('client-features',['client_id'=>$client->id])}}" class="btn btn-sm btn-info float-left">Assign</a></td>
			              <td>{{ $client->client_status }}</td>
			              <td>{{ ($client->db_id)?'db_'.$client->db_id:'NA' }}</td>
			              <td>{{ $client->first_name }}</td>
			              <td>{{ $client->last_name }}</td>
			              <td>{{ $client->email }}</td>
			              <td>
			              	<a href="{{ 'https://'.$client->domain_name.'.'.env('MAIN_DOMAIN').'/admin/dashboard' }}" class="btn btn-sm btn-info float-left">View</a>
			              </td>
			            </tr>
			         @endforeach   
		        	</tbody>
		          </table>
				</div>
	<!-- /.card-body -->
	</div>
	<!-- /.card -->
	</div>
	<!-- /.col -->
	</div>
</div>
@endsection

@section('script')
    <!-- Plugins js-->
    <script src="{{asset('assets/libs/datatables/datatables.min.js')}}"></script>
    <!-- <script src="{{asset('assets/libs/pdfmake/pdfmake.min.js')}}"></script> -->

    <!-- Page js-->
    <script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>
   
@endsection