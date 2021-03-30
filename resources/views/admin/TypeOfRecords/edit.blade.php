@extends('layouts.vertical', ['title' => 'Create TypeOfRecords'])
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3>Add TypeOfRecords</h3>
            </div>

            <div class="card-body">
              <form action="{{ route('typeOfRecord.update', ['typeOfRecord' => $slot->id]) }}" method="POST">
                 <input type="hidden" name="_token" value="{{ csrf_token() }}">
                 <input type="hidden" name="_method" value="PUT">
                <div class="form-group">
                    <div class="row">
                      <div class="col-sm-6">
                        <input type="text" name="records_value" value="{{$slot->records_value}}" class="form-control" placeholder="Enter Records Value">
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