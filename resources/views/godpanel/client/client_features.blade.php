  @extends('layouts.vertical', ['title' => 'Client Features'])
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
              <h5 class="card-title">Client Features</h5>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
                <thead>
                <tr >
                  <th>Sr No.</th>
                  <th>Name</th>
                  <th>Feature Type</th>
                  <!-- <th>Subscription</th> -->
                  <th>Assignment Status</th>
                </tr>
                </thead>
                <tbody>
                 @foreach($features as $index => $feature)
                  <tr>
                    <td>{{ $index+1 }}</td>
                    <td>{{ $feature->name }}</td>
                    <td>{{ $feature->feature_type->name }}</td>
                    <!-- <td>{{ $feature->subscriptions->pluck('subscription.name') }}</td> -->
                    <td>
                      <div class="custom-control custom-switch">
                      <input type="checkbox" name="feature_set" class="custom-control-input" data-client_id="{{ $client_id }}" data-feature_id="{{ $feature->id }}"   id="customSwitch{{ $index }}" <?php echo(in_array($feature->id,$client_features))?'checked':'' ?> >
                      <label class="custom-control-label" for="customSwitch{{ $index }}">Assign</label>
                    </div>
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

    <script type="text/javascript">
    $(document).ready(function() {
      $('.custom-control-input').click(function(){
          var client_id = $(this).attr('data-client_id');
          var feature_id = $(this).attr('data-feature_id');
          if($(this).is(':checked')){
              $.ajax({
                   type:'POST',
                   url:base_url+'/client/'+client_id+'/features/update',
                   data:{'client_id':client_id,'feature_id':feature_id,'assign':true},
                   success:function(data){

                   }
                });
          } else {
              $.ajax({
                 type:'POST',
                 url:base_url+'/client/'+client_id+'/features/update',
                 data:{'client_id':client_id,'feature_id':feature_id,'assign':false},
                 success:function(data){

                 }
              });
          }
      });
    });

    </script>
   
@endsection