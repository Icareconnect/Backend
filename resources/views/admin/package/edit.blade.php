@extends('layouts.vertical', ['title' => 'Package'])

@section('content')
<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">Edit Package</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ url('admin/package').'/'.$package->id}}" method="post" enctype="multipart/form-data">
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
                  <div class="col-sm-6">
                    <label for="title">Title</label>
                    <div class="input-group">
                        <input class="form-control" type="text" value="{{old('title')??$package->title }}" placeholder="title" name="title" id="title">
                    </div>
                     @if ($errors->has('title'))
                                    <span class="text-danger">{{ $errors->first('title') }}</span>
                      @endif
                  </div>
                  <div class="col-sm-6">
                     <label for="description">Description</label>
                        <div class="input-group">
                            <textarea rows="5" class="form-control" placeholder="description" name="description" id="description">{{old('description')??$package->description }}</textarea>
                        </div>
                       @if ($errors->has('description'))
                                      <span class="text-danger">{{ $errors->first('description') }}</span>
                              @endif
                  </div>
              </div>
          </div>
          <div class="form-group">
             <div class="row">
                  <div class="col-sm-6">
                    <label for="exampleInputFile">Image</label>
                    <div class="input-group">
                      <div >
                        <input type="file" value="{{old('image') }}" name="image" id="ct-img-file">
                        <img src="{{ Storage::disk('spaces')->url('thumbs/'.$package->image) }}" id="profile-img-tag" width="200px" />
                      </div>
                    </div>
                     @if ($errors->has('image'))
                                    <span class="text-danger">{{ $errors->first('image') }}</span>
                      @endif
                  </div>
                  <div class="col-sm-6">
                     <label for="price">Price</label>
                        <div class="input-group">
                            <input class="form-control" type="number" placeholder="Price" value="{{old('price')??$package->price }}" name="price" id="price">
                        </div>
                         @if ($errors->has('price'))
                              <span class="text-danger">{{ $errors->first('price') }}</span>
                      @endif
                  </div>
              </div>
          </div>
          <div class="form-group">
            <div class="row">
                  <div class="col-sm-6">
                      <label >Package Type</label>
                       <div class="input-group">
                            <input class="form-control" type="text" placeholder="package_type" value="{{$package->package_type }}" name="package_type" id="package_type" disabled="">
                        </div>
                        @if ($errors->has('package_type'))
                          <span class="text-danger">{{ $errors->first('package_type') }}</span>
                        @endif
                </div>

                  <div class="col-sm-6">
                      <label >Category</label>
                      <select class="form-control" name="category">
                          <option value="">--Select Status--</option>
                          @foreach($categories as $cat_key=>$parentCategory)
                          <option <?php echo ($package->filter_id==null&&$package->category_id==$parentCategory->id)?"selected":'' ?>  value="{{ $parentCategory->id }}">{{ $parentCategory->name }}</option>

                          @if($parentCategory->is_filter)
                            @foreach($parentCategory->filters as $filter)
                            <option <?php echo ($package->filter_id!=null&&$package->filter_id==$filter['data']['id'])?"selected":'' ?>  value="{{ 'filter_'.$filter['data']['id'].'_category_'.$parentCategory->id }}">{{ $filter['data']['option_name'] }}</option>
                            @endforeach
                          @endif
                          @endforeach
                        </select>
                        @if ($errors->has('category'))
                          <span class="text-danger">{{ $errors->first('category') }}</span>
                        @endif
                </div>
            </div>
           </div>
           <div class="form-group">
            <div class="row">
                  <div class="col-sm-6">
                      <label for="total_requests">Total Sessions/Requests</label>
                      <input type="number" name="total_requests" class="form-control" value="{{ old('total_requests')??$package->total_requests }}" id="total_requests" placeholder="Total Sessions/Requests">
                      @if ($errors->has('total_requests'))
                              <span class="text-danger">{{ $errors->first('total_requests') }}</span>
                      @endif
                </div>
                  <div class="col-sm-6">
                      <label >Enable</label>
                      <select class="form-control" name="enable">
                          <option <?php echo ($package->enable=='1')?"selected":'' ?>  value="1">True</option>
                          <option <?php echo ($package->enable=='0')?"selected":'' ?>  value="0">False</option>
                        </select>
                        @if ($errors->has('enable'))
                          <span class="text-danger">{{ $errors->first('enable') }}</span>
                        @endif
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