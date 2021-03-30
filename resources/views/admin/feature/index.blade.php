@extends('layouts.vertical', ['title' => 'Enable Feature Type'])

@section('css')
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />

@endsection

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Enable Feature Type</h3>
              </div>
                <div class="card-body">
                    <table id="customers_pagination" class="table table-bordered table-striped">
                      <thead>
                      <tr >
                        <th>Sr No.</th>
                        <th>Feature Type Name</th>
                        <th>Action</th>
                      </tr>
                      </thead>
                      <tbody>
                       @foreach($client_features as $index => $client_feature)
                        <tr>
                          <td>{{ $index+1 }}</td>
                          <td>{{ $client_feature->feature_type->name }}</td>
                          <td>
                            <a class="edit_feature_type btn btn-sm btn-info float-left" href="{{url('admin/feature-types/'.$client_feature->feature_type->id)}}">View/Update Features</a>
                          </td>
                        </tr>
                     @endforeach   
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Plugins js-->
    <script src="{{asset('assets/libs/datatables/datatables.min.js')}}"></script>

    <!-- Page js-->
    <script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>
@endsection