	@extends('layouts.vertical', ['title' => 'Master Intervals'])

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
                            <li class="breadcrumb-item active">Master Intervals</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div> 
		<div class="row">
		    <div class="col-12">
		      <div class="card">
		        <div class="card-header">
		          <h5 class="card-title">Master Intervals</h5>
		           <a href="{{ url('admin/master_slot/edit')}}" class="btn btn-sm btn-info float-right">Add/Edit Master Intervals</a>
		        </div>
		        <!-- /.card-header -->
		        <div class="card-body">
		          	<table id="scroll-horizontal-datatable" class="table w-100 nowrap">
		            <thead>
		            <tr >
		            	<th>Sr No.</th>
		            	<th>Start Time</th>
		            	<th>End Time</th>
		            </tr>
		            </thead>
		            <tbody>
		             @foreach($masterslots as $index => $slot)
			            <tr>
			              <td>{{ $index+1 }}</td>
			              <td>{{ $slot->start_time }}</td>
			              <td>{{ $slot->end_time }}</td>
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
    		$(".delete-slot").click(function(e){
		          e.preventDefault();
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
		                   url:base_url+'/admin/master_slot/delete',
		                   data:{},
		                   success:function(data){
		                      Swal.fire(
		                        'Deleted!',
		                        'Intervals has been deleted.',
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