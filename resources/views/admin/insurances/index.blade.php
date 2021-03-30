@extends('layouts.vertical', ['title' => 'Insurances'])
@section('css')
<link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Insurances</li>
                    </ol>
                </div>
            </div>
        </div>
    </div> 
	<div class="row">
	    <div class="col-12">
		    <div class="card">
		        <div class="card-header">
		          <h3 class="card-title">Insurance Listing</h3>
		           <a href="{{ url('admin/insurance/create')}}" class="btn btn-sm btn-info float-right">Add New Insurance</a>
		           @if(config('client_connected') && (Config::get("client_data")->domain_name=="mp2r" || Config::get("client_data")->domain_name=="food"))
		           	<label for="exfileampleFormControlFile1">Bulk Upload Insurance</label>
			    	<input type="file" name="file" class="form-control-file" id="bulk_upload_insurance" accept=".xlsx">
			    	<br>
			    	<button id="bulk_upload_insurance_btn"  class="btn btn-sm btn-success float-left">Submit</button>
		       	  @endif
		        </div>
		        <div class="card-body">
		          	<table id="scroll-horizontal-datatable" class="table w-100 nowrap">
		            <thead>
		            <tr >
		            	<th>Sr No.</th>
		            	<th>Name</th>
		            	@if(config('client_connected') && (Config::get("client_data")->domain_name=="mp2r" || Config::get("client_data")->domain_name=="food"))
		            	<th>Carrier Code</th>
		            	@endif
		            	<th>Category</th>
		            	<th>Action</th>
		            </tr>
		            </thead>
		            <tbody>
		             @foreach($insurances as $index => $insurance)
			            <tr>
			              <td>{{ $index+1 }}</td>
			              <td>{{ $insurance->name }}</td>
			              @if(config('client_connected') && (Config::get("client_data")->domain_name=="mp2r" || Config::get("client_data")->domain_name=="food"))
			              <td>{{ $insurance->carrier_code }}</td>
			              @endif
			              <td>{{ ($insurance->category_id)?$insurance->category->name:'NA' }}</td>
			              <td><a href="{{ url('admin/insurance') .'/'.$insurance->id.'/edit'}}" class="btn btn-sm btn-info float-left">Edit</a>
			              </td>
			            </tr>
			         @endforeach   
		        	</tbody>
		          </table>
				</div>
			</div>
		</div>
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
        var file_data = null;
		$("#bulk_upload_insurance").change(function(){
        	readURL(this);
        });
        $("#bulk_upload_insurance_btn").on('click',function(){
        	var _this = $(this);
        	if(!file_data){
        		Swal.fire(
	                'Error',
	                'Please choose file',
	                'error'
	              ).then((result)=>{

	              });
        	}else{
        		_this.html('processing...');
	        	$.ajax({
	               data:file_data,
	               type:'POST',
	               cache: false,
	               processData: false,
	  			   contentType: false,
	               url:base_url+'/admin/insurance/uploadxls',
	               success:function(data){
	               	  if(data.status=='success'){
	               	  	_this.html('Done...');
		                  Swal.fire(
		                    'Success',
		                    'Xls has been updated successfully..',
		                    'success'
		                  ).then((result)=>{
		                  	 _this.html('Submit');
		                  	location.reload();
		                  });
	               	  }else if(data.status=='error'){
	               	  	_this.html('Submit');
	               	  	Swal.fire(
		                    'Error!',
		                    data.message,
		                    'error'
		                  ).then((result)=>{
		                  	 _this.html('Submit');
		                  });
	               	  }
	               }
	            });
        	}
        });
        function readURL(input) {
            file_data = new FormData();
			file_data.append('fileName', $('#bulk_upload_insurance')[0].files[0]);
        }
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
		                   url:base_url+'/admin/cluster/'+cluster_id,
		                   data:{id:cluster_id},
		                   success:function(data){
		                      Swal.fire(
		                        'Deleted!',
		                        'Cluster has been deleted.',
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