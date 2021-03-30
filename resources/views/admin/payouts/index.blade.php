@extends('layouts.vertical', ['title' => 'Service Types'])
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
                            <li class="breadcrumb-item active">Payouts</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Payouts</h4>
                </div>
            </div>
        </div> 

	<div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
                            	<thead>
					            	<tr >
						            	<th>Sr No.</th>
						            	<th>{{ __('text.Vendor') }} Name</th>
						            	<th>Status</th>
						            	<th>Amount</th>
						            	<th>Transaction ID</th>
						            	<th>Comment</th>
                                        <th>View Detail</th>
                                        <th>Action</th>
					            	</tr>
					            </thead>
					            <tbody>
					             @foreach($payoutrequests as $index => $payoutrequest)
						            <tr>
						              <td>{{ $index+1 }}</td>
						              <td>{{ $payoutrequest->cus_info->name }}</td>
						              <td>{{ $payoutrequest->status }}</td>
						              <td>{{ $payoutrequest->transaction->amount }}</td>
						              <td>{{ $payoutrequest->transaction_id }}</td>
                                      <td>{{ isset($payoutrequest->transaction)||$payoutrequest->transaction->payout_message?$payoutrequest->transaction->payout_message:'NA' }}</td>
                                      <td><a href="{{ url('admin/payouts') .'/'.$payoutrequest->id.'/view'}}" class="btn btn-sm btn-info float-left">View Detail</a></td>
						              <td>
                                        @if($payoutrequest->status=='paid' || $payoutrequest->status=='reject')
                                            <a href="javascript:void(0)" class="btn btn-sm btn-info float-left">{{ ucfirst($payoutrequest->status) }}</a>
                                        @elseif($payoutrequest->status=='pending')
                                            <a  href="{{ url('admin/payouts') .'/'.$payoutrequest->id.'/edit'}}" class="btn btn-sm btn-info float-left mark_paid" data-payout_id="{{ $payoutrequest->id }}">Mark Paid</a>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-danger float-left mark_reject" data-payout_id="{{ $payoutrequest->id }}">Mark Reject</a>
                                        @endif
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
            $(".mark_paid").click(function(e){
                      e.preventDefault();
                      var payout_id = $(this).attr('data-payout_id');
                      Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Change Status!'
                      }).then((result) => {
                        if (result.value) {
                            $.ajax({
                               type:'POST',
                               url:base_url+'/admin/payouts/paid/'+payout_id,
                               data:{payout_id:payout_id},
                               success:function(data){
                                  Swal.fire(
                                    'Paid!',
                                    'Status Updated.',
                                    'success'
                                  ).then((result)=>{
                                    window.location.reload();
                                  });
                               }
                            });
                          }
                      });
                
                });

                $('#scroll-horizontal-datatable').on('click', '.mark_reject', function(e){
                      var __this = $(this);
                      e.preventDefault();
                      var payout_id = $(this).attr('data-payout_id');
                      Swal.fire({
                        title: 'Write reason for reject transaction:',
                        input: 'text',
                        inputAttributes: {
                          autocapitalize: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Reject',
                        showLoaderOnConfirm: true,
                        preConfirm: (data) => {
                            if(!data)
                              Swal.showValidationMessage(
                                'Write reason for reject transaction:'
                              )
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                      }).then((result) => {
                            if (result.value) {
                                $.ajax({
                                   type:'POST',
                                   url:base_url+'/admin/payouts/reject/'+payout_id,
                                   data:{payout_id:payout_id,'comment':result.value},
                                   success:function(data){
                                      Swal.fire(
                                        'Rejected!',
                                        'Transaction has been rejected',
                                        'success'
                                      ).then((result)=>{
                                        window.location.reload();
                                      });
                                   }
                                });
                          }
                      });
                     // }
              });
        });
    </script>
@endsection