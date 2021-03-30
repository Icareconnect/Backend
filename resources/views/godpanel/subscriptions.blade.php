  @extends('layouts.vertical', ['title' => 'Subscriptions'])
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
              <h5 class="card-title">Subscriptions</h5>
               <a href="{{ url('subscriptions/new')}}" class="btn btn-sm btn-info float-right">Add New Subscriptions</a>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
                <thead>
                <tr >
                  <th>Sr No.</th>
                  <th>Name</th>
                  <th>Type</th>
                  <th>Price</th>
                  <th>Global Subscription</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                 @foreach($subscriptions as $index => $subscription)
                  <tr>
                    <td>{{ $index+1 }}</td>
                    <td>{{ $subscription->name }}</td>
                    <td>{{ ucfirst($subscription->type) }}</td>
                    <td>{{ $subscription->price }}</td>
                    <td>{{ ucfirst($subscription->global_type) }}</td>
                    <td>
                      <a href="{{ route('edit-subscription',['subscription_id'=>$subscription->id])}}" class="btn btn-sm btn-info float-left">Edit</a>
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
    <!-- Page js-->
    <script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>
   
@endsection