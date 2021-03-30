@extends('layouts.vertical', ['title' => 'HealthCareVisit'])

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
                            <li class="breadcrumb-item active">HealthCareVisit</li>
                        </ol>
                    </div>
                    <h4 class="page-title">HealthCareVisit</h4>
                </div>
            </div>
        </div> 

	<div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted font-13 mb-4">
                           
                        </p>
                        <a href="{{ route('healthCareVisit.create')}}" class="btn btn-sm btn-info float-right">Add HealthCareVisit</a>

                        <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
                            	<thead>
					            	<tr >
						            	<th>Sr No.</th>
						            	<th>HealthCareVisit Value</th>
						            	<th>Created At</th>
						            	<th>Action</th>
					            	</tr>
					            </thead>
					            <tbody>
					             @foreach($slots as $index => $slot)
						            <tr>
						              <td>{{ $index+1 }}</td>
                                      <td>{{ $slot->health_care_value }}</td>
						              <td>{{ $slot->created_at }}</td>
						              <td>
                                        <a href="{{ url('admin/healthCareVisit') .'/'.$slot->id.'/edit'}}" class="btn btn-sm btn-info float-left" style="margin-right: 10px;">Edit</a>

                                        <form action="healthCareVisit/{{$slot->id}}" method="post">
                                           {!! method_field('delete') !!}
                                           {!! csrf_field() !!}
                                            <button class="btn btn-sm btn-info" type="submit">Delete</button>
                                        </form>
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