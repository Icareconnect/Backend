@extends('layouts.vertical', ['title' => __('text.Master PreferenceLifestyle') ])
@section('css')
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
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
                            <li class="breadcrumb-item active">{{ __('text.Master PreferenceLifestyle') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div> 
		<div class="row">
		    <div class="col-12">
		      <div class="card">
		        <div class="card-header">
		          <h3 class="card-title">{{ __('text.Master PreferenceLifestyle') }} Listing</h3>
		           <a href="{{ url('admin/master/lifestyle/create')}}" class="btn btn-sm btn-info float-right">Add New</a>
		        </div>
		        <!-- /.card-header -->
		        <div class="card-body">
		          	<table id="scroll-horizontal-datatable" class="table w-100 nowrap">
		            <thead>
		            <tr >
		            	<th>Sr No.</th>
		            	<th>Name</th>
		            	<th>MuitSelect</th>
		            	<th>Type</th>
		            	<th>Options</th>
		            	<th>Action</th>
		            </tr>
		            </thead>
		            <tbody>
		             @foreach($masterpreferences as $index => $cluster)
			            <tr>
			              <td>{{ $index+1 }}</td>
			              <td>{{ $cluster->name }}</td>
			              <td>{{ ($cluster->is_multi)?"Yes":"No" }}</td>
			              <td>{{ ucwords(str_replace('_',' ',$cluster->type)) }}</td>
			              <td>{{ $cluster->options->pluck('option_name') }}</td>
			              <td><a href="{{ url('admin/master/lifestyle') .'/'.$cluster->id.'/edit'}}" class="btn btn-sm btn-info float-left">Edit</a>
			              	<a class="btn btn-danger btn-sm delete-cluster" data-cluster_id="{{ $cluster->id }}" href="javascript:void(0)">
                              <i class="fas fa-trash">
                              </i>
                              Delete
                            </a>
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
    <script src="{{asset('assets/libs/pdfmake/pdfmake.min.js')}}"></script>

    <!-- Page js-->
    <script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>

    <script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
    <script type="text/javascript">
    	$(function () {
    		 $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
    		$(".delete-cluster").click(function(e){
			          e.preventDefault();
			          var cluster_id = $(this).attr('data-cluster_id');
			          Swal.fire({
			            title: 'Are you sure?',
			            text: "You won't be able to revert this!",
			            showCancelButton: true,
			            confirmButtonColor: '#3085d6',
			            cancelButtonColor: '#d33',
			            confirmButtonText: 'Yes, delete it!'
			          }).then((result) => {
			            if (result.value) {
			                $.ajax({
			                   type:'DELETE',
			                   url:base_url+'/admin/master/lifestyle/'+cluster_id,
			                   data:{id:cluster_id},
			                   success:function(data){
			                      Swal.fire(
			                        'Deleted!',
			                        'Master Preference has been deleted.',
			                        'success'
			                      ).then((result)=>{
			                        window.location.reload();
			                      });
			                   }
			                });
			              }
			          });
			    
			    });
    	});
    </script>
@endsection