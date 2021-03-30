  @extends('layouts.vertical', ['title' => 'Features'])
  @section('css')
      <!-- Plugins css -->
      <link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
  @endsection
  @section('content')
     <!-- Start Content-->
    <div class="container-fluid">

     <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">Feature Type</h5>
               <a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal" class="btn btn-sm btn-info float-right">Add New Feature Type</a>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="scroll-horizontal-datatable2" class="table w-100 nowrap">
                <thead>
                <tr >
                  <th>Sr No.</th>
                  <th>Name</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                 @foreach($feature_types as $index => $feature_type)
                  <tr>
                    <td>{{ $index+1 }}</td>
                    <td>{{ $feature_type->name }}</td>
                    <td>
                      <a href="javascript:void(0)" data-feature_id="{{ $feature_type->id }}" data-feature_name="{{ $feature_type->name }}" class="edit_feature_type btn btn-sm btn-info float-left">Edit</a>
                    </td>
                  </tr>
               @endforeach   
              </tbody>
              </table>
        </div>
  <!-- /.card-body -->
  </div>
  <!-- /.card -->
  </div>
  <!-- /.col -->
  </div>

    <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">Features</h5>
               <a href="{{ route('features-new')}}" class="btn btn-sm btn-info float-right">Add New Feature</a>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
                <thead>
                <tr >
                  <th>Sr No.</th>
                  <th>Name</th>
                  <th>Feature Type</th>
                  <th>Subscription</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                 @foreach($features as $index => $feature)
                  <tr>
                    <td>{{ $index+1 }}</td>
                    <td>{{ $feature->name }}</td>
                    <td>{{ $feature->feature_type->name }}</td>
                    <td>{{ $feature->subscriptions->pluck('subscription.name') }}</td>
                    <td>
                      <a  href="{{ route('edit-feature',['feature_id'=>$feature->id])}}" class="btn btn-sm btn-info float-left">Edit</a>
                    </td>
                  </tr>
               @endforeach   
              </tbody>
              </table>
        </div>
  <!-- /.card-body -->
  </div>
  <!-- /.card -->
  </div>
  <!-- /.col -->
  </div>

  <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <form role="form" action="{{ route('feature-type-new')}}" method="post">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="post">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title">New Feature Type</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              </div>
              <div class="modal-body p-4">
                  <div class="row">
                      <div class="col-md-12">
                          <div class="form-group">
                              <label for="field-1" class="control-label">Name</label>
                              <input required="" name="name" type="text" class="form-control" id="field-1" placeholder="Feature Type Name">
                          </div>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-info waves-effect waves-light">Save changes</button>
              </div>
          </div>
      </div>
    </form>
  </div><!-- /.modal -->

  <div id="edit_feature_type_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <form role="form" action="{{ route('feature-type-update')}}" method="post">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="post">
      <input type="hidden" name="feature_type_id" id="feature_type_id">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title">Update Feature Type</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              </div>
              <div class="modal-body p-4">
                  <div class="row">
                      <div class="col-md-12">
                          <div class="form-group">
                              <label for="edit_feature_type_name" class="control-label">Name</label>
                              <input required="" name="name" type="text" class="form-control" id="edit_feature_type_name" placeholder="Feature Type Name">
                          </div>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-info waves-effect waves-light">Save changes</button>
              </div>
          </div>
      </div>
    </form>
  </div><!-- /.modal -->
 
</div>
@endsection

@section('script')
    <!-- Plugins js-->
    <script src="{{asset('assets/libs/datatables/datatables.min.js')}}"></script>
    <script type="text/javascript">
    $(document).ready(function() {
             $('#scroll-horizontal-datatable2,#scroll-horizontal-datatable').DataTable({
                "scrollX": true,
                "language": {
                    "paginate": {
                        "previous": "<i class='mdi mdi-chevron-left'>",
                        "next": "<i class='mdi mdi-chevron-right'>"
                    }
                },
                "drawCallback": function () {
                    $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                }
            });
             $(".edit_feature_type").click(function(e){
                  e.preventDefault();
                  var feature_id = $(this).attr('data-feature_id');
                  var feature_name = $(this).attr('data-feature_name');
                  $("#feature_type_id").val(feature_id);
                  $("#edit_feature_type_name").val(feature_name);
                  $("#edit_feature_type_modal").modal('show');

            });
        });
    </script>
   
@endsection