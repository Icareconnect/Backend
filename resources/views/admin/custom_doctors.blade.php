@extends('layouts.vertical', ['title' => 'Physiotherapists'])

@section('css')
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted font-13 mb-4">
                            Physiotherapists
                        </p>
                        <a href="{{ url('admin/centre/doctor/create')}}" class="btn btn-sm btn-info float-right">Add New Physiotherapist</a>
                        <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
                                <thead>
                                    <tr >
                                        <th>Sr No.</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                 @foreach($doctors as $index => $doctor)
                                    @php $d_detail = json_decode($doctor->raw_detail); @endphp
                                    <tr>
                                      <td>{{ $index+1 }}</td>
                                      <td>{{ $d_detail->first_name }}</td>
                                      <td>{{ $d_detail->last_name }}</td>
                                      <td>
                                        <ul style="padding: initial;">
                                            <li style="display:inline-block;"><a href="{{ url('admin/centre/doctor') .'/'.$doctor->id.'/edit'}}" class="btn btn-sm btn-info"><i class="fas fa-edit" style="cursor: pointer;"></i></a></li>
                                            <li style="display:inline-block;"><button data-user_id="{{ $doctor->id }}" class="btn btn-sm btn-danger deleteCustomDoctor"><i class="fe-trash"></i></button>
                                            </li>
                                        </ul>
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
    <script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
    <script type="text/javascript">
        $(function () {
             $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#scroll-horizontal-datatable').on('click', '.deleteCustomDoctor', function(e){
                  e.preventDefault();
                  var _this = $(this);
                  var user_id = $(this).attr('data-user_id');
                  Swal.fire({
                    title: 'Do You Want To Delete This Physiotherapist ?',
                    text: "You won't be able to revert this!",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                  }).then((result) => {
                    if (result.value) {
                        $.ajax({
                           type:'POST',
                           url:base_url+'/admin/centre/doctor/delete',
                           data:{"doctor_id":user_id},
                           success:function(data){
                             _this.parents('tr').remove();
                              Swal.fire(
                                'Deleted!',
                                'Physiotherapist has been deleted.',
                                'success'
                              ).then((result)=>{
                                // window.location.reload();
                              });
                           }
                        });
                      }
                  });
            });
        });
    </script>
@endsection