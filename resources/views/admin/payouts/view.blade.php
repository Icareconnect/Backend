@extends('layouts.vertical', ['title' => 'Payout View'])

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
                            <li class="breadcrumb-item active">Payout View</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Payout View</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-4 col-xl-4">
                <div class="card-box text-center">
                   <!--  <img src="{{asset('assets/images/users/user-1.jpg')}}" class="rounded-circle avatar-lg img-thumbnail"
                        alt="profile-image"> -->

                    <h4 class="mb-0">{{ $payoutrequest->cus_info->name }}</h4>
                    <p class="text-muted">@vendor</p>

                    <button type="button" class="btn btn-success btn-xs waves-effect mb-2 waves-light">Follow</button>
                    <button type="button" class="btn btn-danger btn-xs waves-effect mb-2 waves-light">Message</button>

                    <div class="text-left mt-3">
                        <h4 class="font-13 text-uppercase">About Me :</h4>
                        <p class="text-muted mb-2 font-13"><strong>Full Name :</strong> <span class="ml-2">{{ $payoutrequest->cus_info->name }}</span></p>

                        <p class="text-muted mb-2 font-13"><strong>Mobile :</strong><span class="ml-2">{{ $payoutrequest->cus_info->country_code }}-{{ $payoutrequest->cus_info->phone }}</span></p>

                        <p class="text-muted mb-2 font-13"><strong>Email :</strong> <span class="ml-2 ">{{ $payoutrequest->cus_info->email }}</span></p>
                        <p><br></p>
                        <p><br></p>
                        <p><br></p>
                    </div>
                </div> <!-- end card-box -->

                

            </div> <!-- end col-->

            <div class="col-lg-8 col-xl-8">
                <div class="card-box">
                
                    <div class="tab-content">
                        
                        <!-- end about me section content -->

                        
                        <!-- end timeline content-->

                        <div class="tab-pane" style="display:block;">
                            <form>
                                <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-circle mr-1"></i> Account Info</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="firstname">Account Holder Name</label>
                                            <input type="text" value="{{ $payoutrequest->account->holder_name }}" class="form-control" id="firstname"  disabled="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="firstname">Request Status</label>
                                            <input type="text" value="{{ ucfirst($payoutrequest->status) }}" class="form-control" id="firstname"  disabled="">
                                        </div>
                                    </div>
                                </div> <!-- end row --> 
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="firstname">Bank Name</label>
                                            <input type="text" value="{{ $payoutrequest->account->bank_name }}" class="form-control" id="firstname"  disabled="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lastname">Account Number</label>
                                            <input type="text" class="form-control" value="{{ $payoutrequest->account->account_number }}" id="lastname"  disabled="">
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row --> 
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="firstname">IFC Code</label>
                                            <input type="text" value="{{ $payoutrequest->account->ifc_code }}" class="form-control" id="firstname"  disabled="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lastname">Account Type</label>
                                            <input type="text" class="form-control" value="{{ $payoutrequest->account->account_type }}" id="lastname"  disabled="">
                                        </div>
                                    </div> <!-- end col -->
                                    
                                </div> <!-- end row -->


                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="firstname">Amount</label>
                                            <input type="text" value="{{ $payoutrequest->transaction->amount }}" class="form-control" id="firstname"  disabled="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row -->
                            </form>
                        </div>
                        <!-- end settings content-->

                    </div> <!-- end tab-content -->
                </div> <!-- end card-box-->

            </div> <!-- end col -->
        </div>
        <!-- end row-->

    </div> <!-- container -->
@endsection