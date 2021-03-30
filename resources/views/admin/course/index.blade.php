@extends('layouts.vertical', ['title' => 'Courses'])

@section('css')
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />

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
                            <li class="breadcrumb-item active">Courses</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Additional Documents</h4>
                </div>
            </div>
        </div> 

    <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted font-13 mb-4">
                            Courses Listing
                        </p>
                        <a href="{{ url('admin/course/create')}}" class="btn btn-sm btn-info float-right">Add Courses</a>
                        <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
                                <thead>
                                    <tr >
                                        <th>Sr No.</th>
                                        <th>Title</th>
                                        <th>Color Code</th>
                                        <th>Image</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($course as $index => $additional)
                                    <tr>
                                      <td>{{ $index+1 }}</td>
                                      <td>{{ $additional->title }}</td>
                                      <td>{{ $additional->color_code }}</td>
                                      <td> <img src="{{ Storage::disk('spaces')->url('uploads/'.$additional->image_icon) }}" id="profile-img-tag-icon" style="width: 50px;height: 50px"></td>
                                      <td><a href="{{ route('course.edit',[$additional->id]) }}" class="btn btn-sm btn-info float-left">Edit</a>
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
    <script src="{{asset('assets/libs/pdfmake/pdfmake.min.js')}}"></script>

    <!-- Page js-->
    <script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>
@endsection