	@extends('adminlte::page')

	@section('title', 'Service Categories')

	@section('content_header')
	@stop
	@section('content')
		<section class="content-header">
	      <div class="container-fluid">
	        <div class="row mb-2">
	          <div class="col-sm-6">
	            <ol class="breadcrumb float-sm-left">
	              <li class="breadcrumb-item"><a href="{{ url('admin/services') }}">Home</a></li>
	              <li class="breadcrumb-item active">{{ $service_data->type }} Categories</li>
	            </ol>
	          </div>
	        </div>
	      </div><!-- /.container-fluid -->
	    </section>
		<div class="row">
		    <div class="col-12">
		      <div class="card">
		        <div class="card-header">
		          <h3 class="card-title">{{ $service_data->type }}'s Category Listing</h3>
		           <a href="{{ url('admin/services/'.$service_data->id.'/categories/create')}}" class="btn btn-sm btn-info float-right">Add New Category</a>
		        </div>
		        <!-- /.card-header -->
		        <div class="card-body">
		          	<table id="customers_pagination" class="table table-bordered table-striped">
		            <thead>
		            <tr >
		            	<th>Sr No.</th>
		            	<th>Category Name</th>
		            	<th>Status (Is_Active)</th>
		            	<th>Fixed Price</th>
		            	<th>Price Minimum</th>
		            	<th>Price Maximum</th>
		            	<th>Minimum Duration</th>
		            	<th>Gap Duration</th>
		            	<th>Action</th>
		            </tr>
		            </thead>
		            <tbody>
		             @foreach($services as $index => $service)
			            <tr>
			              <td>{{ $index+1 }}</td>
			              <td>{{ $service->category->name }}</td>
			              <td>{{ ($service->is_active=='1'?'True':'False') }}</td>
			              <td>{{ $service->price_fixed!==null?$service->price_fixed:'NA' }}</td>
			              <td>{{ $service->price_minimum }}</td>
			              <td>{{ $service->price_maximum }}</td>
			              <td>{{ $service->minimum_duration }}</td>
			              <td>{{ $service->gap_duration }}</td>
			              <td><a href="{{ url('admin/services/categories') .'/'.$service->id.'/edit'}}" class="btn btn-sm btn-info float-left">Edit</a>
			              </td>
			            </tr>
			         @endforeach   
		        	</tbody>
		            <tfoot>
		            <tr>
		            	<th>Sr No.</th>
		            	<th>Category Name</th>
		            	<th>Status (Is_Active)</th>
		            	<th>Fixed Price</th>
		            	<th>Price Minimum</th>
		            	<th>Price Maximum</th>
		            	<th>Minimum Duration</th>
		            	<th>Gap Duration</th>
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