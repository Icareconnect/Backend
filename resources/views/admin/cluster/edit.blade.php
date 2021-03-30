@extends('layouts.vertical', ['title' => 'Edit Cluster'])

@section('css')

@endsection

@section('content')
<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">Edit Cluster</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ url('admin/cluster').'/'.$cluster->id}}" method="post">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
       <input type="hidden" name="_method" value="PUT">
        <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
          <div class="form-group">
            <div class="row">
                  <div class="col-sm-4">
                      <label for="name">Name</label>
                      <input type="text" name="name" class="form-control" value="{{ old('name')??$cluster->name }}" id="name" placeholder="name">
                      @if ($errors->has('name'))
                              <span class="text-danger">{{ $errors->first('name') }}</span>
                      @endif
                </div>
            </div> 
          </div>
          <div class="form-group">
                  <label>Description</label><br>
                  <textarea rows="5" name="description">{{ old('description')??$cluster->description }}</textarea>
                  @if ($errors->has('description'))
                      <span class="text-danger">{{ $errors->first('description') }}</span>
                  @endif
          </div>   
          <div class="form-group">
            <div class="row">
                  <div class="col-sm-4">
                      <label >Category</label>
                      <select class="form-control category_listing" name="categories[]" multiple="multiple">
                          <option value="">--Select Status--</option>
                          @foreach($categories as $cat_key=>$parentCategory)
                          <option <?php echo ((old('categories')!==null &&  in_array($parentCategory->id, old('categories'))) || in_array($parentCategory->id,$cluster->cluster_category->pluck('category_id')->toArray()))?"selected":'' ?>  value="{{ $parentCategory->id }}">{{ $parentCategory->name }}</option>
                          @endforeach
                        </select>
                        @if ($errors->has('categories'))
                          <span class="text-danger">{{ $errors->first('categories') }}</span>
                        @endif
                </div>
            </div>
           </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
  </div>
@endsection