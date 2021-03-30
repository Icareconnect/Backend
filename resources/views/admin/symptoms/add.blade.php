@extends('layouts.vertical', ['title' => __('text.Symptom') ])

@section('css')
    <style type="text/css">
      .delete_icon{
        height: fit-content;
      }
    </style>
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/selectize/selectize.min.css')}}" rel="stylesheet" type="text/css" />

@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3>Add {{  __('text.Symptom') }}</h3>
            </div>

            <div class="card-body">
              <form action="{{ route('symptoms.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                  <div class="col-sm-4">
                      <label>Is Multi-Select</label>
                        <select  class="form-control" name="multiselect">
                          <option value="1" <?php echo (old('multiselect')=="1")?"selected":'' ?>>True</option>
                          <option value="0" <?php echo (old('multiselect')=="0")?"selected":'' ?>>False</option>
                        </select>
                        @if ($errors->has('multiselect'))
                          <span class="text-danger">{{ $errors->first('multiselect') }}</span>
                        @endif
                    </div>
                    <div class="col-sm-4">
                    <label>{{  __('text.Symptom') }} Name</label>
                    <input name="symptom_name" required="" type="text" value="{{ old('symptom_name') }}" class="form-control" placeholder="{{  __('text.Symptom') }} Name" >
                     @if ($errors->has('symptom_name'))
                      <span class="text-danger">{{ $errors->first('symptom_name') }}</span>
                    @endif
                  </div>
                </div>
              <div class="form-group">
               <div class="row">
                    <div class="col-sm-6">
                      <label for="exampleInputFile">{{  __('text.Symptom') }} Icon</label>
                      <div class="input-group">
                        <div >
                          <input type="file" value="{{old('icon') }}" name="icon" id="ct-img-file">
                          <img src="" id="profile-img-tag" width="200px" />
                        </div>
                      </div>
                       @if ($errors->has('icon'))
                                      <span class="text-danger">{{ $errors->first('icon') }}</span>
                        @endif
                    </div>
                </div>
              </div>
              <div class="form-group">
                 <div class="row">
                  <div class="col-sm-4">
                      <label>{{  __('text.Symptom') }} Options</label>
                      <div class="wrapper_class" style="width: 700px;">
                        <div>
                        <div class="input-group">
                              <input type="text" class="form-control is-warning" name="filter_option[name][]" placeholder="Option" required="">
                              <textarea class="form-control is-warning" name="filter_option[description][]" placeholder="Description"></textarea>
                              <input type="file" class="form-control" name="filter_option[image][]">
                              <span class="btn btn-danger delete_icon">Delete - </span>
                        </div>
                        </div>
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
      $(add_button).click(function(e){
          e.preventDefault();
          x++;
          $(wrapper).append('<div><br><div class="input-group"><input type="text" class="form-control is-warning" name="filter_option[name][]" required="" placeholder="Filter Option"><textarea class="form-control is-warning" name="filter_option[description][]" placeholder="Description"></textarea><input type="file" class="form-control" name="filter_option[image][]"><span class="btn btn-danger delete_icon">Delete - </span></div></div>');
      });
   
      $(wrapper).on("click",".delete_icon", function(e){
          e.preventDefault(); $(this).parent('div').parent('div').remove(); x--;
      })
});

</script>
@endsection