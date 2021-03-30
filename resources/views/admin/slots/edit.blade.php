@extends('layouts.vertical', ['title' => 'Create Slots'])
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3>Add Slots</h3>
            </div>

            <div class="card-body">
              <form action="{{ route('slots.update', ['slot' => $slot->id]) }}" method="POST">
                 <input type="hidden" name="_token" value="{{ csrf_token() }}">
                 <input type="hidden" name="_method" value="PUT">
              <div class="form-group">
                  <div class="row">
                    <div class="col-sm-6">
                      <label for="">Select Slots *</label>
                           <select type="text" class="form-control" name="slot_value">
                              <option value="0">Please Choose Slots</option>
                              {{$slot->slot_value}}
                              <option value="15"
                              @if( $slot->slot_value == 15 )
                                selected="selected";
                              @endif >15 min</option>

                              <option value="30"
                              @if( $slot->slot_value == 30 )
                               selected="selected";
                              @endif>30 min</option>

                              <option value="45"
                              @if( $slot->slot_value == 45 )
                                selected="selected";
                              @endif>45 min</option>

                              <option value="1"
                              @if( $slot->slot_value == 1 )
                               selected="selected";
                              @endif>1 hour</option>
                           </select>
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
@endsection