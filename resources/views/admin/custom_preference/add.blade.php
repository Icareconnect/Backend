@extends('layouts.vertical', ['title' => __('text.Custom Master Preference') ])
<?php
  $options = "";
  foreach ($FilterTypeOption as $key => $option) {
    $options .="<option value=".$option->id.">$option->option_name</option>";
  }
 ?>
@section('css')
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/selectize/selectize.min.css')}}" rel="stylesheet" type="text/css" />

@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3>Add {{  __('text.Custom Master Preference') }}</h3>
            </div>

            <div class="card-body">
              <form action="{{ route('duties.store') }}" method="POST" enctype="multipart/form-data">
                 <input name="type"  type="hidden" value="duty">
                 <input name="multiselect"  type="hidden" value="1">
                @csrf
                <div class="form-group row">
                    <div class="col-sm-4">
                    <label>Title</label>
                    <input name="preference_name" required="" type="text" value="{{ old('preference_name') }}" class="form-control" placeholder="Title" >
                     @if ($errors->has('preference_name'))
                      <span class="text-danger">{{ $errors->first('preference_name') }}</span>
                    @endif
                  </div>
                </div>
              <div class="form-group">
                 <div class="row">
                  <div class="col-sm-4">
                      <label>Options</label>
                      <div class="wrapper_class" style="width: 700px;">
                        <!-- <div>
                        <div class="input-group" style="align-items:end;">
                              <input type="text" class="form-control is-warning" name="filter_option[name][]" placeholder="Option" required="">
                              <input type="file" class="form-control" name="filter_option[image][]">
                              <select  class="form-control" name="filter_option[category_filter_option][]" multiple="" required="">
                                  {!! $options !!}
                              </select>
                                <span class="btn btn-danger delete_icon">Delete - </span>
                        </div>
                        </div> -->
                      </div>
                      <span class="btn btn-primary add_more_option">Add More +</span>
                       @if ($errors->has('filter_option'))
                        <span class="text-danger">{{ $errors->first('filter_option') }}</span>
                      @endif
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
@endsection

@section('script')

<script type="text/javascript">
  $(document).ready(function() {
      var wrapper         = $(".wrapper_class");
      var add_button      = $(".add_more_option");
      var x = 1;
      var options = "{!! $options !!}";
      $(wrapper).append('<div><br><div class="input-group" style="align-items:end;"><input type="text" class="form-control is-warning" name="filter_option[name]['+x+']" required="" placeholder="Filter Option"><input type="file" class="form-control" name="filter_option[image]['+x+']"><select  class="form-control" name="filter_option[category_filter_option]['+x+'][]" multiple="" required="">'+options+'</select><span class="btn btn-danger delete_icon">Delete - </span></div></div>');
      $(add_button).click(function(e){
          e.preventDefault();
          x++;
          $(wrapper).append('<div><br><div class="input-group" style="align-items:end;"><input type="text" class="form-control is-warning" name="filter_option[name]['+x+']" required="" placeholder="Filter Option"><input type="file" class="form-control" name="filter_option[image]['+x+']"><select  class="form-control" name="filter_option[category_filter_option]['+x+'][]" multiple="" required="">'+options+'</select><span class="btn btn-danger delete_icon">Delete - </span></div></div>');
      });
   
      $(wrapper).on("click",".delete_icon", function(e){
          e.preventDefault(); $(this).parent('div').parent('div').remove(); x--;
      })
});

</script>
@endsection