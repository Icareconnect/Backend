<?php
 $tx_dash = 'Users';
if(config('client_connected') && (Config::get("client_data")->domain_name=="food" || Config::get("client_data")->domain_name=="healtcaremydoctor"))
        $tx_dash = 'Patients';
?>
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
		        </div>
		        <!-- /.card-header -->
		        <div class="card-body">
		        	<table>
				     <tr>
				       <td>
				         <button class="btn form-control btn-sm btn-success float-left" type="button" id="delete_user">Delete!</button>
				       </td>
				     </tr>
				   </table>
		          	<table id="scroll-horizontal-datatable" class="table w-100 nowrap">
		            <thead>
		            <tr >
		            	<th><input type="checkbox" id="selectAllchkBox"></th>
		            	<th>Sr No.</th>
		            	<th>Action</th>
		            	<th>Name</th>
		            	<th>Email</th>
		            	<th>Phone</th>
		            	@if(Config::get('client_connected') && (Config::get("client_data")->domain_name=="nurselynx"))
		            	<th>Points</th>
		            	@endif
		            	<th>Total Request</th>
		            	<th>Approved</th>
		            </tr>
		            </thead>
		            <tbody>
		             @foreach($customers as $index => $customer)
		             	<?php $requests = $customer->getReqAnaliticsByCustomer($customer->id); ?>
			            <tr>
			              <td><input type="checkbox" data-user="{{ $customer->id }}"></td>
			              <td>{{ $index+1 }}</td>
			              <td>
			              	<ul style="padding: initial;">
			              		<li style="display:inline-block;"><a href="{{ url('admin/customers') .'/'.$customer->id.'/edit'}}" class="btn btn-sm btn-info"><i class="fas fa-edit" style="cursor: pointer;"></i></a></li>
			              		<li style="display:inline-block;"><button data-user_id="{{ $customer->id }}" class="btn btn-sm btn-danger deleteCustomer"><i class="fe-trash"></i></button>
			              		</li>
			              		<li style="display:inline-block;"><button data-user_id="{{ $customer->id }}" data-user_name="{{ $customer->name }}" class="btn btn-sm btn-success openPasswordModal"><i class="fas fa-key"></i></button>
			              		</li>
			              		@if(Config::get('client_connected') && (Config::get("client_data")->domain_name=="nurselynx"))
			              		<li data-toggle="tooltip" title="Points Update"  style="display:inline-block;"><button id="uni_user_{{ $customer->id }}" data-user_id="{{ $customer->id }}" data-points="{{ $customer->wallet->points }}" data-user_name="{{ $customer->name }}" class="btn btn-sm btn-success openPointsModal"><i class="fas fa-star"></i></button>
			              		</li>
			              		@endif
			              		@if(Config::get('client_connected') && (Config::get("client_data")->domain_name=="food" || Config::get("client_data")->domain_name=="healtcaremydoctor" || Config::get("client_data")->domain_name=="intely") )
					              	<a href="{{ url('admin/customers') .'/'.$customer->id}}" class="btn btn-sm btn-info "><i class="fe-eye"></i></a>
					             @endif
			              	</ul>
			              </td>
			              <td>{{ $customer->name }}</td>
			              <td>{{ $customer->email }}</td>
			              <td>{{ $customer->phone }}</td>
			              @if(Config::get('client_connected') && (Config::get("client_data")->domain_name=="nurselynx"))
			              	<td id="uni_points_{{ $customer->id }}">{{ $customer->wallet->points }}</td>
			              @endif
			              <td>{{ $requests->totalRequest }}</td>
			              <td><a href="javascript:void(0)" class="btn btn-sm btn-info float-left approved_vendor" data-approved="{{ ($customer->account_verified)?'true':'false' }}" data-user_id="{{ $customer->id }}">{{ ($customer->account_verified)?'True':'False' }}</a></td>
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

	<div id="pointsUpdate" data-user_id="" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	  <div class="modal-dialog">
	  <div class="modal-content">
	      <div class="modal-header" style="display:inline;">
	          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	          <h3>Points Update</h3>
	      </div>
	      <div class="modal-body">
	          <div class="col-md-12">
	                <div class="panel panel-default">
	                    <div class="panel-body">
	                        <div>
	                            <div class="panel-body">
	                                <fieldset>
	                                    <div class="form-group">
  											<label for="points">Ponits</label>
  											<input type="number" name="points" id="points" class="form-control validate" required>
  											<span class="alert-danger" id="password_error"></span>
	                                    </div>
	                                    <input class="btn btn-lg btn-primary btn-block" id="pointsUpdateForm" value="Update" type="submit">
	                                </fieldset>
	                            </div>
	                        </div>
	                    </div>
	                </div>
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
    		$("#selectAllchkBox").change(function(){
	    	  $("input:checkbox").prop( 'checked',$(this).is(":checked") );
	        });
    		var doctor_text = "{{ __('text.User') }}";
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

    	$('#scroll-horizontal-datatable').on('click', '.openPointsModal', function(e){
			e.preventDefault();
			$("#pointsUpdate").attr('data-user_id','');
			$("#pointsUpdate").attr('data-points','');
	        let _this = $(this);
	        let user_id = $(this).attr('data-user_id');
	        let points = $(this).attr('data-points');
	        let name = $(this).attr('data-user_name');
			e.preventDefault();
			$("#pointsUpdate").modal('toggle');
			$("#pointsUpdate").attr('data-user_id',user_id);
			$("#pointsUpdate").attr('data-points',points);
			$("#points").val(points);
			$("#m_userName").text(name);
		});

    	$("#delete_user").click(function(e){
	          e.preventDefault();
	          var _this = $(this);
	          var user_ids = [];
	          $(' input[type="checkbox"]').each(function() {
			        if ($(this).is(":checked")) {
			        	if($(this).data('user')){
				          user_ids.push($(this).data('user'));
			        	}
			        }
			  });
			  if(user_ids.length > 0){
		          Swal.fire({
		            title: 'Do You Want To Delete This '+doctor_text+' ?',
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
		                   data:{"user_id":user_ids},
		                   success:function(data){
		                      Swal.fire(
		                        'Deleted!',
		                        doctor_text+' has been deleted.',
		                        'success'
		                      ).then((result)=>{
		                        window.location.reload();
		                      });
		                   }
		                });
		              }
		          });
			  }else{
			  	alert("Please select atleast one "+doctor_text);
			  	return false;
			  }
	    });


    	$('#pointsUpdateForm').on('click', function(e){
			e.preventDefault();
	        let user_id = $("#pointsUpdate").attr('data-user_id');
	        let points = $("#points").val();
	        _this = $(this);
	        _this.val('Please wait...');
	        $.ajax({
               type:'PUT',
               url:base_url+'/admin/consultants/'+user_id,
               data:{id:user_id,account_points_ajax:'true',points:points},
               success:function(data){
                  Swal.fire(
                    'Points!',
                    'Points Updated.',
                    'success'
                  ).then((result)=>{
                  	_this.val('Update');
                  	$("#uni_user_"+user_id).attr('data-points',points);
                  	$("#uni_points_"+user_id).html(points);
                  	$("#pointsUpdate").modal('toggle');
                  });
               },error:function(data){
               		_this.val('Update');
               		Swal.fire(
                    'Points!',
                    'Something went wrong',
                    'error'
                  );
               }
            });

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