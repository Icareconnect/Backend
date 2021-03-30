@extends('layouts.vertical', ['title' => 'Pages'])

@section('css')
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />

@endsection

@section('content')
<section class="content-header">
	      <div class="container-fluid">
	        <div class="row mb-2">
	          <div class="col-sm-6">
	            <ol class="breadcrumb float-sm-left">
	              <li class="breadcrumb-item"><a href="{{ url('admin/dahboard') }}">Home</a></li>
	              <li class="breadcrumb-item active">Pages</li>
	            </ol>
	          </div>
	        </div>
	      </div><!-- /.container-fluid -->
	    </section>
		<div class="row">
		    <div class="col-12">
		      <div class="card">
		        <div class="card-header">
		          <h3 class="card-title">Page Listing</h3>
		          <a href="{{ url('admin/pages/create')}}" class="btn btn-sm btn-info float-right">Add New Page</a>
		        </div>
		        <!-- /.card-header -->
		        <div class="card-body">
		          	<table id="scroll-horizontal-datatable" class="table w-100 nowrap">
		            <thead>
		            <tr >
		            	<th>Sr No.</th>
		            	<th>Title</th>
		            	<th>slug</th>
		            	<th>Author</th>
		            	<th>Status</th>
		            	<th>Updated At</th>
		            	<!-- <th>Created At</th> -->
		            	<th>Action</th>
		            </tr>
		            </thead>
		            <tbody>
		             @foreach($pages as $index => $page)
			            <tr>
			              <td>{{ $index+1 }}</td>
			              <td>{{ $page->title }}</td>
			              <td>{{ $page->slug }}</td>
			              <td>{{ $page->author->name }}</td>
			              <td>{{ $page->status }}</td>
			              <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $page->updated_at)->tz('Asia/Calcutta') }}</td>
			              <!-- <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $page->created_at)->tz('Asia/Calcutta') }}</td> -->
			              <td class="project-actions text-right">
			              	<a class="btn btn-primary btn-sm" href="{{ url('/') .'/'.$page->slug}}">
                              <i class="fas fa-folder">
                              </i>
                              View
                          	</a>
                          	<a class="btn btn-info btn-sm" href="{{ url('admin/pages') .'/'.$page->id.'/edit'}}">
                              <i class="fas fa-pencil-alt">
                              </i>
                              Edit
                          	</a>
                          	<a class="btn btn-danger btn-sm delete-page" data-page_id="{{ $page->id }}" href="javascript:void(0)">
                              <i class="fas fa-trash">
                              </i>
                              Delete
                          	</a>
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
	@endsection

@section('script')
    <!-- Plugins js-->
    <script src="{{asset('assets/libs/datatables/datatables.min.js')}}"></script>

    <!-- Page js-->
    <script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>

    <script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
    <script type="text/javascript">
    	$(function () {
    		 $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
    		$(".delete-page").click(function(e){
			          e.preventDefault();
			          var cluster_id = $(this).attr('data-page_id');
			          Swal.fire({
			            title: 'Are you sure?',
			            text: "You won't be able to revert this!",
			            showCancelButton: true,
			            confirmButtonColor: '#3085d6',
			            cancelButtonColor: '#d33',
			            confirmButtonText: 'Yes, delete it!'
			          }).then((result) => {
			            if (result.value) {
			                $.ajax({
			                   type:'DELETE',
			                   url:base_url+'/admin/pages/'+cluster_id,
			                   data:{id:cluster_id},
			                   success:function(data){
			                      Swal.fire(
			                        'Deleted!',
			                        'Cluster has been deleted.',
			                        'success'
			                      ).then((result)=>{
			                        window.location.reload();
			                      });
			                   }
			                });
			              }
			          });
			    
			    });
    	});
    </script>
@endsection