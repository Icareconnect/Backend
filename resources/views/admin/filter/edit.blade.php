@extends('layouts.vertical', ['title' => 'Filter Type'])

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
              <h3>Edit Filter Type</h3>
            </div>

            <div class="card-body">
              <form action="{{ route('filters.update',[$category->id,$filterType->id])}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="form-group row">
                  <div class="col-sm-4">
                      <label>Category Name</label>
                      <input type="text" disabled="" placeholder="Selected Category" class="form-control" id="category_selected" value="{{ $category->name }}" type="text" readonly="">
                      <input  type="hidden" value="{{ $category->id }}" id="last_action" name="category" type="text" readonly="">
                      @if ($errors->has('category'))
                        <span class="text-danger">{{ $errors->first('category') }}</span>
                      @endif
                  </div>
                  <div class="col-sm-4">
                      <label>Is Multi-Select</label>
                        <select  class="form-control" name="multiselect">
                          <option value="">--Select Status--</option>
                          <option value="1" <?php echo (old('multiselect') ?? $filterType->is_multi =='1')?"selected":'' ?>>True</option>
                          <option value="0" <?php echo (old('multiselect') ?? $filterType->is_multi =='0')?"selected":'' ?>>False</option>
                        </select>
                        @if ($errors->has('multiselect'))
                          <span class="text-danger">{{ $errors->first('multiselect') }}</span>
                        @endif
                    </div>
                </div>
                 <div class="form-group">
                  <div class="row">
                  <div class="col-sm-4">
                    <label>Filter Name</label>
                    <input type="text" name="filter_name" class="form-control" value="{{ old('filter_name')?? $filterType->filter_name }}" placeholder="Filter Name">
                    @if ($errors->has('filter_name'))
                      <span class="text-danger">{{ $errors->first('filter_name') }}</span>
                    @endif
                  </div>
                  <div class="col-sm-4">
                    <label>Preference Name</label>
                    <input  type="text" name="preference_name" value="{{ $filterType->preference_name }}" class="form-control" placeholder="Preference Name">
                     @if ($errors->has('preference_name'))
                      <span class="text-danger">{{ $errors->first('preference_name') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              @php $show_price = false; @endphp
              @if(Config('client_connected') && Config::get("client_data")->domain_name=="intely")
                @php $show_price = false; @endphp
              @endif
              <div class="form-group">
                 <div class="row">
                  <div class="col-sm-11">
                      <label>Filter Options</label>
                      <div class="wrapper_class">
                        @foreach($filterType->filter_option as $id=>$filter_value)
                          <div>
                            <br>
                            <div class="input-group">
                                  <input type="text" class="form-control is-warning" name="filter_option[name][{{ $filter_value->id }}]" placeholder="Filter Option" value="{{ $filter_value->option_name }}" required="">
                                  <textarea  class="form-control is-warning" name="filter_option[description][{{ $filter_value->id }}]" placeholder="Address/Description">{{ $filter_value->description }}</textarea>
                                   @if(Config('client_connected') && Config::get("client_data")->domain_name=="intely")
                                   <input type="number" placeholder="Price" class="form-control" name="filter_option[price][{{ $filter_value->id }}]" value="{{ $filter_value->price }}">
                                   @endif
                                   <input type="file" class="form-control" name="filter_option[image][{{ $filter_value->id }}]" >
                                   <!-- <img src="{{ asset('/storage/thumbs/'.$filter_value->image) }}" id="profile-img-tag-icon" height="50" width="50" /> -->
                                   <img src="{{ Storage::disk('spaces')->url('thumbs/'.$filter_value->image) }}" id="profile-img-tag-icon" height="50" width="50" />
                                   <div>
                                    <span class="btn btn-danger delete_icon" data-filtertypeoption_id="{{ $filter_value->id }}">Delete - </span>
                                    @if(Config('client_connected') && Config::get("client_data")->domain_name=="physiotherapist")
                                      <a target="__blank" href="{{ url('admin/filter_option/update').'/'.$filter_value->id}}" class="btn btn-info">Add Info</a>
                                   @endif
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
      var show_price = "{{ $show_price }}";
      var price = '';
      if(show_price){
        price = '<input type="number" placeholder="Price" class="form-control" name="new_option[price][]">';
      }
      $(add_button).click(function(e){
          e.preventDefault();
          $(wrapper).append('<div><br><div class="input-group"><input type="text" class="form-control is-warning" name="new_option[name][]" required="" placeholder="Filter Option"><textarea  class="form-control is-warning" name="new_option[description][]" placeholder="Address/Description"></textarea>'+price+'<input type="file" class="form-control" name="new_option[image][]"> <div><img src="" id="profile-img-tag-icon" height="50" width="50" /><span class="btn btn-danger delete_icon_new">Delete - </span></div></div></div>');
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