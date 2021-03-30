@extends('layouts.vertical', ['title' =>$text])

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
                            <li class="breadcrumb-item active">{{ $text }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div> 
		<div class="row">
		    <div class="col-12">
		      <div class="card">
		        <div class="card-header">
		          <h3 class="card-title">{{ $text }}</h3>
		           <a href="{{ url($action_url.'/create') }}" class="btn btn-sm btn-info float-right">Add New Custom Field</a>
		        </div>
		        <!-- /.card-header -->
		        <div class="card-body">
		          	<table id="scroll-horizontal-datatable" class="table w-100 nowrap">
		            <thead>
		            <tr >
		            	<th>Sr No.</th>
		            	<th>Field Name</th>
		            	<th>Field Type</th>
		            	<th>Show On SignUp</th>
		            	<th>Action</th>
		            </tr>
		            </thead>
		            <tbody>
		             @foreach($customfields as $index => $customfield)
			            <tr>
			              <td>{{ $index+1 }}</td>
			              <td>{{ $customfield->field_name }}</td>
			              <td>{{ $customfield->field_type }}</td>
			              <td>{{ ($customfield->required_sign_up=='1'?'Yes':'No') }}</td>
			              <td><a href="{{ url($action_url) .'/'.$customfield->id.'/edit'}}" class="btn btn-sm btn-info float-left">Edit</a>
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

    <!-- Page js-->
    <script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>

@endsection