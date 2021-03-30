@extends('layouts.vertical', ['title' => 'Categories'])

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
                            <li class="breadcrumb-item active">Categories</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Categories</h4>
                </div>
            </div>
        </div> 

    <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted font-13 mb-4">
                            Category Listing
                        </p>
                        <a href="{{ url('admin/categories/create')}}" class="btn btn-sm btn-info float-right">Add Main category</a>
                        <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
                                <thead>
                                    <tr >
                                        <th>Sr No.</th>
                                        <th>Name</th>
                                        <th>Color Code</th>
                                        <th>Child Cat.</th>
                                        <th>Enable On Front-End</th>
                                        <th>Enable Service Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                 @foreach($parentCategories as $index => $category)
                                    <tr>
                                      <td>{{ $index+1 }}</td>
                                      <td>{{ $category->name }}</td>
                                      <td>{{ $category->color_code }}</td>
                                      <td>{{ $category->subcategory->count() }}</td>
                                      <td>{{ ($category->enable=='1'?'Yes':'No') }}</td>
                                      <td>{{ ($category->enable_service_type=='1'?'Yes':'No') }}</td>
                                      <td><a href="{{ url('admin/categories') .'/'.$category->id.'/edit'}}" class="btn btn-sm btn-info float-left">Edit</a>
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