@extends('layouts.vertical', ['title' => 'View '.__('text.Vendor')])

@section('css')
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
  <style type="text/css">
    /*.tab-pane{
    height:462px;
    overflow-y:scroll;
    width:100%;
  }*/
  </style>
@endsection

@section('content')
    <div id="modal01" class="w3-modal" onclick="this.style.display='none'" style="z-index: 999999;">
      <span class="w3-button w3-hover-red w3-xlarge w3-display-topright">&times;</span>
      <div class="w3-modal-content w3-animate-zoom">
        <img id="img01" style="width:100%">
      </div>
    </div>
    <div class="row">
            <div class="col-md-4">
              <div class="card-header p-2">
                  <ul class="nav nav-pills">
                    <li class="nav-item"></li>
                  </ul>
                </div><!-- /.card-header -->
              <!-- Profile Image -->
              <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <div class="text-center">
                          <img class="profile-user-img img-fluid img-circle" src="{{ Storage::disk('spaces')->url('thumbs/'.$consultant->profile_image) }}" alt="User Image">
              
                  </div>

                  <h3 class="profile-username text-center">{{ ($consultant->name)?$consultant->name:'unknown' }}</h3>
                  <!-- <p class="text-muted text-center">{{ $consultant->name }}</p> -->

                  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                      <b>Email:</b> {{ $consultant->email }}
                    </li>
                    <li class="list-group-item">
                      <b>Phone:</b> {{ $consultant->country_code }}-{{ $consultant->phone }}
                    </li>
                    <li class="list-group-item">
                      <b>Category:</b> {{ $consultant->getCategoryData($consultant->id)?$consultant->getCategoryData($consultant->id)->name:'NA' }} 
                    </li> 
                    <li class="list-group-item">
                      <b>About:</b> {{ ($consultant->profile && $consultant->profile->about)?$consultant->profile->about:'' }} 
                    </li>
                    <li class="list-group-item">
                      <b>Address:</b> {{ ($consultant->profile && $consultant->profile->location_name)?$consultant->profile->location_name:'' }} 
                    </li>
                  </ul>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->

              <!-- About Me Box -->
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Action</h3>
                </div>
                <div class="card-body">
                  @if($consultant->account_verified)
                    <button class="btn btn-sm btn-success float-left">Approved</button>
                    <button id="activeExpert" class="btn btn-sm {{ ($consultant->account_active)?'btn-success':'btn-danger' }}  float-right" data-approved="{{ ($consultant->account_active)?'true':'false' }}" data-consultant_id="{{ $consultant->id }}">{{ ($consultant->account_active)?'Active':'Inactive' }}</button>
                  @else
                    <button id="approveExpert" class="btn btn-sm btn-success float-left" data-approved="{{ ($consultant->account_verified)?'true':'false' }}" data-consultant_id="{{ $consultant->id }}">{{ ($consultant->account_verified)?'Approved':'Approve' }}</button>
                    @if(Config::get('client_connected') && Config::get('client_data')->domain_name=='intely')
                      <button id="rejectCovidExpert" data-approved="{{ ($consultant->account_covid_rejected)?'true':'false' }}" data-consultant_id="{{ $consultant->id }}" class="btn btn-sm btn-danger float-right" style="margin-left: 10px;">{{ ($consultant->account_covid_rejected)?'Covid Rejected':'Covid Decline' }}</button>
                      <button id="rejectExpert" data-approved="{{ ($consultant->account_rejected)?'true':'false' }}" data-consultant_id="{{ $consultant->id }}" class="btn btn-sm btn-danger float-right">{{ ($consultant->account_rejected)?'Rejected':'Doc Decline' }}</button>
                    @else
                    <button id="rejectExpert" data-approved="{{ ($consultant->account_rejected)?'true':'false' }}" data-consultant_id="{{ $consultant->id }}" class="btn btn-sm btn-danger float-right">{{ ($consultant->account_rejected)?'Rejected':'Reject' }}</button>
                    @endif
                  @endif
                </div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-md-8">
              <div class="card">
                <div class="card-header">
                </div><!-- /.card-header -->
                <div class="card-body">
                  <ul class="nav nav-tabs nav-bordered">
                      <li class="nav-item">
                          <a href="#Documents" data-toggle="tab" aria-expanded="false" class="nav-link px-3 py-2 active">
                              <i class="mdi mdi-pencil-box-multiple font-18 d-md-none d-block"></i>
                              <span class="d-none d-md-block">Documents</span>
                          </a>
                      </li>
                      @if(Config::get('client_connected') && Config::get('client_data')->domain_name=='intely')
                      <li class="nav-item">
                          <a href="#CovidReview" data-toggle="tab" aria-expanded="true" class="nav-link px-3 py-2">
                              <i class="mdi mdi-image font-18 d-md-none d-block"></i>
                              <span class="d-none d-md-block">Covid Review</span>
                          </a>
                      </li>
                      @endif
                      <li class="nav-item">
                          <a href="#BankAccount" data-toggle="tab" aria-expanded="true" class="nav-link px-3 py-2">
                              <i class="mdi mdi-image font-18 d-md-none d-block"></i>
                              <span class="d-none d-md-block">Bank Account</span>
                          </a>
                      </li>
                  </ul>
                  <div class="tab-content">
                    <div class="active tab-pane" id="Documents">
                      <?php $i=0; if($consultant->additionals){ ?>
                      @foreach($consultant->additionals as $key=>$additional)
                        @foreach($additional->documents as $document)
                          <?php $i++; ?>
                          <div class="post clearfix">
                            <div class="user-block">
                              <b>{{ $i }}. </b> {{ $additional->name }} ||
                              <span class="username">
                                <b>Title:</b> {{ $document->title }}
                              </span><br>
                              <span class="description"><b>Description:</b> {{ $document->description }}</span><br>
                              @if($document->status == 'declined')
                                  <span class="description"><b>Comment:</b> {{ $document->comment }}</span><br>
                              @endif
                              @if(strtolower($document->type)=='pdf')
                                <object data="{{ Storage::disk('spaces')->url('pdf/'.$document->file_name) }}" type="application/pdf" height="200px" width="200px">
                                  <!-- <p>Alternative text - include a link <a href="myfile.pdf">to the PDF!</a></p> -->
                              </object>
                              @else
                              <img style="cursor:pointer" 
                                onclick="onClick(this)" class="myImg img-circle img-bordered-sm" height="100px" width="100px" src="{{ Storage::disk('spaces')->url('uploads/'.$document->file_name) }}" alt="User Image">
                              @endif
                            </div>
                            @if($document->status == 'in_progress')
                              <button class="btn btn-sm btn-success approvedDocument" data-consultant_id="{{ $consultant->id }}" data-document_id="{{ $document->id }}">Approve</button>
                              <button class="btn btn-danger btn-sm declinedDocument"  data-consultant_id="{{ $consultant->id }}" data-document_id="{{ $document->id }}">Decline</button> 
                            @elseif($document->status == 'declined')
                              <button class="btn btn-sm btn-success approvedDocument" data-consultant_id="{{ $consultant->id }}" data-document_id="{{ $document->id }}">Approve</button>
                              <button class="btn btn-danger btn-sm">Declined</button>
                            @elseif($document->status == 'approved')
                              <button class="btn btn-sm btn-success">Approved</button>
                              <button class="btn btn-danger btn-sm declinedDocument"  data-consultant_id="{{ $consultant->id }}" data-document_id="{{ $document->id }}">Decline</button> 
                            @endif
                              @if(strtolower($document->type)=='pdf')
                              <a href="{{ Storage::disk('spaces')->url('pdf/'.$document->file_name) }}" class="btn btn-sm btn-success" target="__blank">View</a>
                              @else
                              <a href="{{ url('download/'.$document->file_name) }}" class="btn btn-sm btn-success">Download</a>
                              @endif
                            <hr>
                          </div>
                        @endforeach
                      @endforeach
                    <?php } ?>
                      <!-- /.post -->
                      
                    </div>
                    @if(Config::get('client_connected') && Config::get('client_data')->domain_name=='intely')
                      <div class="tab-pane" id="CovidReview">
                        <?php $j = 1; ?>
                        @if($consultant->master_preferences)
                          @foreach($consultant->master_preferences as $key=>$master_preference)
                              @if($master_preference['preference_type']=='covid')
                              <div class="post clearfix">
                                <div class="user-block">
                                  <b>{{ $j }}. </b> {{ $master_preference['preference_name'] }}
                                  <br>
                                  <br>
                                  <span class="username">
                                    <b>Ans:</b> {{ $master_preference['options'][0]['option_name'] }}
                                  </span><br>
                                </div>
                                <hr>
                              </div>
                              <?php $j++ ?>
                              @endif
                          @endforeach
                        @else
                           <h5 style="text-align: center;">No Covid Info Saved</h5>
                        @endif
                        <!-- /.post -->
                        
                      </div>
                    @endif
                    <div class="tab-pane" id="BankAccount">
                      @if(!$consultant->account)
                      <h5 style="text-align: center;">Bank Account not added yet!</h5>
                      @else
                      <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstname">Account Holder Name</label>
                                <input type="text" value="{{ $consultant->account->holder_name }}" class="form-control" id="firstname"  disabled="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstname">{{ __('text.Vendor') }} Type</label>
                                <input type="text" value="{{ ($consultant->account->customer_type)?$consultant->account->customer_type:'NA' }}" class="form-control" id="firstname"  disabled="">
                            </div>
                        </div>
                    </div> <!-- end row --> 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstname">Bank Name</label>
                                <input type="text" value="{{ $consultant->account->bank_name }}" class="form-control" id="firstname"  disabled="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lastname">Account Number</label>
                                <input type="text" class="form-control" value="{{ $consultant->account->account_number }}" id="lastname"  disabled="">
                            </div>
                        </div> <!-- end col -->
                    </div> <!-- end row --> 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstname">Routing Number</label>
                                <input type="text" value="{{ $consultant->account->ifc_code }}" class="form-control" id="firstname"  disabled="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lastname">Account Type</label>
                                <input type="text" class="form-control" value="{{ $consultant->account->account_type }}" id="lastname"  disabled="">
                            </div>
                        </div> <!-- end col -->
                        
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstname">City</label>
                                <input type="text" value="{{ ($consultant->account->city)?$consultant->account->city:'NA' }}" class="form-control" id="firstname"  disabled="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lastname">Address</label>
                                <input type="text" class="form-control" value="{{ ($consultant->account->address)?$consultant->account->address:'NA' }}" id="lastname"  disabled="">
                            </div>
                        </div> <!-- end col -->
                        
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstname">Province</label>
                                <input type="text" value="{{ ($consultant->account->province)?$consultant->account->province:'NA' }}" class="form-control" id="firstname"  disabled="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lastname">Postal Code</label>
                                <input type="text" class="form-control" value="{{ ($consultant->account->postal_code)?$consultant->account->postal_code:'NA' }}" id="lastname"  disabled="">
                            </div>
                        </div> <!-- end col -->
                        
                    </div>
                    @endif
                    </div>
                    <!-- /.tab-pane -->
                  </div>
                  <!-- /.tab-content -->
                </div><!-- /.card-body -->
              </div>
              <!-- /.nav-tabs-custom -->
            </div>
            <!-- /.col -->
          </div>
   @endsection
@section('script')
<script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
<script>
  function onClick(element) {
    document.getElementById("img01").src = element.src;
    document.getElementById("modal01").style.display = "block";
  }
  var doctor_text = "{{ __('text.Vendor') }}";
  $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
  $(".approvedDocument").on('click',function(e){
          var __this = $(this);
          var document_id = __this.attr('data-document_id');
          var consultant_id = __this.attr('data-consultant_id');
          // if(approved=='false'){
          Swal.fire({
            title: 'Are you sure?',
            text: "Do You want to Approve this document",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Approved!'
          }).then((result) => {
            if (result.value) {
                $.ajax({
                   type:'PUT',
                   url:base_url+'/admin/consultants/'+consultant_id,
                   data:{id:consultant_id,account_document_verify:'true',document_id:document_id},
                   success:function(data){
                      Swal.fire(
                        'Approved!',
                        'Document has been Approved.',
                        'success'
                      ).then((result)=>{
                        location.reload();
                      });
                   }
                });
              }
          });
         // }
  });
  $(".declinedDocument").on('click',function(e){
          var __this = $(this);
          var document_id = __this.attr('data-document_id');
          var consultant_id = __this.attr('data-consultant_id');
          // if(approved=='false'){
          Swal.fire({
            title: 'Write reason for Decline Document:',
            input: 'text',
            inputAttributes: {
              autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Decline',
            showLoaderOnConfirm: true,
            preConfirm: (data) => {
                if(!data)
                  Swal.showValidationMessage(
                    'Write eason for Decline Document:'
                  )
            },
            allowOutsideClick: () => !Swal.isLoading()
          }).then((result) => {
            if (result.value) {
                $.ajax({
                   type:'PUT',
                   url:base_url+'/admin/consultants/'+consultant_id,
                   data:{id:consultant_id,account_document_decline:'true',document_id:document_id,'comment':result.value},
                   success:function(data){
                      Swal.fire(
                        'Declined!',
                        'Document has been Declined.',
                        'success'
                      ).then((result)=>{
                        location.reload();
                      });
                   }
                });
              }
          });
         // }
  });
  $("#approveExpert").on('click',function(e){
          var __this = $(this);
          var consultant_id = __this.attr('data-consultant_id');
          var approved = __this.attr('data-approved');
          if(approved=='false'){
            Swal.fire({
              title: 'Are you sure?',
              text: "You want to Approve this account",
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, Approved!'
            }).then((result) => {
              if (result.value) {
                  $.ajax({
                     type:'PUT',
                     url:base_url+'/admin/consultants/'+consultant_id,
                     data:{id:consultant_id,account_verify_ajax:'true'},
                     success:function(data){
                        Swal.fire(
                          'Approved!',
                          'Account has been Approved.',
                          'success'
                        ).then((result)=>{
                          __this.attr('data-approved','true');
                          __this.text('Approved');
                          location.reload();
                        });
                     }
                  });
                }
            });
         }
  });
  $("#rejectCovidExpert").on('click',function(e){
    var __this = $(this);
    console.log(__this.attr('data-approved'))
    var consultant_id = __this.attr('data-consultant_id');
    var approved = __this.attr('data-approved');
    if(approved=='false'){
      Swal.fire({
        title: 'Write reason for Reject:',
        input: 'text',
        inputValue:'you can start your service after 14 days recovery.',
        inputAttributes: {
          autocapitalize: 'off',
          disabled:true,
        },
        showCancelButton: true,
        confirmButtonText: 'Reject',
        showLoaderOnConfirm: true,
        preConfirm: (data) => {
            if(!data)
              Swal.showValidationMessage(
                'Write reason for Reject:'
              )
        },
        allowOutsideClick: () => !Swal.isLoading()
      }).then((result) => {
        // __this.text('Rejecting');
        if (result.value) {
          $.ajax({
             type:'PUT',
             url:base_url+'/admin/consultants/'+consultant_id,
             data:{id:consultant_id,account_covid_reject_ajax:'true','comment':result.value},
             success:function(data){
                Swal.fire(
                  'Rejected!',
                  'Account has been Rejected Due to Covid Result',
                  'success'
                ).then((result)=>{
                    location.reload();
                });
             }
          });
        }
      });
    }
  });

  $("#rejectExpert").on('click',function(e){
    var __this = $(this);
    console.log(__this.attr('data-approved'))
    var consultant_id = __this.attr('data-consultant_id');
    var approved = __this.attr('data-approved');
    if(approved=='false'){
      Swal.fire({
        title: 'Write reason for Reject:',
        input: 'text',
        inputAttributes: {
          autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Reject',
        showLoaderOnConfirm: true,
        preConfirm: (data) => {
            if(!data)
              Swal.showValidationMessage(
                'Write reason for Reject:'
              )
        },
        allowOutsideClick: () => !Swal.isLoading()
      }).then((result) => {
        // __this.text('Rejecting');
        if (result.value) {
          $.ajax({
             type:'PUT',
             url:base_url+'/admin/consultants/'+consultant_id,
             data:{id:consultant_id,account_reject_ajax:'true','comment':result.value},
             success:function(data){
                Swal.fire(
                  'Rejected!',
                  'Account has been Rejected.',
                  'success'
                ).then((result)=>{
                    location.reload();
                });
             }
          });
        }
      });
    }
  });

 $("#activeExpert").on('click',function(e){
        var __this = $(this);
        console.log(__this.attr('data-approved'))
        var consultant_id = __this.attr('data-consultant_id');
        var approved = __this.attr('data-approved');
        var text = 'InActive';
        if(approved=='false'){
          text = 'Active';
        }
        Swal.fire({
          title: 'Are you sure?',
          text: "You want to "+text+" this account",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes!'
        }).then((result) => {
          if (result.value) {
              $.ajax({
                 type:'PUT',
                 url:base_url+'/admin/consultants/'+consultant_id,
                 data:{id:consultant_id,account_active_ajax:'true'},
                 success:function(data){
                    Swal.fire(
                      'Approved!',
                      'Account has been '+ text +'.',
                      'success'
                    ).then((result)=>{
                        location.reload();
                    });
                 }
              });
            }
        });
  });
</script>
@endsection