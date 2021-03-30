<?php
 $tx_dash = 'Vendors';
if(config('client_connected') && Config::get("client_data")->domain_name=="intely")
        $tx_dash = 'Nurses';
if(config('client_connected') && (Config::get("client_data")->domain_name=="food" || Config::get("client_data")->domain_name=="healtcaremydoctor"))
        $tx_dash = 'Doctors';
?>
@extends('layouts.vertical', ['title' => __('text.Vendors') ])
@section('css')
<link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<style type="text/css">
	.offline_online{
		margin: 0 !important;
    	padding: 0!important;
    },
</style>
<div class="container-fluid">
		<div class="row">
		    <div class="col-12">
		      <div class="card">
		        <div class="card-header">
		          <h3 class="card-title">{{ __('text.Vendors') }}</h3>
		          <a href="{{ url('admin/consultants/create')}}" class="btn btn-sm btn-info float-right">Add New</a>
		          	@if(config('client_connected') && (Config::get("client_data")->domain_name=="mp2r" || Config::get("client_data")->domain_name=="food"))
		           	<!-- Bulk Upload {{ __('text.Vendors') }} -->
			    	<input type="file" name="file" class="form-control-file float-left" id="exampleFormControlFile1" accept=".xlsx" style="display: inline;width: 20%;">
			    	<button id="submitExcel"  class="btn btn-sm btn-success float-left">Submit</button>
		       		@endif
		        </div>
		        <!-- /.card-header -->
		        <div class="card-body">
		        	<table>
				     <tr>
				       <td>
				         <button class="btn form-control btn-sm btn-success float-left" type="button" id="delete_user">Delete!</button>
				       </td>
				       @if(config('client_connected') && (Config::get("client_data")->domain_name=="intely"))
				       <td>
				         <button class="btn form-control btn-sm btn-success float-left" type="button" id="sendMsgToPrem">Send Message to Premium!</button>
				       </td>
				       @endif
				       <td>
				       	<div class="row">
				         	<div class="col-md-4">
					         <select class="form-control" id='searchDate'>
					           <option value=''>-- Select Date--</option>
					           <?php for($x = 1; $x <= 31; $x++) {
								  $value = str_pad($x,2,"0",STR_PAD_LEFT); ?>
								  <option value="<?php echo $value ?>"><?php echo $value ?></option>;
								<?php } ?>
					         </select>
				     		</div>
				     		<div class="col-md-4">
				     			<select class="form-control" id='searchMonth'>
					           <option value=''>-- Select Month--</option>
					           @php
								    $months = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
								@endphp
					           @foreach($months as $k=>$v)
								  <option value="{{ $v }}">{{ $v }}</option>
								@endforeach
					         </select>
				     		</div>
				     		<div class="col-md-4">
				     			<select class="form-control" id='searchYear'>
					           <option value=''>-- Select Year--</option>
					           @for($i = date('Y'); $i >= (date('Y') - 1); $i--)
                                <option value="{{ $i }}">{{ $i }}</option>            
                                @endfor
					         </select>
				     		</div>
				     	</div>
				       	 <!-- <div class="form-group">
					         <select class="form-control" id='searchByPlan'>
					           <option value=''>-- Select Plan--</option>
					           <option value='Basic'>Basic</option>
					           <option value='Executive'>Executive</option>
					           <option value='Premium'>Premium</option>
					         </select>
					         <select class="form-control" id='searchByPlan'>
					           <option value=''>-- Select Plan--</option>
					           <option value='Basic'>Basic</option>
					           <option value='Executive'>Executive</option>
					           <option value='Premium'>Premium</option>
					         </select>
				     	</div> -->
				       </td>
				       @if(config('client_connected') && (Config::get("client_data")->domain_name=="mp2r" || Config::get("client_data")->domain_name=="food"))
				       <td>
				         <select class="form-control" id='searchByPlan'>
				           <option value=''>-- Select Plan--</option>
				           <option value='Basic'>Basic</option>
				           <option value='Executive'>Executive</option>
				           <option value='Premium'>Premium</option>
				         </select>
				       </td>
				       @endif
				     </tr>
				   </table>
		          	<table id="scroll-horizontal-datatable" class="table w-100 nowrap" >
		            <thead>
		            <tr >
    	                <th><input type="checkbox" id="selectAllchkBox"></th>
		            	<th>Sr No.</th>
		            	<th>Actions</th>
		            	@if(config('client_connected') && (Config::get("client_data")->domain_name=="mp2r" || Config::get("client_data")->domain_name=="food"))
		            		<th>Online/Offline</th>
		            		<th>Insurances</th>
		            		<th>Plans</th>
		            	@endif
		            	@if(config('client_connected') && (Config::get("client_data")->domain_name=="intely"))
		            		<th>Premium</th>
		            	@endif
		            	<th>Name</th>
		            	<th>Email</th>
		            	@if(Config::get('client_connected') && (Config::get("client_data")->domain_name=="nurselynx"))
		            	<th>Points</th>
		            	@endif
		            	<th>Cat. Name</th>
		            	@if(config('client_connected') && (Config::get("client_data")->domain_name=="physiotherapist"))
		            		<th>Patients</th>
		            		<th>Requests</th>
		            	@endif
		            	<th>Approved</th>
	            		<th style="display: none;">Date</th>
	            		<th style="display: none;">Month</th>
	            		<th style="display: none;">Year</th>
		            </tr>
		            </thead>
		            <tbody>
		             @foreach($consultants as $index => $consultant)
			            <tr class="delete_row">
    	                  <td><input type="checkbox" data-user="{{ $consultant->id }}"></td>
			              <td>{{ $index+1 }}</td>
			              <td>
			              	<ul style="padding: initial;">
			              		<li data-toggle="tooltip" title="Edit" style="display:inline-block;"><a href="{{ url('admin/consultants') .'/'.$consultant->id.'/edit'}}" class="btn btn-sm btn-info"><i class="fas fa-edit" style="cursor: pointer;"></i></a></li>
			              		<li data-toggle="tooltip" title="Delete" style="display:inline-block;"><button data-user_id="{{ $consultant->id }}" class="btn btn-sm btn-danger deleteConsultant"><i class="fe-trash"></i></button>
			              		</li>
			              		<li data-toggle="tooltip" title="Password Update" style="display:inline-block;"><button data-user_id="{{ $consultant->id }}" data-user_name="{{ $consultant->name }}" class="btn btn-sm btn-success openPasswordModal"><i class="fas fa-key"></i></button>
			              		</li>
			              		
			              		@if(Config::get('client_connected') && (Config::get("client_data")->domain_name=="nurselynx"))
			              		<li data-toggle="tooltip" title="Points Update"  style="display:inline-block;"><button id="uni_user_{{ $consultant->id }}" data-user_id="{{ $consultant->id }}" data-points="{{ $consultant->wallet->points }}" data-user_name="{{ $consultant->name }}" class="btn btn-sm btn-success openPointsModal"><i class="fas fa-star"></i></button>
			              		</li>
			              		@endif
			              		
				              	<a data-toggle="tooltip" title="View" href="{{ url('admin/consultants') .'/'.$consultant->id}}" class="btn btn-sm btn-info "><i class="fe-eye"></i></a>
			              	</ul>
			              </td>
			              @if(config('client_connected') && (Config::get("client_data")->domain_name=="mp2r" || Config::get("client_data")->domain_name=="food"))
			              <td><button id="manual_available_{{ $consultant->id }}" data-user_id="{{ $consultant->id }}"  class="{{ (!$consultant->manual_available)?'btn btn-danger waves-effect waves-light manual-online':'btn btn-success  waves-effect waves-light float-left manual-online' }} " data-manual_available="{{ $consultant->manual_available }}">
			              		<span id="upper_span_{{ $consultant->id }}" style="{{ (!$consultant->manual_available) ? 'display: inline' : 'display: none' }}" >Offline</span>
			              		<span id="span_manual_available_{{ $consultant->id }}" class="offline_online {{ (!$consultant->manual_available)?'btn-label-right':'btn-label' }}">
			              			<i id="i_manual_available_{{ $consultant->id }}" class="{{ (!$consultant->manual_available)?'mdi mdi-close-circle-outline':'mdi mdi-check-all' }} "></i>
			              		</span>
			              		<span id="lower_span_{{ $consultant->id }}" style="{{ (!$consultant->manual_available) ? 'display: none' : 'display: inline' }}">Online</span>
			              	</button></td>
			              	<td>{{$consultant->insurance_names}}</td>
			              	<td>{{ isset($consultant->plan_names)?implode(",",$consultant->plan_names):'Basic'}}</td>
			              @endif
			              @if(config('client_connected') && (Config::get("client_data")->domain_name=="intely"))
			              	<td><button id="pre_available_{{ $consultant->id }}" data-user_id="{{ $consultant->id }}"  class="{{ (!$consultant->premium_enable)?'btn btn-danger waves-effect waves-light pre-online':'btn btn-success  waves-effect waves-light float-left pre-online' }} " data-premium_enable="{{ $consultant->premium_enable }}">
			              		<span id="upper_span_{{ $consultant->id }}" style="{{ (!$consultant->premium_enable) ? 'display: inline' : 'display: none' }}" >Off</span>
			              		<span id="span_pre_available_{{ $consultant->id }}" class="offline_online {{ (!$consultant->premium_enable)?'btn-label-right':'btn-label' }}">
			              			<i id="i_pre_available_{{ $consultant->id }}" class="{{ (!$consultant->premium_enable)?'mdi mdi-close-circle-outline':'mdi mdi-check-all' }} "></i>
			              		</span>
			              		<span id="lower_span_{{ $consultant->id }}" style="{{ (!$consultant->premium_enable) ? 'display: none' : 'display: inline' }}">On</span>
			              	</button></td>
			              @endif
			              <td>{{ $consultant->name }}</td>
			              <td>{{ $consultant->email }}</td>
			              @if(Config::get('client_connected') && (Config::get("client_data")->domain_name=="nurselynx"))
			              	<td id="uni_points_{{ $consultant->id }}">{{ $consultant->wallet->points }}</td>
			              @endif
			              <td>{{ ($consultant->getCategoryData($consultant->id) && $consultant->getCategoryData($consultant->id)->id?($consultant->filter)?$consultant->filters_name:$consultant->getCategoryData($consultant->id)->name:'NA') }}</td>
			              @if(config('client_connected') && (Config::get("client_data")->domain_name=="physiotherapist"))
			              <td>{{ $consultant->patientCount }}</td>
			              <td>{{ $consultant->requestCompleted($consultant->id) }}</td>
			              @endif
			              <td style="display: none;">{{ $consultant->date }}</td>
			              <td style="display: none;">{{ $consultant->month }}</td>
			              <td style="display: none;">{{ $consultant->year }}</td>
			              <td>
			              	<a href="javascript:void(0)" class="btn btn-sm btn-info float-left approved_vendor" data-approved="{{ ($consultant->account_verified)?'true':'false' }}" data-consultant_id="{{ $consultant->id }}">{{ ($consultant->account_verified)?'True':'False' }}</a>
			              </td>
			            </tr>
			         @endforeach   
		        	</tbody>
		          </table>
				</div>
			</div>
		</div>
	</div>
	<!--modal-->
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
<script src="{{asset('assets/libs/datatables/datatables.min.js')}}"></script>
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
	    $('#searchByPlan').change(function(){
	    		dataTable.search( this.value ).draw();
		});
		$('#searchDate').change(function(){
	    		dataTable.search( this.value ).draw();
		});
		$('#searchMonth').change(function(){
	    		dataTable.search( this.value ).draw();
		});
		$('#searchYear').change(function(){
	    		dataTable.search( this.value ).draw();
		});
    });



	$(function () {
		var doctor_text = "{{ __('text.Vendor') }}";
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
		$('#scroll-horizontal-datatable').on('click', '.manual-online', function(e){
	          e.preventDefault();
	          var _this = $(this);
	          var user_id = $(this).attr('data-user_id');
	          var manual_available = $(this).attr('data-manual_available');
	          if(manual_available == 'true'){
	          	var main_title = "Do you want to make Offline this Professional?";
	          	var confirmButtonText = 'Offline';
	          }else if(manual_available == 1){
	          	var main_title = "Do you want to make Offline this Professional?";
	          	var confirmButtonText = 'Offline';
	          }else{
		        var main_title = "Do you want make Online?";
		        var confirmButtonText = 'Online';
	          }
	          Swal.fire({
	            title: main_title,
	            showCancelButton: true,
	            cancelButtonColor: '#d33',
	            confirmButtonColor: '#3085d6',
	            confirmButtonText: confirmButtonText,
	          }).then((result) => {
	            if (result.value) {
	                $.ajax({
	                   type:'POST',
	                   url:base_url+'/admin/consultants/manual-online',
	                   data:{"user_id":user_id, manual_available:manual_available},
	                   success:function(data){
	                      Swal.fire(
	                        'Updated!',
	                        'Record updated successfully.',
	                        'success'
	                      ).then((result)=>{
	                      	if(data.user_data.manual_available == true){
	                      		$('#scroll-horizontal-datatable #manual_available_'+data.user_data.id).attr('data-manual_available',data.user_data.manual_available);
			                    $('#scroll-horizontal-datatable #manual_available_'+data.user_data.id).removeClass('btn btn-danger waves-effect waves-light manual-online').addClass('btn btn-success btn-rounded waves-effect waves-light float-left manual-online');
			                    $('#scroll-horizontal-datatable #span_manual_available_'+data.user_data.id).removeClass('btn-label-right').addClass('btn-label');
			                    $('#scroll-horizontal-datatable #i_manual_available_'+data.user_data.id).removeClass('mdi mdi-close-circle-outline').addClass('mdi mdi-check-all');
			                    $('#scroll-horizontal-datatable #upper_span_'+data.user_data.id).hide();
			                    $('#scroll-horizontal-datatable #lower_span_'+data.user_data.id).show();
	                      	}else{
			                    $('#scroll-horizontal-datatable #manual_available_'+data.user_data.id).attr('data-manual_available',data.user_data.manual_available);
	                      		$('#scroll-horizontal-datatable #manual_available_'+data.user_data.id).removeClass('btn btn-success btn-rounded waves-effect waves-light float-left manual-online').addClass('btn btn-danger waves-effect waves-light manual-online');
			                    $('#scroll-horizontal-datatable #span_manual_available_'+data.user_data.id).removeClass('btn-label').addClass('btn-label-right');
			                    $('#scroll-horizontal-datatable #i_manual_available_'+data.user_data.id).removeClass('mdi mdi-check-all').addClass('mdi mdi-close-circle-outline');
			                    $('#scroll-horizontal-datatable #upper_span_'+data.user_data.id).show();
			                    $('#scroll-horizontal-datatable #lower_span_'+data.user_data.id).hide();
	                      	}
	                      });
	                   }
	                });
	              }
	          });
	    });
	    $('#scroll-horizontal-datatable').on('click', '.pre-online', function(e){
	          e.preventDefault();
	          var _this = $(this);
	          var user_id = $(this).attr('data-user_id');
	          var premium_enable = $(this).attr('data-premium_enable');
	          if(premium_enable){
	          	var main_title = "Do you want to make Offline this Professional?";
	          	var confirmButtonText = 'Offline';
	          }else if(premium_enable == 1){
	          	var main_title = "Do you want to make Offline this Professional?";
	          	var confirmButtonText = 'Offline';
	          }else{
		        var main_title = "Do you want make Online?";
		        var confirmButtonText = 'Online';
	          }
	          Swal.fire({
	            title: main_title,
	            showCancelButton: true,
	            cancelButtonColor: '#d33',
	            confirmButtonColor: '#3085d6',
	            confirmButtonText: confirmButtonText,
	          }).then((result) => {
	            if (result.value) {
	                $.ajax({
	                   type:'POST',
	                   url:base_url+'/admin/consultants/pre-online',
	                   data:{"user_id":user_id, premium_enable:premium_enable},
	                   success:function(data){
	                      Swal.fire(
	                        'Updated!',
	                        'Record updated successfully.',
	                        'success'
	                      ).then((result)=>{
	                      	if(data.user_data.premium_enable){
	                      		$('#scroll-horizontal-datatable #pre_available_'+data.user_data.id).attr('data-premium_enable',data.user_data.premium_enable);
			                    $('#scroll-horizontal-datatable #pre_available_'+data.user_data.id).removeClass('btn btn-danger waves-effect waves-light manual-online').addClass('btn btn-success btn-rounded waves-effect waves-light float-left manual-online');
			                    $('#scroll-horizontal-datatable #span_pre_available_'+data.user_data.id).removeClass('btn-label-right').addClass('btn-label');
			                    $('#scroll-horizontal-datatable #i_pre_available_'+data.user_data.id).removeClass('mdi mdi-close-circle-outline').addClass('mdi mdi-check-all');
			                    $('#scroll-horizontal-datatable #upper_span_'+data.user_data.id).hide();
			                    $('#scroll-horizontal-datatable #lower_span_'+data.user_data.id).show();
	                      	}else{
			                    $('#scroll-horizontal-datatable #pre_available_'+data.user_data.id).attr('data-premium_enable',data.user_data.premium_enable);
	                      		$('#scroll-horizontal-datatable #pre_available_'+data.user_data.id).removeClass('btn btn-success btn-rounded waves-effect waves-light float-left manual-online').addClass('btn btn-danger waves-effect waves-light manual-online');
			                    $('#scroll-horizontal-datatable #span_pre_available_'+data.user_data.id).removeClass('btn-label').addClass('btn-label-right');
			                    $('#scroll-horizontal-datatable #i_pre_available_'+data.user_data.id).removeClass('mdi mdi-check-all').addClass('mdi mdi-close-circle-outline');
			                    $('#scroll-horizontal-datatable #upper_span_'+data.user_data.id).show();
			                    $('#scroll-horizontal-datatable #lower_span_'+data.user_data.id).hide();
	                      	}
	                      });
	                   }
	                });
	              }
	          });
	    });
	    var file_data = null;
		$("#exampleFormControlFile1").change(function(){
        	readURL(this);
        });
        $("#selectAllchkBox").change(function(){
    	  $("input:checkbox").prop( 'checked',$(this).is(":checked") );
        });
        $("#submitExcel").on('click',function(){
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
	               url:base_url+'/admin/consultants/uploadxls',
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
			file_data.append('fileName', $('#exampleFormControlFile1')[0].files[0]);
        }
	    
		$('#scroll-horizontal-datatable').on('click', '.deleteConsultant', function(e){
	          e.preventDefault();
	          var _this = $(this);
	          var user_id = $(this).attr('data-user_id');
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
	                   url:base_url+'/admin/consultants/delete-doctor',
	                   data:{"user_id":user_id},
	                   success:function(data){
	          			 _this.parents('tr').remove();
	                      Swal.fire(
	                        'Deleted!',
	                        doctor_text+' has been deleted.',
	                        'success'
	                      ).then((result)=>{
	                        // window.location.reload();
	                      });
	                   }
	                });
	              }
	          });
	    });



	    $('#sendMsgToPrem').on('click', function(e){
	          var _this = $(this);
	          var text = _this.html();
          // if(approved=='false'){
          Swal.fire({
            title: 'Write some text to send message:',
            input: 'text',
            inputAttributes: {
              autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Send Message',
            showLoaderOnConfirm: true,
            preConfirm: (data) => {
                if(!data){
                  Swal.showValidationMessage(
                    'Write some text to send message:'
                  )
              	}else{
              		_this.html('Sending...');
              	}
            },
            allowOutsideClick: () => !Swal.isLoading()
          }).then((result) => {
          	_this.html(text);
            if (result.value) {
                $.ajax({
                   type:'POST',
                   url:base_url+'/admin/consultants/send_message_to_pre',
                   data:{'comment':result.value},
                   success:function(data){
                      Swal.fire(
                        'Success!',
                        'Message has been Sent.',
                        'success'
                      ).then((result)=>{
                        // location.reload();
                      });
                   }
                });
              }
          });
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
		                   url:base_url+'/admin/consultants/delete-doctor',
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
			  	alert("Please select atleast one Professional.");
			  	return false;
			  }
	    });

		$("#scroll-horizontal-datatable").on('click', '.approved_vendor',function(e){
		          // e.preventDefault();
		          var __this = $(this);
					console.log(__this.attr('data-approved'))
		          var consultant_id = __this.attr('data-consultant_id');
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
			                   url:base_url+'/admin/consultants/'+consultant_id,
			                   data:{id:consultant_id,account_verify_ajax:'true'},
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
	