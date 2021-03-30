@extends('layouts.vertical', ['title' => 'Create Slots'])
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3>Add Slots</h3>
            </div>

            <div class="card-body">
              <form action="{{ route('slots.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                 
              <div class="form-group">
                  <div class="row">
                    <div class="col-sm-6">
                      <label for="">Select Slots*</label>
                           <select type="text" class="form-control" name="slot_value">
                              <option value="0">Please Choose Slots</option>
                              <option value="15">15 min</option>
                              <option value="30">30 min</option>
                              <option value="45">45 min</option>
                              <option value="1">1 hour</option>
                           </select>
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
<script>
   var msg = '{{Session::get('alert')}}';
   var exist = '{{Session::has('alert')}}';
   if(exist){
     alert(msg);
   }
</script>
@endsection