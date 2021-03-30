@extends('layouts.vertical', ['title' => 'Support Package'])

@section('css')
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />

@endsection

@section('content')
    <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted font-13 mb-4">
                            Package Listing
                        </p>
                        <a href="{{ url('admin/support_packages/create')}}" class="btn btn-sm btn-info float-right">Add New Package</a>
                        <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
                                <thead>
                                    <tr >
                                        <th>Sr No.</th>
                                        <th>Title</th>
                                        <th>Color Code</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                 @foreach($packages as $index => $category)
                                    <tr>
                                      <td>{{ $index+1 }}</td>
                                      <td>{{ $category->title }}</td>
                                      <td>{{ $category->color_code }}</td>
                                      <td>{{ $category->price }}</td>
                                      <td><a href="{{ url('admin/support_packages') .'/'.$category->id.'/edit'}}" class="btn btn-sm btn-info float-left">Edit</a>
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
    <script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>
@endsection