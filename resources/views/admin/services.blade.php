@extends('layouts.vertical', ['title' => 'Coupons'])

@section('css')
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
  <div class="card">
    <div class="card-header border-transparent">
      <h3 class="card-title">Variables</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
          <thead>
          <tr>
            <th>Sr No.</th>
            <th>Variable Type</th>
            <th>Key</th>
            <th>Value</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
          <?php $inc = 1; ?>
          </thead>
          <tbody>
          @foreach($services as $index => $service)
           @if(config('client_connected') && (Config::get("client_data")->domain_name=="physiotherapist" || Config::get("client_data")->domain_name=="food") && ($service->type=='charges'||$service->type=='audio/video'||$service->type=='class_calling' ||$service->type=='insurance'))
           
          @else
          <tr>
            <td><?php echo $inc++; ?></td>
            <td>{{$service->type}}</td>
            @if($service->type=='unit_price' && \Config('client_connected') && \Config::get("client_data")->domain_name=="intely")
                <td>Hour</td>
                <td>{{ ($service->value/60) }}</td>
            @else
                <td>{{$service->key_name}}</td>
                <td>{{$service->value}}</td>
            @endif
            
            <td><span class="badge badge-success">Enabled</span></td>
            <td><a href="{{ url('admin/service_enable') .'/'.$service->id.'/edit'}}" class="btn btn-sm btn-info float-left">Edit</a></td>
          </tr>
          @endif
          @endforeach
          </tbody>
        </table>
      <!-- /.table-responsive -->
    </div>
    <!-- /.card-body -->
    <!-- /.card-footer -->
  </div>
@endsection

@section('script')
    <!-- Plugins js-->
    <script src="{{asset('assets/libs/datatables/datatables.min.js')}}"></script>
    <!-- Page js-->
    <script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>

@endsection