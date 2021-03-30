@extends('vendor.intely.layouts.index', ['title' => 'Dashboard','after_signup'=>true])

@section('content')
<div class="content-wrapper">
  <div class="container-fluid">
    <h1 class="mb-4">Dashboard</h1>
    <div class="row col-spacing">
      <div class="col-lg-5">
        <div class="dashboard-box bg-white">
         <div class="p-3">
          <h6>Recently added Jobs</h6>
          <p class="no-text">2,547</p>
        </div>
          <ul class="recent-list d-flex align-items-center justify-content-center">
            <li>
              <label class="d-block mb-0">This Month</label>
              <span class="d-block">1500</span>
            </li>
            <li>
              <label class="d-block mb-0">This Month</label>
              <span class="d-block">1500</span>
            </li>
          </ul>
        </div>
      </div>
      <div class="col-lg-3">
        <div class="dashboard-box bg-white">
         <div class="p-3">
            <h6>Partially fulfilled Jobs </h6>
            <p class="no-text">1,489</p>
          </div>
          <ul class="recent-list d-flex align-items-center justify-content-center">
            <li>
              <label class="d-block mb-0">This Month</label>
              <span class="d-block">1500</span>
            </li>
            <li>
              <label class="d-block mb-0">This Month</label>
              <span class="d-block">1500</span>
            </li>
          </ul>
        </div>
      </div>
      <div class="col-lg-2">
        <div class="dashboard-box job-box bg-white h-100 p-3">
          <a class="day-text" href="#">Today</a>
          <h6>High urgency Jobs</h6>
          <p>19</p>
          <a class="see-all" href="#">See all</a>
        </div>
      </div>
      <div class="col-lg-2">
        <div class="dashboard-box job-box bg-white h-100 p-3">
          <a class="day-text active-text" href="#">Active</a>
          <h6>Inprogress Jobs</h6>
          <p>20</p>
          <a class="see-all" href="#">See all</a>
        </div>
      </div>
    </div>

    <div class="nurses-content mt-4">
      <div class="row">
        <div class="col">
          <div class="nurses-header d-flex justify-content-between align-items-center">
            <h6 class="text-16">Nurses</h6>
            <a class="view-btn" href="">View Alll</a>
          </div>
          <div class="outer-wrapper">
            <table class="nurses-data">
              <thead>
                <tr>
                  <th>Sr No.</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone Number</th>
                  <th>Resume</th>
                  <th>document</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Jackson Wilkerson</td>
                  <td>jacksonwilkerson@gmail.com</td>
                  <td>+971 65 452 3789</td>
                  <td><img src="{{ asset('assets/intely/images/img01.png')}}" alt=""></td>
                  <td><img src="{{ asset('assets/intely/images/img02.png')}}" alt=""></td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>Bobby Herrera</td>
                  <td>jacksonwilkerson@gmail.com</td>
                  <td>+971 65 452 3789</td>
                  <td><img src="{{ asset('assets/intely/images/img01.png')}}" alt=""></td>
                  <td><img src="{{ asset('assets/intely/images/img02.png')}}" alt=""></td>
                </tr>
                <tr>
                  <td>3</td>
                  <td>Elmer Curtis</td>
                  <td>jacksonwilkerson@gmail.com</td>
                  <td>+971 65 452 3789</td>
                  <td><img src="{{ asset('assets/intely/images/img01.png')}}" alt=""></td>
                  <td><img src="{{ asset('assets/intely/images/img02.png')}}" alt=""></td>
                </tr>
                <tr>
                  <td>4</td>
                  <td>Johnny Thompson</td>
                  <td>jacksonwilkerson@gmail.com</td>
                  <td>+971 65 452 3789</td>
                  <td><img src="{{ asset('assets/intely/images/img01.png')}}" alt=""></td>
                  <td><img src="{{ asset('assets/intely/images/img02.png')}}" alt=""></td>
                </tr>
                <tr>
                  <td>5</td>
                  <td>Blanche Vaughn</td>
                  <td>jacksonwilkerson@gmail.com</td>
                  <td>+971 65 452 3789</td>
                  <td><img src="{{ asset('assets/intely/images/img01.png')}}" alt=""></td>
                  <td><img src="{{ asset('assets/intely/images/img02.png')}}" alt=""></td>
                </tr>
                <tr>
                  <td>6</td>
                  <td>Elnora Diaz</td>
                  <td>jacksonwilkerson@gmail.com</td>
                  <td>+971 65 452 3789</td>
                  <td><img src="{{ asset('assets/intely/images/img01.png')}}" alt=""></td>
                  <td><img src="{{ asset('assets/intely/images/img02.png')}}" alt=""></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection