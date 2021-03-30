@extends('layouts.vertical', ['title' => 'Additional Documents'])

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
                            <li class="breadcrumb-item active">Additional Documents</li>
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
                            Additional Documents Listing
                        </p>
                        <a href="{{ url('admin/additional-document/create')}}" class="btn btn-sm btn-info float-right">Add Additional Documents</a>
                        <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
                                <thead>
                                    <tr >
                                        <th>Sr No.</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Enable</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($additionals as $index => $additional)
                                    <tr>
                                      <td>{{ $index+1 }}</td>
                                      <td>{{ $additional->name }}</td>
                                      <td>{{ $additional->type }}</td>
                                      <td><?php echo ($additional->is_enable=='1')?"True":'False' ?> </td>
                                      <td><a href="{{ route('additional-document.edit',[$additional->id]) }}" class="btn btn-sm btn-info float-left">Edit</a>
                                      <!-- <button type="button" class="btn btn-danger btn-xs delete-filter"  data-filter_id="{{ $additional->id }}">Delete</button> -->
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