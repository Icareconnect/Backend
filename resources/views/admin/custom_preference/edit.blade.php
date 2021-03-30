@extends('layouts.vertical', ['title' =>  __('text.Custom Master Preference') ])
<?php
  $options = "";
  foreach ($masterPreference->FilterTypeOption as $key => $option) {
    $options .="<option value=".$option->id.">$option->option_name</option>";
  }
 ?>
@section('css')
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />

@endsection
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
              <h3>Edit {{  __('text.Custom Master Preference') }}</h3>
            </div>

            <div class="card-body">
              <form action="{{ route('duties.update',[$masterPreference->id])}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input name="type"  type="hidden" value="duty">
                <input name="multiselect"  type="hidden" value="1">
                <input type="hidden" name="_method" value="PUT">
                <div class="form-group row">
                    <div class="col-sm-4">
                      <label>Title</label>
                      <input  type="text" name="preference_name" value="{{ $masterPreference->name }}" class="form-control" placeholder="Title">
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
                        @foreach($masterPreference->filter_option as $id=>$filter_value)
                        <?php $f_ids = $filter_value->filterDuty->pluck('module_id')->all(); ?>
                          <div>
                            <br>
                            <div class="input-group">
                                  <input type="text" class="form-control is-warning" name="filter_option[name][{{ $filter_value->id }}]" placeholder=" Option" value="{{ $filter_value->name }}" required="">
                                   <input type="file" class="form-control" name="filter_option[image][{{ $filter_value->id }}]">
                                   <!-- <img src="{{ asset('/storage/thumbs/'.$filter_value->image) }}" id="profile-img-tag-icon" height="50" width="50" /> -->
                                   <img src="{{ Storage::disk('spaces')->url('thumbs/'.$filter_value->image) }}" id="profile-img-tag-icon" height="50" width="50" />
                                    <select  class="form-control" name="filter_option[category_filter_option][{{ $filter_value->id }}][]" multiple="" required="">
                                      @foreach($masterPreference->FilterTypeOption as $FilterTypeOption)
                                      <option value="{{ $FilterTypeOption->id }}" {{ in_array($FilterTypeOption->id,$f_ids)?"selected":"" }}>{{ $FilterTypeOption->option_name }}</option>
                                      @endforeach
                                    </select>
                                   <div>
                                    <span class="btn btn-danger delete_icon" data-filtertypeoption_id="{{ $filter_value->id }}">Delete - </span>
                                  </div>
                            </div>
                          </div>
                        @endforeach
                      </div>
                      <br>
                      <span class="btn btn-primary add_more_option">Add More +</span>
                       @if ($errors->has('filter_option'))
                        <span class="text-danger">{{ $errors->first('filter_option') }}</span>
                      @endif
                  </div>
                </div>
              </div>
                <div class="form-group">
                  <button type="submit" class="btn btn-primary">Update</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    </div>
@endsection
@section('script')
<script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function() {
      var wrapper         = $(".wrapper_class");
      var add_button      = $(".add_more_option");
      var options = "{!! $options !!}";
      var x = 1;
      $(add_button).click(function(e){
          e.preventDefault();
          x++;
          $(wrapper).append('<div><br><div class="input-group"><input type="text" class="form-control is-warning" name="new_option[name]['+x+']" required="" placeholder="Option"><input type="file" class="form-control" name="new_option[image]['+x+']"> <select  class="form-control" name="new_option[category_filter_option]['+x+'][]" multiple="" required="">'+options+'</select> <div><span class="btn btn-danger delete_icon_new">Delete - </span></div></div></div>');
      });
   
      $(wrapper).on("click",".delete_icon", function(e){
        var __this = this;
        var filtertypeoption_id = $(this).attr('data-filtertypeoption_id');
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
                 type:'POST',
                 url:base_url+'/admin/delete_master_option',
                 data:{filtertypeoption_id:filtertypeoption_id},
                 success:function(data){
                    if(data.status=='success'){
                        Swal.fire(
                          'Deleted!',
                          'Option has been deleted.',
                          'success'
                        ).then((result)=>{
                            e.preventDefault(); $(__this).parent('div').parent('div').remove();
                        });
                    }else{
                      Swal.fire(
                          'Error!',data.message,'error'
                        ).then((result)=>{

                        });
                    }
                 }
              });
            }
        });
      });
      $(wrapper).on("click",".delete_icon_new", function(e){
        e.preventDefault(); $(this).parent('div').parent('div').remove();x--;
      });
});

</script>
@endsection