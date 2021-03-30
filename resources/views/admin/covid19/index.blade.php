	@extends('layouts.vertical', ['title' => 'Covid-19'])

	@section('css')
	    <!-- Plugins css -->
	    <link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
	    <link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
	@endsection
	@section('content')
		 <!-- Start Content-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div> 
		<div class="row">
		    <div class="col-12">
		      <div class="card">
		        <div class="card-header">
		          <h5 class="card-title">COVID-19 Listing</h5>
		           <a href="{{ url('admin/covid19/create')}}" class="btn btn-sm btn-info float-right">Add New COVID-19 Type</a>
		        </div>
		        <!-- /.card-header -->
		        <div class="card-body">
		          	<table id="scroll-horizontal-datatable" class="table w-100 nowrap">
		            <thead>
		            <tr >
		            	<th>Sr No.</th>
		            	<th>Type</th>
		            	<th>Title</th>
		            	<th>Show On Home</th>
		            	<th>Enable</th>
		            	<th>Show On Click</th>
		            	<th>Image Web</th>
		            	<th>Image Mobile</th>
		            	<th>Action</th>
		            </tr>
		            </thead>
		            <tbody>
		             @foreach($covid19s as $index => $covid19)
			            <tr>
			              <td>{{ $index+1 }}</td>
			              <td>{{ $covid19->type }}</td>
			              <td>{{ $covid19->title }}</td>
			              <td>{{ ($covid19->home_screen)?'Yes':'No' }}</td>
			              <td>{{ ($covid19->enable)?'Yes':'No' }}</td>
			              <td>{{ ($covid19->on_click_info)?$covid19->on_click_info:'Detail View' }}</td>
			              <td>@if($covid19->image_web) <img height="50px" width="50px" src="{{ Storage::disk('spaces')->url('thumbs/'.$covid19->image_web) }}"> @endif</td>
			              <td>@if($covid19->image_mobile) <img height="50px" width="50px" src="{{ Storage::disk('spaces')->url('thumbs/'.$covid19->image_mobile) }}"> @endif</td>
			              <td>
			              	<a href="{{ url('admin/covid19') .'/'.$covid19->id.'/edit'}}" class="btn btn-sm btn-info float-left">Edit</a>

			              	<!-- <a class="btn btn-danger btn-sm delete-covid19" data-banner_id="{{ $covid19->id }}" href="javascript:void(0)">
                              <i class="fas fa-trash">
                              </i>
                              Delete
                            </a> -->
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

    <script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
    <script type="text/javascript">
    	$(function () {
    		 $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
    		$(".delete-banner").click(function(e){
			          e.preventDefault();
			          var banner_id = $(this).attr('data-banner_id');
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
			                   url:base_url+'/admin/banner/'+banner_id,
			                   data:{id:banner_id},
			                   success:function(data){
			                      Swal.fire(
			                        'Deleted!',
			                        'Banner has been deleted.',
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