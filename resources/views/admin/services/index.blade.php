@extends('layouts.vertical', ['title' => 'Service Types'])

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
                            <li class="breadcrumb-item active">Service Types</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Service Types</h4>
                </div>
            </div>
        </div> 

	<div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted font-13 mb-4">
                            Service Types is main services  like Chat,Call etc
                        </p>
                        <a href="{{ url('admin/services/create')}}" class="btn btn-sm btn-info float-right">Add Service Type</a>

                        <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
                            	<thead>
					            	<tr >
						            	<th>Sr No.</th>
						            	<th>Service Name</th>
						            	<th>Description</th>
						            	<th>Color Code</th>
						            	<th>Need Availability</th>
						            	<th>Action</th>
					            	</tr>
					            </thead>
					            <tbody>
					             @foreach($services as $index => $service)
						            <tr>
						              <td>{{ $index+1 }}</td>
						              <td>{{ $service->type }}</td>
						              <td>{{ $service->description }}</td>
						              <td>{{ $service->color_code }}</td>
						              <td>{{ ($service->need_availability=='1'?'True':'False') }}</td>
						              <td><a href="{{ url('admin/services') .'/'.$service->id.'/edit'}}" class="btn btn-sm btn-info float-left">Edit</a>
						              	<!-- <a href="{{ url('admin/services') .'/'.$service->id.'/categories'}}" class="btn btn-sm btn-danger float-left">Categories</a> -->
						              </td>
						            </tr>
						         @endforeach   
					        	</tbody>
                        </table>

                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div>
</div>
@endsection

@section('script')
    <!-- Plugins js-->
    <script src="{{asset('assets/libs/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('assets/libs/pdfmake/pdfmake.min.js')}}"></script>

    <!-- Page js-->
    <script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>
@endsection