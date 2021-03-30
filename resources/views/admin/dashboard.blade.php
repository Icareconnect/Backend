<?php
$category_permission = json_decode(Auth::user()->permission);
$permission = (isset($category_permission->module) && $category_permission->module=='category')?true:false;
$admin = Auth::user()->hasRole('admin');
$service_provider = Auth::user()->hasRole('service_provider');
$tx_dash = 'Consultants';
if(Config::get('client_connected') && Config::get("client_data")->domain_name=="mp2r" )
    $tx_dash = 'Professionals';
else if(config('client_connected') && Config::get("client_data")->domain_name=="intely")
    $tx_dash = 'Nurses';
?>
@extends('layouts.vertical', ['title' => 'Dashboard'])

@section('css')
    <!-- Plugins css -->
    <link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/libs/selectize/selectize.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
@if($admin)
    <!-- Start Content-->
    <div class="container-fluid">
    
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Dashboard</h4>
                </div>
            </div>
        </div>     
        <!-- end page title --> 

        <div class="row">
            <div class="col-md-4 col-xl-3">
                <div class="widget-rounded-circle card-box">
                    <a href="{{ url('admin/customers') }}">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                                    <i class="fe-users font-22 avatar-title text-primary"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-right">
                                    <h3 class="mt-1"><span data-plugin="counterup">{{ $userCount }}</span></h3>
                                    <p class="text-muted mb-1 text-truncate">Total {{ __('text.Users') }}</p>
                                </div>
                            </div>
                        </div> <!-- end row-->
                    </a>
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->

            <div class="col-md-4 col-xl-3">
                <div class="widget-rounded-circle card-box">
                    <a href="{{ url('admin/consultants') }}">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                                    <i class="fe-users font-22 avatar-title text-success"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-right">
                                    <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $vendorCount }}</span></h3>
                                    <p class="text-muted mb-1 text-truncate">Total {{ __('text.Vendors') }}</p>
                                </div>
                            </div>
                        </div> <!-- end row-->
                    </a>
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->

            <div class="col-md-4 col-xl-3">
                <div class="widget-rounded-circle card-box">
                    <a href="{{ url('admin/requests') }}">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-lg rounded-circle bg-soft-info border-info border">
                                    <i class="fas fa-comments font-22 avatar-title text-info"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-right">
                                    <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $total_req }}</span></h3>
                                    <p class="text-muted mb-1 text-truncate">Total Requests</p>
                                </div>
                            </div>
                        </div> <!-- end row-->
                    </a>
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->

        </div>
        <!-- end row-->
        <div class="row">
            <div class="col-lg-4">
                <div class="card-box">
                    <div class="dropdown float-right">
                    </div>
                    <h4 class="header-title mb-0">Total Revenue</h4>
                    @if(Config('client_connected') && Config::get("client_data")->domain_name=="physiotherapist")
                    <br>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                            <select name="centres" id="searchByCategory" class="form-control">
                                <option value="">--Select Filter--</option>
                                <optgroup label="Category">
                                  @foreach($categories as $cat_key=>$parentCategory)
                                  @if($parentCategory->is_filter)
                                    @foreach($parentCategory->filters as $filter)
                                    <option  value="{{ 'filter_'.$filter['data']['id'].'_category_'.$parentCategory->id }}">{{ $filter['data']['option_name'] }}</option>
                                    @endforeach
                                  @endif
                                  @endforeach
                                 </optgroup>
                                 <optgroup label="Centre">
                                  @foreach($categories as $cat_key=>$parentCategory)
                                  @if(!$parentCategory->is_filter && $parentCategory->parent_id)
                                    <option  value="{{ $parentCategory->id }}">{{ $parentCategory->name }}</option>
                                  @endif
                                  @endforeach
                                 </optgroup>
                            </select>
                          </div>
                        </div>
                    </div>
                    @endif
                    <div class="widget-chart text-center" dir="ltr">
                        <div id="chart" class="mt-0"  data-colors="#f1556c"></div>
                        <h5 class="text-muted mt-0">Total Over All Revenue</h5>
                        <h2 id="overAll">{{ $revenue['totalRevenue'] }}</h2>
                    </div>
                </div> <!-- end card-box -->
            </div> <!-- end col-->

            <div class="col-lg-8">
                <div class="card-box pb-2">
                    <div class="float-right d-none d-md-inline-block">
                        <div class="btn-group mb-2">
                            <button type="button" id="WeeklyClass" class="btn btn-xs btn-secondary">Weekly</button>
                            <button type="button" id="MonthlyClass" class="btn btn-xs btn-light">Monthly</button>
                        </div>
                    </div>

                    <h4 class="header-title mb-3">Sales Analytics</h4>
                    <!-- <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                            <label class="control-label" for="date_deb">Filter</label>
                            <select name="date_deb" id="date_deb" class="form-control">
                                <option value="07/11/2012">07/11/2012</option><option value="30/09/2012">30/09/2012</option>            </select>
                          </div>
                        </div>
                    </div> -->
                    <div dir="ltr" class="analyticsWeeklyClassToggle">
                        <div id="sales-analyticsWeeklyClass" class="mt-4" data-colors="#1abc9c,#4a81d4"></div>
                    </div>
                    <div dir="ltr" class="analyticsMonthlyClassToggle" style="display: none;">
                        <div id="sales-analyticsMonthlyClass" class="mt-4" data-colors="#1abc9c,#4a81d4"></div>
                    </div>
                </div> <!-- end card-box -->
            </div> <!-- end col-->
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-xl-6">
                <div class="card-box">
                    <div class="dropdown float-right">
                        <div class="dropdown-menu dropdown-menu-right">
                           
                        </div>
                    </div>

                    <h4 class="header-title mb-3">Recently Added {{ __('text.Vendors') }}</h4>

                    <div class="table-responsive">
                        <table class="table table-borderless table-hover table-nowrap table-centered m-0">
                            <thead class="thead-light">
                                <tr>
                                    <th colspan="2">Profile</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latest_sp as $key=>$sp)
                                <tr>
                                    <td style="width: 36px;">
                                      @if($sp->profile_image)
                                          <img src="{{ url('/').'/media/'.$sp->profile_image }}" alt="User Image"  class="rounded-circle avatar-sm">
                                      @else
                                          <img src="{{ url('/').'/default/user.jpg' }}" alt="User Image"  class="rounded-circle avatar-sm">
                                      @endif
                                    </td>

                                    <td>
                                        <h5 class="m-0 font-weight-normal">{{ ($sp->name)?$sp->name:'unknown' }}</h5>
                                        <p class="mb-0 text-muted"><small>Member Since {{ Carbon\Carbon::parse($sp->created_at)->diffForHumans() }}</small></p>
                                    </td>

                                    <td>
                                         {{ $sp->email }}
                                    </td>

                                    <td>
                                        {{ $sp->phone }}
                                    </td>

                                    <td>
                                    </td>

                                </tr>
                                @endforeach
                          

                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- end col -->
            @if(!config('client_connected'))
            <div class="col-xl-6">
                <div class="card-box">
                    <div class="dropdown float-right">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Edit Report</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Export Report</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Action</a>
                        </div>
                    </div>

                    <h4 class="header-title mb-3">Revenue History</h4>

                    <div class="table-responsive">
                        <table class="table table-borderless table-nowrap table-hover table-centered m-0">

                            <thead class="thead-light">
                                <tr>
                                    <th>Marketplaces</th>
                                    <th>Date</th>
                                    <th>Payouts</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <h5 class="m-0 font-weight-normal">Themes Market</h5>
                                    </td>

                                    <td>
                                        Oct 15, 2018
                                    </td>

                                    <td>
                                        $5848.68
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-warning text-warning">Upcoming</span>
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i class="mdi mdi-pencil"></i></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <h5 class="m-0 font-weight-normal">Freelance</h5>
                                    </td>

                                    <td>
                                        Oct 12, 2018
                                    </td>

                                    <td>
                                        $1247.25
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-success text-success">Paid</span>
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i class="mdi mdi-pencil"></i></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <h5 class="m-0 font-weight-normal">Share Holding</h5>
                                    </td>

                                    <td>
                                        Oct 10, 2018
                                    </td>

                                    <td>
                                        $815.89
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-success text-success">Paid</span>
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i class="mdi mdi-pencil"></i></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <h5 class="m-0 font-weight-normal">Envato's Affiliates</h5>
                                    </td>

                                    <td>
                                        Oct 03, 2018
                                    </td>

                                    <td>
                                        $248.75
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-danger text-danger">Overdue</span>
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i class="mdi mdi-pencil"></i></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <h5 class="m-0 font-weight-normal">Marketing Revenue</h5>
                                    </td>

                                    <td>
                                        Sep 21, 2018
                                    </td>

                                    <td>
                                        $978.21
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-warning text-warning">Upcoming</span>
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i class="mdi mdi-pencil"></i></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <h5 class="m-0 font-weight-normal">Advertise Revenue</h5>
                                    </td>

                                    <td>
                                        Sep 15, 2018
                                    </td>

                                    <td>
                                        $358.10
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-success text-success">Paid</span>
                                    </td>

                                    <td>
                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i class="mdi mdi-pencil"></i></a>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div> <!-- end .table-responsive-->
                </div> <!-- end card-box-->
            </div> <!-- end col -->
            @endif
        </div>
        <!-- end row -->
        
    </div> <!-- container -->
@elseif($service_provider && $permission)
    <!-- Start Content-->
    <div class="container-fluid">
    
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Session Dashboard2</h4>
                </div>
            </div>
        </div>     
        <!-- end page title --> 

        <div class="row">
            <div class="col-md-4 col-xl-3">
                <div class="widget-rounded-circle card-box">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-info border-info border">
                                <i class="fas fa-comments font-22 avatar-title text-info"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-right">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $total_req }}</span></h3>
                                <p class="text-muted mb-1 text-truncate">Total Requests</p>
                            </div>
                        </div>
                    </div> <!-- end row-->
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->

           
        </div>
        <!-- end row-->
        <div class="row">
            <div class="col-lg-4">
                <div class="card-box">
                    <div class="dropdown float-right">
                        
                    </div>

                    <h4 class="header-title mb-0">Total Revenue</h4>

                    <div class="widget-chart text-center" dir="ltr">
                        
                        <div id="chart" class="mt-0"  data-colors="#f1556c"></div>

                        <h5 class="text-muted mt-0">Total Over All Revenue</h5>
                        <h2>{{ $revenue['totalRevenue'] }}</h2>
                    </div>
                </div> <!-- end card-box -->
            </div> <!-- end col-->

            <div class="col-lg-8">
                <div class="card-box pb-2">
                    <div class="float-right d-none d-md-inline-block">
                        <div class="btn-group mb-2">
                            <button type="button" id="WeeklyClass" class="btn btn-xs btn-secondary">Weekly</button>
                            <button type="button" id="MonthlyClass" class="btn btn-xs btn-light">Monthly</button>
                        </div>
                    </div>

                    <h4 class="header-title mb-3">Sales Analytics</h4>

                    <div dir="ltr" class="analyticsWeeklyClassToggle">
                        <div id="sales-analyticsWeeklyClass" class="mt-4" data-colors="#1abc9c,#4a81d4"></div>
                    </div>
                    <div dir="ltr" class="analyticsMonthlyClassToggle" style="display: none;">
                        <div id="sales-analyticsMonthlyClass" class="mt-4" data-colors="#1abc9c,#4a81d4"></div>
                    </div>
                </div> <!-- end card-box -->
            </div> <!-- end col-->
        </div>
        <!-- end row -->

    </div> <!-- container -->
@endif
@endsection
@section('script')
  <!-- Plugins js-->
  <script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>
  <script src="{{asset('assets/libs/apexcharts/apexcharts.min.js')}}"></script>
  <script src="{{asset('assets/libs/selectize/selectize.min.js')}}"></script>

  <script type="text/javascript">
    $('#searchByCategory').change(function(){
        $.ajax({
           type:'GET',
           url:base_url+'/admin/Trevenue',
           data:{category:$(this).val()},
           success:function(data){
              chartData.destroy();
              $('#overAll').text(data.data.totalRevenue);
              var options = {
              series: [parseInt(data.data.totalRevenue)/100],
              chart: {
              height: 350,
              type: 'radialBar',
            },
            plotOptions: {
              radialBar: {
                hollow: {
                  size: '70%',
                }
              },
            },
            labels: ['Revenue'],
            };
            var chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();
           }
        });
    });
    $("#WeeklyClass").click(function(){
        $(this).removeClass("btn-light");
        $(this).addClass("btn-secondary");

        $("#MonthlyClass").removeClass("btn-secondary");
        $("#MonthlyClass").addClass("btn-light");
        $(".analyticsMonthlyClassToggle").css('display','none');
        $(".analyticsWeeklyClassToggle").css('display','block');
    });
    $("#MonthlyClass").click(function(){
        $(this).removeClass("btn-light");
        $(this).addClass("btn-secondary");

        $("#WeeklyClass").removeClass("btn-secondary");
        $("#WeeklyClass").addClass("btn-light");

        $(".analyticsWeeklyClassToggle").css('display','none');
        $(".analyticsMonthlyClassToggle").css('display','block');
    });
    var amount = <?php echo json_encode($revenue["revenueWeekly"]["amount"]); ?>;
    var sales = <?php echo json_encode($revenue["revenueWeekly"]["sales"]); ?>;
    var dates = <?php echo json_encode($revenue["revenueWeekly"]["dates"]); ?>;
    var mamount = <?php echo json_encode($revenue["revenueMonthly"]["amount"]); ?>;
    var msales = <?php echo json_encode($revenue["revenueMonthly"]["sales"]); ?>;
    var months = <?php echo json_encode($revenue["revenueMonthly"]["months"]); ?>;
    var options = {
      series: [parseInt("<?php echo $revenue['totalRevenue']; ?>")/100],
      chart: {
      height: 350,
      type: 'radialBar',
    },
    plotOptions: {
      radialBar: {
        hollow: {
          size: '70%',
        }
      },
    },
    labels: ['Revenue'],
    };

    var chartData = new ApexCharts(document.querySelector("#chart"), options);
    chartData.render();
    var colors = ['#1abc9c', '#4a81d4'];
    var dataColors = $("#sales-analyticsWeeklyClass").data('colors');
    if (dataColors) {colors = dataColors.split(",");}
    var options = {
        series: [{name: 'Revenue',type: 'column',data:amount},{name: 'Sales',type: 'line',data:sales}],chart: {height: 378,type: 'line'},stroke: {width: [2, 3]},  plotOptions: {bar: {columnWidth: '50%'}},colors: colors,dataLabels: {enabled: true,enabledOnSeries: [1]},labels:dates,xaxis: {type: 'datetime'},legend: {offsetY: 7},grid: {padding: { bottom: 20}},fill: {type: 'gradient',gradient: {      shade: 'light',type:"horizontal",  shadeIntensity: 0.25,    gradientToColors: undefined,inverseColors: true,opacityFrom: 0.75,opacityTo: 0.75,stops: [0, 0, 0]}},yaxis: [{title: {text: 'Net Revenue'}}, {opposite: true,title: {text: 'Number of Sales'}}]};
        var chart = new ApexCharts(document.querySelector("#sales-analyticsWeeklyClass"), options);chart.render();

var dataColors = $("#sales-analyticsMonthlyClass").data('colors');
    if (dataColors) {colors = dataColors.split(",");}
    var options = {
        series: [{name: 'Revenue',type: 'column',data:mamount},{name: 'Sales',type: 'line',data:msales}],chart: {height: 378,type: 'line'},stroke: {width: [2, 3]},  plotOptions: {bar: {columnWidth: '50%'}},colors: colors,dataLabels: {enabled: true,enabledOnSeries: [1]},labels:months,xaxis: {type: 'months'},legend: {offsetY: 7},grid: {padding: { bottom: 20}},fill: {type: 'gradient',gradient: {      shade: 'light',type:"horizontal",  shadeIntensity: 0.25,    gradientToColors: undefined,inverseColors: true,opacityFrom: 0.75,opacityTo: 0.75,stops: [0, 0, 0]}},yaxis: [{title: {text: 'Net Revenue'}}, {opposite: true,title: {text: 'Number of Sales'}}]};
        var chart = new ApexCharts(document.querySelector("#sales-analyticsMonthlyClass"), options);chart.render();
  </script>
@endsection
 