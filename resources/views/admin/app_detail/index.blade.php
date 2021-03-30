@extends('layouts.vertical', ['title' => 'App Details'])

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
                            App Detail
                        </p>
                        @if($AppDetails->count()<=0)
                        <a href="{{ url('admin/app_detail/create')}}" class="btn btn-sm btn-info float-right">Add App Detail</a>
                        @endif
                        <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
                                <thead>
                                    <tr >
                                        <th>Sr No.</th>
                                        <th>App Logo</th>
                                        <th>Background Color</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                 @foreach($AppDetails as $index => $AppDetail)
                                    <tr>
                                      <td>{{ $index+1 }}</td>
                                      <td>{{ $AppDetail->app_logo }}</td>
                                      <td>{{ $AppDetail->background_color }}</td>
                                      <td><a href="{{ url('admin/app_detail') .'/'.$AppDetail->id.'/edit'}}" class="btn btn-sm btn-info float-left">Edit</a>
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