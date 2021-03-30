	@extends('adminlte::page')

	@section('title', 'Categories')

	@section('content_header')
	<h1>Categories</h1>
	@stop
	@section('content')
    @if (Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h4 class="alert-heading">Success!</h4>
                <p>{{ Session::get('success') }}</p>

                <button type="button" class="close" data-dismiss="alert aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

    <!-- <div class="modal" tabindex="-1" role="dialog" id="editCategoryModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Category</h5>

            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <form action="" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-body">
              <div class="form-group">
                <input type="text" name="name" class="form-control" value="" placeholder="Category Name" required>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Update</button>
            </div>
          </form>
          </div>
      </div>
    </div> -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Categories</div>
                <div class="card-body">
                  <ul class="list-group">
                    @foreach ($parentCategories as $category)
                      <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                         <span style="color:{{ $category->color_code }}"> {{ $category->name }}</span>

                          <!-- <div class="button-group d-flex"> -->
                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-primary mr-1 edit-category" >Edit</a>

                            <!-- <form action="{{ route('categories.destroy', $category->id) }}" method="POST">
                              @csrf
                              @method('DELETE')

                              <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form> -->
                          <!-- </div> -->
                        </div>

                        @if ($category->subcategory)
                            @include('admin.categories.subCategoryList',['subcategories' => $category->subcategory, 'dataParent' => $category->id , 'dataLevel' => 1])
                        @endif
                      </li>
                    @endforeach
                  </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <h3>Create Category</h3>
            </div>

            <div class="card-body">
              <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                  <ul role="tree" aria-labelledby="tree_label">
                  @foreach($parentCategories as $category)
                    <li role="treeitem" data-parent_id="{{ $category->id }}" data-cate_name="{{$category->name}}" aria-expanded="false" >
                      <span>{{$category->name}}</span>
                    @if(count($category->subcategory))
                      @include('admin.categories.subCat',['subcategories' => $category->subcategory])
                    @else
                    @endif
                      <!-- {{$category->name}} -->
                    </li>
                  @endforeach
                  </ul>
                  <input type="hidden" id="last_action" name="parent_id" type="text" readonly="">
                  @if ($errors->has('parent_id'))
                    <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                  @endif
                </div>

                <div class="form-group">
                  <input type="text" name="name" id="category_selected" class="form-control" value="{{ old('name') }}" placeholder="Category Name" required>
                  @if ($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                  @endif
                </div>
                <div class="form-group">
                  <label>Color picker:</label>
                  <input name="color_code" type="text" value="{{ old('color_code') }}" class="form-control my-colorpicker1">
                   @if ($errors->has('color_code'))
                    <span class="text-danger">{{ $errors->first('color_code') }}</span>
                  @endif
                </div>
                <div class="form-group">
                  <label>Description</label>
                  <textarea name="description">{{ old('description') }}</textarea>
                  @if ($errors->has('description'))
                      <span class="text-danger">{{ $errors->first('description') }}</span>
                  @endif
                </div>
                <div class="form-group">
                    <label for="exampleInputFile">Image</label>
                    <div class="input-group">
                      <div >
                        <input type="file" value="{{ old('category_image') }}" name="category_image" id="exampleInputFile">
                      </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="exampleInputFile">Icon</label>
                    <div class="input-group">
                      <div >
                        <input type="file" value="{{ old('image_icon') }}" name="image_icon" id="exampleInputFile">
                      </div>
                    </div>
                </div>
                <div class="form-group">
                  <button type="submit" class="btn btn-primary">Create</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    </div>
	<!-- ./wrapper -->
	<!-- page script -->
	@stop