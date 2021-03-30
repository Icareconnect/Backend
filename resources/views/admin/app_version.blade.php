@extends('layouts.vertical', ['title' => 'App Version'])

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
                            <li class="breadcrumb-item active">App Version</li>
                        </ol>
                    </div>
                    <h4 class="page-title">App Version</h4>
                </div>
            </div>
        </div>     
        <!-- end page title --> 


        <div class="row">
            <div class="col-12">
                <div class="card-box">
                    <div class="row">
                        <div class="col-lg-8">
                            
                        </div>
                        <div class="col-lg-4">
                            <div class="text-lg-right mt-3 mt-lg-0">
                                <a href="{{ url('admin/app_version/create') }}" class="btn btn-sm btn-info float-right"><i class="mdi mdi-plus-circle mr-1"></i> Add New Version</a>
                            </div>
                        </div><!-- end col-->
                    </div> <!-- end row -->
                </div> <!-- end card-box -->
            </div><!-- end col-->
        </div>
        <!-- end row -->        

        <div class="row">
            <div class="col-lg-4">
                <div class="card-box bg-pattern">
                    <div class="text-center">
                        <img src="{{asset('assets/images/android.png')}}"alt="logo" class="avatar-xl rounded-circle mb-3">
                        <h4 class="mb-1 font-20">Android, User App</h4>
                        <p class="text-muted  font-14">Version Name: {{ $data['and_user']->version_name }}</p>
                        <p class="text-muted  font-14">Verion {{ $data['and_user']->version }}</p>
                    </div>
                    <div class="row mt-4 text-center">
                        <div class="col-6">
                            <h5 class="font-weight-normal text-muted">Update Type</h5>
                            @if($data['and_user']->update_type==0)
                                <h4>No-Update</h4>
                            @elseif($data['and_user']->update_type==1)
                                <h4>Minor Update</h4>
                            @else
                                <h4>Major Update</h4>
                            @endif
                        </div>
                    </div>
                </div> <!-- end card-box -->
            </div><!-- end col -->


            <div class="col-lg-4">
                <div class="card-box bg-pattern">
                    <div class="text-center">
                        <img src="{{asset('assets/images/android.png')}}"alt="logo" class="avatar-xl rounded-circle mb-3">
                        <h4 class="mb-1 font-20">Android, Doctor App</h4>
                        <p class="text-muted  font-14">Version Name: {{ $data['and_doc']->version_name }}</p>
                        <p class="text-muted  font-14">Verion {{ $data['and_doc']->version }}</p>
                    </div>
                    <div class="row mt-4 text-center">
                        <div class="col-6">
                            <h5 class="font-weight-normal text-muted">Update Type</h5>
                            @if($data['and_doc']->update_type==0)
                                <h4>No-Update</h4>
                            @elseif($data['and_doc']->update_type==1)
                                <h4>Minor Update</h4>
                            @else
                                <h4>Major Update</h4>
                            @endif
                        </div>
                    </div>
                </div> <!-- end card-box -->
            </div><!-- end col -->


            <div class="col-lg-4">
                <div class="card-box bg-pattern">
                    <div class="text-center">
                        <img src="{{asset('assets/images/companies/apple.png')}}"alt="logo" class="avatar-xl rounded-circle mb-3">
                        <h4 class="mb-1 font-20">IOS User APP</h4>
                        <p class="text-muted  font-14">Version Name: {{ $data['ios_user']->version_name }}</p>
                        <p class="text-muted  font-14">Verion {{ $data['ios_user']->version }}</p>
                    </div>
                    <div class="row mt-4 text-center">
                        <div class="col-6">
                            <h5 class="font-weight-normal text-muted">Update Type</h5>
                            @if($data['ios_user']->update_type==0)
                                <h4>No-Update</h4>
                            @elseif($data['ios_user']->update_type==1)
                                <h4>Minor Update</h4>
                            @else
                                <h4>Major Update</h4>
                            @endif
                        </div>
                    </div>
                </div> <!-- end card-box -->
            </div><!-- end col -->


            <div class="col-lg-4">
                <div class="card-box bg-pattern">
                    <div class="text-center">
                        <img src="{{asset('assets/images/companies/apple.png')}}"alt="logo" class="avatar-xl rounded-circle mb-3">
                        <h4 class="mb-1 font-20">IOS Doctor APP</h4>
                        <p class="text-muted  font-14">Version Name: {{ $data['ios_doc']->version_name }}</p>
                        <p class="text-muted  font-14">Verion {{ $data['ios_doc']->version }}</p>
                    </div>
                    <div class="row mt-4 text-center">
                        <div class="col-6">
                            <h5 class="font-weight-normal text-muted">Update Type</h5>
                            @if($data['ios_doc']->update_type==0)
                                <h4>No-Update</h4>
                            @elseif($data['ios_doc']->update_type==1)
                                <h4>Minor Update</h4>
                            @else
                                <h4>Major Update</h4>
                            @endif
                        </div>
                    </div>
                </div> <!-- end card-box -->
            </div><!-- end col -->

        </div>
        <!-- end row -->

    </div> <!-- container -->
@endsection