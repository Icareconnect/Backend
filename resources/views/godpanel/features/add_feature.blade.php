@extends('layouts.vertical', ['title' => 'New Subscription'])
@section('css')
<style type="text/css">
  .wrapper_class{
    padding-bottom: 10px;
  }
</style>
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">New Subscription</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ route('features-new')}}" method="post">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="post">
        <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
          <div class="form-group">
            <div class="row">
              <div class="col-sm-6">
                <label for="name">Name</label>
                <input  type="text" class="form-control" name="name" value="{{old('name')}}" placeholder="Enter Name" required="">
                @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
              </div>
              <div class="col-sm-4">
                <label for="app_name">Subscription Plans</label>
                <select class="form-control" name="subscription_plan[]" multiple="" required="">
                      @foreach($subscriptions as $subscription)
                        <option <?php echo (old('subscription_plan')!==null &&  in_array($subscription->id, old('subscription_plan')) )?"selected":'' ?>  value="{{ $subscription->id }}">{{ $subscription->name }}</option>
                      @endforeach
                  </select>
                  @if ($errors->has('subscription_plan'))
                    <span class="text-danger">{{ $errors->first('subscription_plan') }}</span>
                  @endif 
                </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-sm-4">
                <label for="app_name">Feature Type</label>
                <select class="form-control" name="feature_type" required="">
                      <option value="">--Select--</option>
                      @foreach($feature_types as $feature_type)
                        <option <?php echo (old('feature_type')==$feature_type->id)?"selected":'' ?>  value="{{ $feature_type->id }}">{{ $feature_type->name }}</option>
                      @endforeach
                  </select>
                  @if ($errors->has('feature_type'))
                    <span class="text-danger">{{ $errors->first('feature_type') }}</span>
                  @endif 
                </div>
            </div>
          </div>
          <br>
          FEATURE KEYS
          <hr>
          <div class="form-group">
                 <div class="row">
                  <div class="col-sm-12">
                      <div class="wrapper_class">
                        <div>
                          <br>
                          <div class="input-group">
                                <div class="col-lg-4">
                                  <input type="text" class="form-control is-warning" name="feature_keys[0][name]" placeholder="Key Name" required="">
                                </div>
                                <div class="col-lg-3">
                                  <div class="custom-control custom-switch">
                                      <input type="checkbox" name="feature_keys[0][for_front_end]" class="custom-control-input" id="customSwitch1">
                                      <label class="custom-control-label" for="customSwitch1">FOR FRONT END</label>
                                  </div>
                                </div>
                                 <div class="col-lg-2">
                                    <span class="btn btn-danger delete_icon">Delete - </span>
                                </div>
                          </div>
                        </div>
                      </div>
                       @if ($errors->has('filter_option'))
                        <span class="text-danger">{{ $errors->first('filter_option') }}</span>
                      @endif
                  </div>
                </div>
                <button class="btn btn-primary add_more_option">Add More +</button>
          </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
  </div>
@endsection
@section('script')
 <script type="text/javascript">
  $(document).ready(function() {
      var wrapper         = $(".wrapper_class");
      var add_button      = $(".add_more_option");
   
      var x = 1;
      $(add_button).click(function(e){
          e.preventDefault();
          x++;
          var id = "customSwitch"+x;
          $(wrapper).append(`<div><br><div class="input-group"><div class="col-lg-4"><input type="text" class="form-control is-warning" name="feature_keys[`+x+`][name]" placeholder="Key Name" required=""></div><div class="col-lg-3"><div class="custom-control custom-switch"><input type="checkbox" name="feature_keys[`+x+`][for_front_end]" class="custom-control-input" id="`+id+`"><label class="custom-control-label" for="`+id+`">FOR FRONT END</label></div></div><div class="col-lg-2"><span class="btn btn-danger delete_icon">Delete - </span></div></div></div>`);
      });
   
      $(wrapper).on("click",".delete_icon", function(e){
          e.preventDefault(); $(this).parent('div').parent('div').parent('div').remove(); x--;
      })
});

</script>
@endsection