@extends('layouts.vertical', ['title' => 'Slots'])

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
                            <li class="breadcrumb-item active">Slots</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Slots</h4>
                </div>
            </div>
        </div> 

	<div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted font-13 mb-4">
                           
                        </p>
                        <a href="{{ route('slots.create')}}" class="btn btn-sm btn-info float-right">Add Slots</a>

                        <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
                            	<thead>
					            	<tr >
						            	<th>Sr No.</th>
						            	<th>Slots Value</th>
						            	<th>Created At</th>
						            	<th>Action</th>
					            	</tr>
					            </thead>
					            <tbody>
					             @foreach($slots as $index => $slot)
						            <tr>
						              <td>{{ $index+1 }}</td>
						              @if($slot->slot_value == 1)
                                         <td>{{ $slot->slot_value }} hour </td>
                                      @else
                                         <td>{{ $slot->slot_value }} min </td>
                                      @endif
						              <td>{{ $slot->created_at }}</td>
						              <td>
                                        <a href="{{ url('admin/slots') .'/'.$slot->id.'/edit'}}" class="btn btn-sm btn-info float-left" style="margin-right: 10px;">Edit</a>

                                        <form action="slots/{{$slot->id}}" method="post">
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