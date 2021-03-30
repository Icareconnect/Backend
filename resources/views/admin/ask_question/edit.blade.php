@extends('layouts.vertical', ['title' => 'Edit Ask Question'])

@section('content')
<div class="card card-primary">
  <div class="card-header">
      <h3 class="card-title">Edit Ask Question</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ url('admin/ask_question').'/'.$feed->id}}" method="post" enctype="multipart/form-data">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="PUT">
        <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
          <div class="form-group row">
              <div class="col-sm-6">
                <label for="">Question</label><br>
                <input type="text" class="form-control"  name="question"  placeholder="Question" required="" value="{{ old('question')??$feed->title }}">
                @if ($errors->has('question'))
                        <span class="text-danger">{{ $errors->first('question') }}</span>
                @endif
              </div>
          </div> 
          <div class="form-group row">
              <div class="col-sm-6">
                <label for="page_body__">Answer</label><br>
                <textarea class="form-control" rows="6" column="6"  name="answer"  id="page_body__" placeholder="Place some text here" >{{{old('answer')??$feed->description }}}</textarea>
                @if ($errors->has('answer'))
                        <span class="text-danger">{{ $errors->first('answer') }}</span>
                @endif
              </div>
          </div>
          <div class="form-group row">
              <div class="col-sm-6">
                <label for="exampleInputFile">Image Web</label>
                <div class="input-group">
                  <div >
                    <input type="file" value="{{old('image') }}" name="image" id="ct-img-file">
                    <img src="{{ Storage::disk('spaces')->url('thumbs/'.$feed->image) }}" id="profile-img-tag" width="200px" />
                  </div>
                </div>
                 @if ($errors->has('image'))
                                <span class="text-danger">{{ $errors->first('image') }}</span>
                  @endif
              </div>
          </div>
        <!-- /.card-body -->
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
  </div>
@endsection