@extends('layouts.vertical', ['title' => __('Additional Documents')])

@section('css')
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/selectize/selectize.min.css')}}" rel="stylesheet" type="text/css" />

@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3>Add {{ __('Additional Document')}}</h3>
            </div>

            <div class="card-body">
              <form action="{{ route('additional-document.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                  <div class="col-sm-4">
                    <label>Name</label>
                    <input type="text" required="" name="name" class="form-control" value="{{ old('name') }}" placeholder="Name">
                    @if ($errors->has('name'))
                      <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                  </div>
                  <div class="col-sm-4">
                      <label>Is Enable</label>
                        <select  class="form-control" name="is_enable">
                          <option value="1" <?php echo (old('is_enable')=='1')?"selected":'' ?>>True</option>
                          <option value="0" <?php echo (old('is_enable')=='0')?"selected":'' ?>>False</option>
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
                        <option value="text" <?php echo (old('field_type')=='text')?"selected":'' ?>>Text</option>
                        <option value="file" <?php echo (old('field_type')=='file')?"selected":'' ?>>File</option>
                        <option value="date" <?php echo (old('field_type')=='date')?"selected":'' ?>>Date</option>
                      </select>
                      @if ($errors->has('field_type'))
                        <span class="text-danger">{{ $errors->first('field_type') }}</span>
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
          $(wrapper).append('<div><br><div class="input-group"><input type="text" class="form-control is-warning" name="filter_option[]" required="" placeholder="Filter Option"><span class="btn btn-danger delete_icon">Delete - </span></div></div>');
      });
   
      $(wrapper).on("click",".delete_icon", function(e){
          e.preventDefault(); $(this).parent('div').parent('div').remove(); x--;
      })
});

</script>
<!-- <script type="text/javascript">
  $(function () {
  $('.selectize-close-btn').selectize({
            plugins: ['remove_button'],
            persist: false,
            create: true,
            render: {
                item: function(data, escape) {
                    return '<div>"' + escape((data.text)) + '"</div>';
                }
            },
            onDelete: function(values) {
                return confirm(values.length > 1 ? 'Are you sure you want to remove these ' + values.length + ' items?' : 'Are you sure you want to remove "' + values[0] + '"?');
            }
        });
});
</script> -->
@endsection