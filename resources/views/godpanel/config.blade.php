  @extends('layouts.vertical', ['title' => 'Variables'])
  @section('css')
      <!-- Plugins css -->
      <link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
      <link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
  @endsection
  @section('content')
     <!-- Start Content-->
    <div class="container-fluid">
    <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">Config Variables</h5>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
                <thead>
                <tr >
                  <th>Sr No.</th>
                  <th>Name</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                 @foreach($godconfigs as $index => $godconfig)
                  <tr>
                    <td>{{ $index+1 }}</td>
                    <td>{{ $godconfig->key_name }}</td>
                    <td>
                      @if(strtolower($godconfig->key_name)=='cache enable')
                      <div class="custom-control custom-switch">
                      <input type="checkbox" name="feature_set" class="custom-control-input"  data-godconfig_id="{{ $godconfig->id }}"   id="customSwitch{{ $index }}" <?php echo($godconfig->key_value=='1')?'checked':'' ?> >
                      <label class="custom-control-label" for="customSwitch{{ $index }}">Update</label>
                      </div>

                      @elseif(strtolower($godconfig->key_name)=='cache clear')
                        <a href="javascript:void(0);" class="clear_cache edit_feature_type <?php echo ($cache==false)?'disabled':'' ?>  btn btn-sm btn-info float-left"  data-godconfig_id="{{ $godconfig->id }}" >Clear</a>
                      @endif
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
</div>
@endsection

@section('script')
    <!-- Plugins js-->
    <script src="{{asset('assets/libs/datatables/datatables.min.js')}}"></script>
        <script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>

    <script type="text/javascript">
    $(document).ready(function() {
      $('.custom-control-input').click(function(){
          var godconfig_id = $(this).attr('data-godconfig_id');
          var enable = false;
          if($(this).is(':checked')){
              $.ajax({
               type:'POST',
               url:base_url+'/godpanel/variables/update',
               data:{'godconfig_id':godconfig_id,'enable':true},
               success:function(data){

               }
            });
          } else {
              $.ajax({
               type:'POST',
               url:base_url+'/godpanel/variables/update',
               data:{'godconfig_id':godconfig_id,'enable':false},
               success:function(data){

               }
            });
          }
      });
    });
    $(document).ready(function() {
      $('.clear_cache').click(function(){
          var godconfig_id = $(this).attr('data-godconfig_id');
          var enable = false;
          $.ajax({
             type:'POST',
             url:base_url+'/godpanel/variables/update',
             data:{'godconfig_id':godconfig_id},
             success:function(data){
              Swal.fire(
                  'Cleared!',
                  'Cache has been cleared.',
                  'success'
                ).then((result)=>{

                });
             }
          });
      });
    });

    </script>
   
@endsection