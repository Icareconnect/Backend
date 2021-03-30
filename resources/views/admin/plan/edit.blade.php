@extends('layouts.vertical', ['title' => 'Edit Subscription Plan'])

@section('content')
<div class="card card-primary">
   <div class="card-header">
      <h3 class="card-title">Edit Subscription Plan</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ url('admin/subscription').'/'.$plan->id}}" method="post" enctype="multipart/form-data">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="PUT">
        <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
          <div class="form-group row">
              <div class="col-sm-6">
                <label for="">Name</label><br>
                <input type="text" class="form-control"  name="name"  placeholder="Name" required="" value="{{ old('name')??$plan->name }}">
                @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
              </div>
              <div class="col-sm-6">
                <label for="page_body__">Description</label><br>
                <textarea class="form-control" rows="3"   name="description"  id="page_body__" placeholder="Place some text here" >{{{old('description')??$plan->description }}}</textarea>
                @if ($errors->has('description'))
                        <span class="text-danger">{{ $errors->first('description') }}</span>
                @endif
              </div>
          </div> 
          
          <div class="form-group row">
              <div class="col-sm-6">
                <label for="page_body__">Subscription ID</label><br>
               <input type="text" class="form-control"  name="subscription_id"  placeholder="Subscription ID" required="" value="{{ old('subscription_id')??$plan->plan_id }}">
                @if ($errors->has('subscription_id'))
                        <span class="text-danger">{{ $errors->first('subscription_id') }}</span>
                @endif
              </div>
              <div class="col-sm-6">
                <label for="price">Price</label><br>
               <input type="number" class="form-control"  name="price"  placeholder="Price" required="" value="{{ old('price')??$plan->price }}">
                @if ($errors->has('price'))
                        <span class="text-danger">{{ $errors->first('price') }}</span>
                @endif
              </div>
          </div>
          <div class="form-group row">
              <!-- <div class="col-sm-6">
                <label for="page_body__">Plan Type</label><br>
               <input type="text" class="form-control"  name="subscription_id"  placeholder="Subscription ID" required="" value="{{ old('subscription_id') }}">
                @if ($errors->has('subscription_id'))
                        <span class="text-danger">{{ $errors->first('subscription_id') }}</span>
                @endif
              </div> -->
              <div class="col-sm-6">
                  <label >Status</label>
                  <select class="form-control" name="status">
                      <option <?php echo (old('status')??$plan->status=='enable')?"selected":'' ?>  value="enable">Enable</option>
                      <option <?php echo (old('status')??$plan->status=='disable')?"selected":'' ?>  value="disable">Disable</option>
                  </select>
                  @if ($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                  @endif
              </div>
          </div>
          <div class="form-group">
            <div class="row">
                  <div class="col-sm-8">
                      <label >Features</label>
                      <select class="form-control category_listing" name="permission[]" multiple="multiple">
                          @foreach($permissions as $permission)
                          <option <?php echo ((old('permission')!==null &&  in_array($permission, old('permission'))) || in_array($permission,$plan->permission))?"selected":'' ?>  value="{{ $permission }}">{{ $permission }}</option>
                          @endforeach
                        </select>
                        @if ($errors->has('permission'))
                          <span class="text-danger">{{ $errors->first('permission') }}</span>
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