
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Dr.</title>
  <meta charset="UTF-8">	
  <meta charset="utf-8">
  <meta name="viewport">
		<style>	
		* {
			padding: 0px;
			margin: 0px;
		}
		body {
			width: 100%;
			overflow-x: hidden;
			background: #f7f7f7;
			font-family: arial, sans-serif;
		}
	a{text-decoration:none;color:#000;outline: none;}

	a:hover{text-decoration:none;color:#000;outline: none;}
	a:focus{text-decoration:none;outline: none;}
	.clear{clear:both;}
	img-fluid{max-width: 100%;height: auto;}
	ul li{list-style:none;}
	.p-0{padding:0px !important}
	.border-0{border:0px;}
	.container-fluid{width:95%;display:block;margin: 20px auto; background: #fff; overflow: hidden;padding: 30px;}
	.w-100{width:100%;display: inline-block;};
	.w-25{width:25%;display:inline-block;}
	.pull-right{float:right;}
	.mx-auto;{margin:0 auto;;display:block;}
	.text-center{text-align:center;}
	.bold-text{font-weight:600;}
	.row{display:inline-block;width:100%}
	.text-right{text-align:right;}
	.statement-heading {
		margin-bottom:20px;
	}
	table {
	  
	  border-collapse: collapse;
	  width: 100%;
	}
	table, th, td {
	  border: none;
	  border-collapse: collapse;
	}
	th, td {
	  padding: 5px;
	}
	th {
	  text-align: left;
	  border-bottom: 1px solid #ddd;
	}
	td {
		color: #777;
		line-height: 24px;
		padding-top: 30px;
	}
		.col-6 {
			width: 49%;
			display: inline-block;
		}
		.col-4 {
			width: 33%;
			display: inline-block;
		}
		.col-8 {
			width: 64%;
			display: inline-block;
		}

	@media (max-width:768px) {
		.container-fluid {
		    width: 95%;
		    display: block;
		    margin: 20px auto;
		    background: #fff;
		    overflow: hidden;
		    padding: 15px;
		}
		.footer-block p {
			font-size: 15px!important;
		}
		.full-mobile {
			width: 100%!important;
			text-align: left!important;
		}
		.mt-4 {
			margin-top: 30px;
		}
		.footer-block .col-6 {
			width: 100%!important;
			margin-bottom: 5px;
		}
	}
	@media (max-width: 768px) { 
	.container-fluid {
		width: 95%;
		display: block;
		margin: 20px auto;
		background: #fff;
		overflow: hidden;
		padding: 15px;
		}
	}

	</style>
</head>
<body style="">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12" style="background: #31398F; padding: 20px;">
				<div class="row">
					<div class="col-8" style="vertical-align: middle;">
						<h4 style="color: #fff; font-size: 28px;">{{ Config::get("client_data")->first_name.' '.Config::get("client_data")->last_name  }}:<br>The Fitness Healthcare</h4>
						<p style="margin-bottom: 20px; color: #fff; margin-top: 8px;">Loream Ipsum is a dummy text</p>
					</div>
					<div class="col-4" style="text-align: right; padding-top: 20px;">
						@if(config('client_connected'))
                		@php $image_name = Config::get("client_data")->domain_name; @endphp
						<img style="height: 100px;" src="{{ asset('assets/images/'.$image_name.'.png') }}">
						@else
						<img style="height: 100px;" src="{{ asset('assets/images/logo-light.png') }}">
                		@endif
					</div>
				</div>
			</div>
			<div class="col-12">
				<hr style="margin-bottom: 20px; margin-top: 40px;">
			</div>
			<div class="col-6 full-mobile">
				<h4 style="color: #000; margin-bottom: 8px; font-size: 15px; font-size: 22px;">{{ $requesttable->cus_info->name }}</h4>
				<p style="color: #777; margin-bottom: 8px; font-size: 15px;">Phone: {{ $requesttable->cus_info->country_code.''.$requesttable->cus_info->phone }}</p>
				<p style="color: #777; margin-bottom: 8px; font-size: 15px;">Email: {{ $requesttable->cus_info->email }}</p>
				<p style="color: #777;">Age: {{ ($requesttable->cus_info->profile && $requesttable->cus_info->profile->dob)?$requesttable->cus_info->profile->dob:'NA' }}</p>
			</div>
			<div class="col-6 full-mobile mt-4" style="text-align: right;">
				<h5 style="margin-bottom: 15px; font-size: 15px; font-weight: 500; font-size: 18px; color: #777;">Prescription No: &nbsp; [{{ ($requesttable->pre_scription)?$requesttable->pre_scription->id:'NA' }}]</h5>
				<h5 style="margin-bottom: 8px; font-size: 15px; font-weight: 500; font-size: 18px; color: #777;">Date: &nbsp; [{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $requesttable->booking_date)->format('d/m/Y') }}]</h5>
				<p style="color: #777;">&nbsp;</p>
			</div>
			<div class="col-12">
				<hr style="margin-top: 20px; margin-bottom: 20px;">
			</div>
			<div class="col-12">
				<p>Rx</p>
				<hr style="margin-top: 5px; margin-bottom: 20px;">
			</div>
			@if($requesttable->pre_scription && $requesttable->pre_scription->type=='digital')
			<table style="width:100%">
			  <tr>
			    <th>Medicine Name</th>
			    <th>Dosage </th>
			    <th>Duration</th>
			  </tr>
			  @foreach($requesttable->medicines as $key=>$medicine)
			  <tr>
			    <td>{{ $medicine->medicine_name }} -- {{ $medicine->dosage_type }}</td>
			    <td>
			    	@foreach(json_decode($medicine->dosage_timing) as $dosage_timing) 
			    		{{ (isset($dosage_timing->dose_value))?$dosage_timing->dose_value:'One' }} ( {{ $dosage_timing->with.' '.$dosage_timing->time }}),<br>
			    	@endforeach
			    </td>
			    <td>{{ $medicine->duration }}</td>
			  </tr>
			  @endforeach
			</table>
			@elseif($requesttable->pre_scription && $requesttable->pre_scription->type=='manual')
			<section class="table-section">
					<h5 style="margin-bottom: 12px; font-size: 15px;">{{ $requesttable->pre_scription->title }}</h5>
					@foreach($requesttable->images as $img)
						<img src="{{ Storage::disk('spaces')->url('uploads/'.$img->image_name) }}" height="200px;" width="200px">
					@endforeach
				</section>
			@endif
			<hr style="margin-top: 20px; margin-bottom: 20px;">
			<div class="row">
				<div class="col-12">
					<h5 style="margin-bottom: 15px; font-size: 16px; color: #3a3a3a">Advice Given</h5>
					<p style="color: #777; margin-bottom: 8px; letter-spacing: 1px;">{{ isset($requesttable->pre_scription)?$requesttable->pre_scription->pre_scription_notes:'' }}</p>
					<!-- <h5 style="font-size: 16px; color: #3a3a3a">Take Appointment from my doctor in next 3 Days</h5> -->
				</div>
				<hr style="margin-top: 20px; margin-bottom: 20px;">
				<div class="col-8">
					
				</div>
				<div class="col-4">
					<h5 style="font-size: 17px; margin-bottom: 5px;">Dr {{ $requesttable->sr_info->name }}</h5>
					<p style="font-size: 16px; color: #777;">{{ $requesttable->sr_info->getCategoryData($requesttable->sr_info->id)->name }}:{{{ $requesttable->sr_info->country_code.''.$requesttable->sr_info->phone }}}</p>
				</div>
			</div>

			<div class="col-12 footer-block" style="margin: 0 -30px; background: #30398D; padding: 12px 20px; margin-top: 60px;">
				<div class="row">
					<div class="col-6">
						<p style="color: #fff;"><img style="margin-right: 8px; vertical-align: top;" src="{{ asset('assets/images/phone-icon.png') }}">[{{ $requesttable->sr_info->country_code.''.$requesttable->sr_info->phone }}]</p>
					</div>
					<div class="col-6" style="text-align: right;">
						<p style="color: #fff;"><img style="margin-right: 8px; vertical-align: top;" src="{{ asset('assets/images/msg-icon.png') }}">{{ $requesttable->sr_info->email }}</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

