@extends('layouts.vertical', ['title' => __('Additional Document')])

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
              <h3>Edit {{ __('Additional Document')}}</h3>
            </div>

            <div class="card-body">
              <form action="{{ url('admin/additional-document/update')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="form-group row">
                  <div class="col-sm-4">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name')?? $filterType->name }}" placeholder="Name">
                    @if ($errors->has('name'))
                      <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                  </div>
                  <div class="col-sm-4">
                      <label>Is Enable</label>
                        <select  class="form-control" name="is_enable">
                          <option value="">--Select Status--</option>
                          <option value="1" <?php echo (old('is_enable') ?? $filterType->is_enable =='1')?"selected":'' ?>>True</option>
                          <option value="0" <?php echo (old('is_enable') ?? $filterType->is_enable =='0')?"selected":'' ?>>False</option>
                        </select>
                        @if ($errors->has('is_enable'))
                          <span class="text-danger">{{ $errors->first('is_enable') }}</span>
                        @endif
                    </div>
                </div>
                 <div class="form-group">
                  <div class="row">
                  
                  <div class="col-sm-4">
                    <label>Field Type</label>
                      <select  class="form-control" name="field_type">
                        <option value="text" <?php echo (old('field_type')?? $filterType->type=='text')?"selected":'' ?>>Text</option>
                        <option value="file" <?php echo (old('field_type')?? $filterType->type=='file')?"selected":'' ?>>File</option>
                        <option value="date" <?php echo (old('field_type')?? $filterType->type=='date')?"selected":'' ?>>Date</option>
                      </select>
                      @if ($errors->has('field_type'))
                        <span class="text-danger">{{ $errors->first('field_type') }}</span>
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
          $(wrapper).append('<div><br><div class="input-group"><input type="text" class="form-control is-warning" name="new_option[]" required="" placeholder="Filter Option"><span class="btn btn-danger delete_icon_new">Delete - </span></div></div>');
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
                 url:base_url+'/admin/delete_filter_option',
                 data:{filtertypeoption_id:filtertypeoption_id},
                 success:function(data){
                    if(data.status=='success'){
                        Swal.fire(
                          'Deleted!',
                          'Filter has been deleted.',
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