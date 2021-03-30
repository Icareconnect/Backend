@extends('layouts.vertical', ['title' =>  __('text.Symptom') ])

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
              <h3>Edit {{  __('text.Symptom') }}</h3>
            </div>

            <div class="card-body">
              <form action="{{ route('symptoms.update',[$masterPreference->id])}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="form-group row">
                  <div class="col-sm-4">
                      <label>Is Multi-Select</label>
                        <select  class="form-control" name="multiselect">
                          <option value="">--Select Status--</option>
                          <option value="1" <?php echo (old('multiselect') ?? $masterPreference->is_multi ==1)?"selected":'' ?>>True</option>
                          <option value="0" <?php echo (old('multiselect') ?? $masterPreference->is_multi ==0)?"selected":'' ?>>False</option>
                        </select>
                        @if ($errors->has('multiselect'))
                          <span class="text-danger">{{ $errors->first('multiselect') }}</span>
                        @endif
                    </div>
                    <div class="col-sm-4">
                      <label>{{  __('text.Symptom') }} Name</label>
                      <input  type="text" name="symptom_name" value="{{ $masterPreference->name }}" class="form-control" placeholder="{{  __('text.Symptom') }} Name">
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
                            <img src="{{ Storage::disk('spaces')->url('thumbs/'.$masterPreference->image) }}" id="profile-img-tag" width="200px" />
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
                        @foreach($masterPreference->filter_option as $id=>$filter_value)
                          <div>
                            <br>
                            <div class="input-group">
                                  <input type="text" class="form-control is-warning" name="filter_option[name][{{ $filter_value->id }}]" placeholder=" Option" value="{{ $filter_value->name }}" required="">
                                  <textarea class="form-control is-warning" name="filter_option[description][{{ $filter_value->id }}]" placeholder="Description">{{ $filter_value->description }}</textarea>
                                   <input type="file" class="form-control" name="filter_option[image][{{ $filter_value->id }}]">
                                   <!-- <img src="{{ asset('/storage/thumbs/'.$filter_value->image) }}" id="profile-img-tag-icon" height="50" width="50" /> -->
                                   <img src="{{ Storage::disk('spaces')->url('thumbs/'.$filter_value->image) }}" id="profile-img-tag-icon" height="50" width="50" />
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

      $(add_button).click(function(e){
          e.preventDefault();
          $(wrapper).append('<div><br><div class="input-group"><input type="text" class="form-control is-warning" name="new_option[name][]" required="" placeholder="Option"><textarea class="form-control is-warning" name="new_option[description][]" placeholder="Description"></textarea><input type="file" class="form-control" name="new_option[image][]"> <div><span class="btn btn-danger delete_icon_new">Delete - </span></div></div></div>');
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
                 url:base_url+'/admin/delete_symptoms_option',
                 data:{filtertypeoption_id:filtertypeoption_id},
                 success:function(data){
                    if(data.status=='success'){
                        Swal.fire(
                          'Deleted!',
                          'Symptom Option has been deleted.',
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
        e.preventDefault(); $(this).parent('div').parent('div').remove();
      });
});

</script>
@endsection