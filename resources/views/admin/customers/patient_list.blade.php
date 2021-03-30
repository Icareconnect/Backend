@extends('layouts.vertical', ['title' => __('text.Users')])
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
	                            <li class="breadcrumb-item active">{{ __('text.Users') }}</li>
	                        </ol>
	                    </div>
	                </div>
            </div>
        </div>
		<div class="row">
		    <div class="col-12">
		      <div class="card">
		        <div class="card-header">
		          	<h3 class="card-title">{{ __('text.Users') }}</h3>
		          	<a href="{{ url('admin/patient/create')}}" class="btn btn-sm btn-info float-right">Add New {{ __('text.User') }}</a>
		        </div>
		        <!-- /.card-header -->
		        <div class="card-body">
		          	<table id="scroll-horizontal-datatable" class="table w-100 nowrap">
		            <thead>
		            <tr >
		            	<th>Sr No.</th>
		            	<th>Action</th>
		            	<th>{{ __('text.User') }} Name</th>
		            	<th>Phone</th>
		            	<th>Email</th>
		            	<th>DOB</th>
		            	<th>Total Request</th>
		            	<th>Source</th>
		            </tr>
		            </thead>
		            <tbody>
		             @foreach($customers as $index => $customer)
		             	<?php $requests = $customer->getReqAnaliticsByCustomer($customer->id); ?>
			            <tr>
			              <td>{{ $index+1 }}</td>
			              <td>
			              	<ul style="padding: initial;">
			              		<li style="display:inline-block;"><a href="{{ url('admin/patient') .'/'.$customer->id.'/edit'}}" class="btn btn-sm btn-info"><i class="fas fa-edit" style="cursor: pointer;"></i></a></li>
			              		<li style="display:inline-block;"><button data-user_id="{{ $customer->id }}" class="btn btn-sm btn-danger deleteCustomer"><i class="fe-trash"></i></button>
			              		</li>
			              		<li style="display:inline-block;"><button data-user_id="{{ $customer->id }}" data-user_name="{{ $customer->name }}" class="btn btn-sm btn-success openPasswordModal"><i class="fas fa-key"></i></button>
			              		</li>
					              	<a href="{{ url('admin/customers') .'/'.$customer->id}}" class="btn btn-sm btn-info "><i class="fe-eye"></i></a>
			              	</ul>
			              </td>
			              <td>{{ $customer->name }}</td>
			              <td>{{ $customer->phone }}</td>
			              <td>{{ $customer->email }}</td>
			              <td>{{ $customer->profile->dob }}</td>
			              <td>{{ $requests->totalRequest }}</td>
			              <td>{{ $customer->source }}</td>
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

	<div id="pwdModal" data-user_id="" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	  <div class="modal-dialog">
	  <div class="modal-content">
	      <div class="modal-header" style="display:inline;">
	          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	          <h3>Reset Password?</h3>
	      </div>
	      <div class="modal-body">
	          <div class="col-md-12">
	                <div class="panel panel-default">
	                    <div class="panel-body">
	                        <div>
	                          <p>If User have forgotten password you can reset it here.</p>
	                          <p>Name:<b id="m_userName"></b></p>
	                            <div class="panel-body">
	                                <fieldset>
	                                    <div class="form-group">
  											<input type="password" name="pwd" id="input-pwd" class="form-control validate" required>
  											<label data-error="wrong" data-success="right" for="input-pwd">Password</label>
  											<span toggle="#input-pwd" class="fa fa-fw fa-eye field-icon toggle-password"></span>
  											<br>
  											<span class="alert-danger" id="password_error"></span>
	                                    </div>
	                                    <input class="btn btn-lg btn-primary btn-block" id="resetPassword" value="Reset" type="submit">
	                                </fieldset>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	      </div>
	      <div class="modal-footer">
	          <div class="col-md-12">
	          <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
			  </div>	
	      </div>
	  </div>
	  </div>
	</div>
</div>
@endsection

@section('script')
    <!-- Plugins js-->
    <script src="{{asset('assets/libs/datatables/datatables.min.js')}}"></script>
    <!-- <script src="{{asset('assets/libs/pdfmake/pdfmake.min.js')}}"></script> -->

    <!-- Page js-->
    <!-- <script src="{{asset('assets/js/pages/datatables.init.js')}}"></script> -->

    <script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
    <script type="text/javascript">
    	$(document).ready(function() {
			 $('.toggle-password').on('click', function() {
			  $(this).toggleClass('fa-eye fa-eye-slash');
			  let input = $($(this).attr('toggle'));
			  if (input.attr('type') == 'password') {
			    input.attr('type', 'text');
			  }
			  else {
			    input.attr('type', 'password');
			  }
			});

			var dataTable = $('#scroll-horizontal-datatable').DataTable({
	         	"autoFill": false,
	            "scrollX": true,
	            "language": {
	                "paginate": {
	                    "previous": "<i class='mdi mdi-chevron-left'>",
	                    "next": "<i class='mdi mdi-chevron-right'>"
	                }
	            },
	            "drawCallback": function () {
	                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
	            },
	            initComplete: function() {
	                $(this.api().table().container()).find('input[type=search]').parent().wrap('<form>').parent().attr('autocomplete', 'off');
	            }
	        });
    	});
    	$(function () {
    		 $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

    		 $('#scroll-horizontal-datatable').on('click', '.openPasswordModal', function(e){
			e.preventDefault();
			$("#pwdModal").attr('data-user_id','');
	        var _this = $(this);
	        var user_id = $(this).attr('data-user_id');
	        var name = $(this).attr('data-user_name');
			e.preventDefault();
			$("#pwdModal").modal('toggle');
			$("#pwdModal").attr('data-user_id',user_id);
			$("#m_userName").text(name);
		});

		$('#resetPassword').on('click', function(e){
				e.preventDefault();
		        let user_id = $("#pwdModal").attr('data-user_id');
		        let password = $("#input-pwd").val();
		        if(!password){
		        	$("#password_error").text("Please fill the password");
		        	return false;
		        }
		        if(password.length<5){
		        	$("#password_error").text("password should be minimum 5 character");
		        	return false;	
		        }
		        _this = $(this);
		        _this.val('Please wait...');
		        $.ajax({
	               type:'PUT',
	               url:base_url+'/admin/consultants/'+user_id,
	               data:{id:user_id,account_password_ajax:'true',password:password},
	               success:function(data){
	               	$("#input-pwd").val('');
	                  Swal.fire(
	                    'Reset Successful',
	                    'Password has been Reset.',
	                    'success'
	                  ).then((result)=>{
	                  	_this.val('Reset');
	                  	$("#pwdModal").modal('toggle');
						$("#pwdModal").attr('data-user_id',user_id);
	                  });
	               },error:function(data){
	               		_this.val('Reset');
	               		Swal.fire(
	                    'Reset!',
	                    'Something went wrong',
	                    'error'
	                  );
	               }
	            });

			});

    		$('#scroll-horizontal-datatable').on('click', '.deleteCustomer', function(e){
		          e.preventDefault();
		          var _this = $(this);
		          var user_id = $(this).attr('data-user_id');
		          Swal.fire({
		            title: 'Do You Want To Delete This Patient ?',
		            text: "You won't be able to revert this!",
		            showCancelButton: true,
		            confirmButtonColor: '#3085d6',
		            cancelButtonColor: '#d33',
		            confirmButtonText: 'Yes, delete it!'
		          }).then((result) => {
		            if (result.value) {
		                $.ajax({
		                   type:'POST',
		                   url:base_url+'/admin/customers/delete-patient',
		                   data:{"user_id":user_id},
		                   success:function(data){
		          			 _this.parents('tr').remove();
		                      Swal.fire(
		                        'Deleted!',
		                        'Patient has been deleted.',
		                        'success'
		                      ).then((result)=>{
		                        // window.location.reload();
		                      });
		                   }
		                });
		              }
		          });
		    });

    		$("#scroll-horizontal-datatable").on('click', '.approved_vendor',function(e){
			          // e.preventDefault();
			          var __this = $(this);
    					console.log(__this.attr('data-approved'))
			          var user_id = __this.attr('data-user_id');
			          var approved = __this.attr('data-approved');
			          if(approved=='false'){
				          Swal.fire({
				            title: 'Are you sure?',
				            text: "You want to Approve this account",
				            showCancelButton: true,
				            confirmButtonColor: '#3085d6',
				            cancelButtonColor: '#d33',
				            confirmButtonText: 'Yes, Approved!'
				          }).then((result) => {
				            if (result.value) {
				                $.ajax({
				                   type:'PUT',
				                   url:base_url+'/admin/consultants/'+user_id,
				                   data:{id:user_id,account_verify_ajax:'true'},
				                   success:function(data){
				                      Swal.fire(
				                        'Approved!',
				                        'Account has been Approved.',
				                        'success'
				                      ).then((result)=>{
				                      	__this.attr('data-approved','true');
				                      	__this.text('True');
				                      });
				                   }
				                });
				              }
				          });

			         }
			    });
    	});
    </script>
@endsection