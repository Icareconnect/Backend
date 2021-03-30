@extends('layouts.vertical', ['title' => 'Coupons'])

@section('css')
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />

@endsection

@section('content')
		<section class="content-header">
	      <div class="container-fluid">
	        <div class="row mb-2">
	          <div class="col-sm-6">
	            <ol class="breadcrumb float-sm-left">
	              <li class="breadcrumb-item"><a href="{{ url('admin/dahboard') }}">Home</a></li>
	              <li class="breadcrumb-item active">Coupon</li>
	            </ol>
	          </div>
	        </div>
	      </div><!-- /.container-fluid -->
	    </section>
		<div class="row">
		    <div class="col-12">
		      <div class="card">
		        <div class="card-header">
		          <h3 class="card-title">Coupons Listing</h3>
		           <a href="{{ url('admin/coupon/create')}}" class="btn btn-sm btn-info float-right">Add New Coupons</a>
		        </div>
		        <!-- /.card-header -->
		        <div class="card-body">
		          	<table id="scroll-horizontal-datatable" class="table w-100 nowrap">
		            <thead>
		            <tr >
		            	<th>Sr No.</th>
		            	<th>Coupon Code</th>
		            	<th>StartDate & EndDate</th>
		            	<th>Max Redeem Limit</th>
		            	<th>Min Redeem Value</th>
		            	<th>Max Redeem Discount</th>
		            	<th>Category</th>
		            	<th>Service Type</th>
		            	<th>Discount Unit</th>
		            	<th>Discount Value</th>
		            	<th>Action</th>
		            </tr>
		            </thead>
		            <tbody>
		             @foreach($coupons as $index => $coupon)
			            <tr>
			              <td>{{ $index+1 }}</td>
			              <td>{{ $coupon->coupon_code }}</td>
			              <td>{{ $coupon->start_date }} : {{ $coupon->end_date }}</td>
			              <td>{{ $coupon->limit }}</td>
			              <td>{{ $coupon->minimum_value }}</td>
			              <td>{{ $coupon->maximum_discount_amount }}</td>
			              <td>{{ $coupon->discount_type }}</td>
			              <td>{{ $coupon->discount_value }}</td>
			              <td>{{ ($coupon->category)?$coupon->category->name:'NA' }}</td>
			              <td>{{ ($coupon->service)?$coupon->service->type:'NA' }}</td>
			              <td><a href="{{ url('admin/coupon') .'/'.$coupon->id.'/edit'}}" class="btn btn-sm btn-info float-left">Edit</a>
			              	<a class="btn btn-danger btn-sm delete-coupon" data-coupon_id="{{ $coupon->id }}" href="javascript:void(0)">
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
    		$(".delete-coupon").click(function(e){
			          e.preventDefault();
			          var cluster_id = $(this).attr('data-coupon_id');
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
			                   url:base_url+'/admin/coupon/'+cluster_id,
			                   data:{id:cluster_id},
			                   success:function(data){
			                      Swal.fire(
			                        'Deleted!',
			                        'Coupon has been deleted.',
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