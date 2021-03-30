@extends('vendor.intely.layouts.index', ['title' => 'Nurses','after_signup'=>true])

@section('content')
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="row mb-4">
        <div class="col d-flex align-items-center justify-content-between">
          <h1 class="mb-0">Jobs</h1>
          <a class="job-btn text-center" href="javascriptvoid:(0)" data-toggle="modal" data-target="#jobs">Add new job</a>
        </div>
      </div>
      
      <div class="nurses-content mt-4">
        <div class="row">
          <div class="col">
            <div class="outer-wrapper">
              <table class="jobs-data">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Job Title</th>
                    <th>Job Description</th>
                    <th>Location</th>
                    <th>Start Date</th>
                    <th>Due Date</th>
                    <th>Timings</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>abcxyz</td>
                    <td>healthcare job </td>
                    <td>Toronto</td>
                    <td>28 June, 2020</td>
                    <td>28 June, 2020</td>
                    <td>04:30 pm</td>
                    <td><a class="day-text new" href="#">New</a></td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>abcxyz</td>
                    <td>healthcare job </td>
                    <td>Toronto</td>
                    <td>28 June, 2020</td>
                    <td>28 June, 2020</td>
                    <td>04:30 pm</td>
                    <td><a class="day-text Fulfilled" href="#">Fulfilled</a></td>
                  </tr>
                  <tr>
                    <td>3</td>
                    <td>abcxyz</td>
                    <td>healthcare job </td>
                    <td>Toronto</td>
                    <td>28 June, 2020</td>
                    <td>28 June, 2020</td>
                    <td>04:30 pm</td>
                    <td><a class="day-text in_progress" href="#">In Progress</a></td>
                  </tr>
                  <tr>
                    <td>4</td>
                    <td>abcxyz</td>
                    <td>healthcare job </td>
                    <td>Toronto</td>
                    <td>28 June, 2020</td>
                    <td>28 June, 2020</td>
                    <td>04:30 pm</td>
                    <td><a class="day-text fulfilled" href="#">Partially fulfilled</a></td>
                  </tr>
                  <tr>
                    <td>5</td>
                    <td>abcxyz</td>
                    <td>healthcare job </td>
                    <td>Toronto</td>
                    <td>28 June, 2020</td>
                    <td>28 June, 2020</td>
                    <td>04:30 pm</td>
                    <td><a class="day-text pending" href="#">Pending</a></td>
                  </tr>
                  <tr>
                    <td>6</td>
                    <td>abcxyz</td>
                    <td>healthcare job </td>
                    <td>Toronto</td>
                    <td>28 June, 2020</td>
                    <td>28 June, 2020</td>
                    <td>04:30 pm</td>
                    <td><a class="day-text in_progress" href="#">In Progress</a></td>
                  </tr>
                  <tr>
                    <td>7</td>
                    <td>abcxyz</td>
                    <td>healthcare job </td>
                    <td>Toronto</td>
                    <td>28 June, 2020</td>
                    <td>28 June, 2020</td>
                    <td>04:30 pm</td>
                    <td><a class="day-text pending" href="#">Pending</a></td>
                  </tr>
                  <tr>
                    <td>8</td>
                    <td>abcxyz</td>
                    <td>healthcare job </td>
                    <td>Toronto</td>
                    <td>28 June, 2020</td>
                    <td>28 June, 2020</td>
                    <td>04:30 pm</td>
                    <td><a class="day-text pending" href="#">Pending</a></td>
                  </tr>
                  <tr>
                    <td>9</td>
                    <td>abcxyz</td>
                    <td>healthcare job </td>
                    <td>Toronto</td>
                    <td>28 June, 2020</td>
                    <td>28 June, 2020</td>
                    <td>04:30 pm</td>
                    <td><a class="day-text new" href="#">New</a></td>
                  </tr>
                  <tr>
                    <td>10</td>
                    <td>abcxyz</td>
                    <td>healthcare job </td>
                    <td>Toronto</td>
                    <td>28 June, 2020</td>
                    <td>28 June, 2020</td>
                    <td>04:30 pm</td>
                    <td><a class="day-text fulfilled" href="#">Fulfilled</a></td>
                  </tr>
                  <tr>
                    <td>11</td>
                    <td>abcxyz</td>
                    <td>healthcare job </td>
                    <td>Toronto</td>
                    <td>28 June, 2020</td>
                    <td>28 June, 2020</td>
                    <td>04:30 pm</td>
                    <td><a class="day-text in_progress" href="#">In Progress</a></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
    <!-- /.container-fluid-->
    
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fa fa-angle-up"></i>
    </a>
  </div>

  


  <!-- Jobs Modal -->
  <div class="modal fade" id="jobs" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header border-0 p-0">
          <a href="javascript:void(0)" type="" class="close position-absolute" data-dismiss="modal" aria-label="Close">
            <img src="images/ic_close.svg" alt="">
          </a>
        </div>
        <div class="modal-body border-0 p-0">
          <h4 class="text-center">Job Opening form</h4>
          <form class="jobs_form mt-lg-5 mt-4">
            <div class="form-group">
              <div class="row row-spacing no-gutters">
                <div class="col">
                  <label>Job Title</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row row-spacing no-gutters">
                <div class="col">
                  <label>Job Description</label>
                  <textarea class="form-control" name="" id="" cols="30" rows="5"></textarea>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row row-spacing no-gutters">
                <div class="col">
                  <label>Qualification</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label>Experience</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
                <div class="col-md-6">
                  <label>Category</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label>Services required</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
                <div class="col-md-6">
                  <label> No. of openings</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label>Location</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
                <div class="col-md-6">
                  <label>Urgency</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label>Start Date</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
                <div class="col-md-6">
                  <label>Duration</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label>Timings</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
                <div class="col-md-6">
                  <label>Due date</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col">
                  <label>Preferences</label>
                  <input class="form-control" type="" name="" id="" placeholder="">
                </div>
              </div>
            </div>

            <div class="form-group mt-lg-5 mt-4 text-center">
              <button class="btn form-btn"><span>Submit</span></button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
@endsection