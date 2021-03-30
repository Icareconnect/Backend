	@extends('adminlte::page')

	@section('title', 'Filter  Type')

	@section('content_header')
	<h1>Filter Type</h1>
	@stop
	@section('content')
    @if (Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h4 class="alert-heading">Success!</h4>
                <p>{{ Session::get('success') }}</p>

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Filter</h3>
                <a href="{{ url('admin/filters/create')}}" class="btn btn-sm btn-info float-right">Add New Filter</a>
              </div>
                <div class="card-body">
                    <table id="customers_pagination" class="table table-bordered table-striped">
                      <thead>
                      <tr >
                        <th>Sr No.</th>
                        <th>Filter Name</th>
                        <th>Preference Name</th>
                        <th>Category Name</th>
                        <th>Multi Select</th>
                        <th>Filter Options</th>
                        <th>Action</th>
                      </tr>
                      </thead>
                      <tbody>
                       @foreach($filtertypes as $index => $filtertype)
                        <tr>
                          <td>{{ $index+1 }}</td>
                          <td>{{ $filtertype->filter_name }}</td>
                          <td>{{ $filtertype->preference_name }}</td>
                          <td>{{ $filtertype->category->name }}</td>
                          <td><?php echo ($filtertype->is_multi=='1')?"True":'False' ?> </td>
                          <td>{{ $filtertype->options->pluck('option_name') }}</td>
                          <td><a href="{{ url('admin/filters') .'/'.$filtertype->id.'/edit'}}" class="btn btn-sm btn-info float-left">Edit</a>
                          <a class="btn btn-danger btn-sm delete-filter" data-filter_id="{{ $filtertype->id }}" href="javascript:void(0)">
                              <i class="fas fa-trash">
                              </i>
                              Delete
                            </a>
                          </td>
                        </tr>
                     @endforeach   
                    </tbody>
                      <tfoot>
                      <tr>
                        <th>Sr No.</th>
                        <th>Filter Name</th>
                        <th>Preference Name</th>
                        <th>Category Name</th>
                         <th>Multi Select</th>
                        <th>Filter Options</th>
                        <th>Action</th>
                      </tr>
                      </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
	<!-- ./wrapper -->
	<!-- page script -->
	@stop