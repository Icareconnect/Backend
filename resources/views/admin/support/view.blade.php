@extends('layouts.vertical', ['title' => 'View Question'])

@section('content')
<div class="card card-primary">
  <div class="card-header">
      <h4 class="card-title">View Question</h4>
    </div>
    <form role="form" action="{{ url('admin/support_questions/reply').'/'.$question->id}}" method="post" enctype="multipart/form-data">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="post">
        <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
          <div class="form-group row">
              <div class="col-sm-6">
                <label for="">Title</label><br>
                <input type="text" class="form-control"  placeholder="Title" value="{{ $question->title }}" readonly="" disabled>
                @if ($errors->has('title'))
                        <span class="text-danger">{{ $errors->first('title') }}</span>
                @endif
              </div>
              <div class="col-sm-6">
                <label for="">Created By</label><br>
                <input type="text" class="form-control"  placeholder="Title" value="{{ $question->created_by->name }}" readonly="">
              </div>
          </div> 
          <div class="form-group row">
              <div class="col-sm-6">
                <label for="page_body__">Question Description</label><br>
                <textarea readonly class="form-control" rows="6" column="6"  id="page_body__" placeholder="Question Description" disabled>{{ $question->description }}</textarea>
              </div>
              <div class="col-sm-6">
                <label for="">Status</label><br>
                <input type="text" class="form-control"  placeholder="Status" value="{{ $question->status }}" readonly="" disabled>
              </div>
          </div>
          <div class="form-group row">
              <div class="col-sm-6">
                <label for="page_body__">Answer</label><br>
                <textarea name="answer" class="form-control" rows="6" column="6"  id="page_body__" placeholder="Answer" disabled="">{{ (isset($question->answers[0])?$question->answers[0]['answer']:'') }}</textarea>
              </div>
              <div class="col-sm-6">
                <label for="">Type</label><br>
                <input type="text" class="form-control"  placeholder="Type" value="{{ ($question->amount)?'₹'.$question->amount.' Premium':'Free' }}" readonly="">
              </div>
          </div>
    </form>
  </div>
@endsection