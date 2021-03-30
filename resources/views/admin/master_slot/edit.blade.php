@extends('layouts.vertical', ['title' => 'Add/Edit Master Intervals'])

@section('content')
<link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
<script type="text/javascript">
  var main_intervals = <?php echo json_encode($intervals); ?>;
  var main_start_times = <?php echo json_encode($start_times); ?>;
  var main_end_times = <?php echo json_encode($end_times); ?>;
</script>
<div class="card card-primary" id="manage_slots">
  <div class="card-header">
      <h3 class="card-title">Add/Edit Master Intervals</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form" action="{{ url('admin/edit')}}" method="post" enctype="multipart/form-data">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="PUT">
        <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
          <section class="select-time">
              <p class="select-date">Select Time</p>
              <div class="row" id="intervalList">
                  <div v-for="(interval, index) in intervals" class="col-md-12 row">
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="pwd">From</label>
                              <select v-model="interval.seleted_start" class="form-control" id="sel1">
                                  <option v-for="time1 in interval.start_times" :value="time1.key">
                                      @{{ time1.value }}</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="pwd">To</label>
                              <select v-model="interval.seleted_end" class="form-control" id="sel1">
                                  <option v-for="time in interval.end_times" :value="time.key">
                                      @{{ time.value }}</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-2">
                          <div class="form-group">
                              <button type="button" class="btn btn-primary" @click="deleteInterval(index)">Delete</button>
                          </div>
                      </div>
                  </div>
              </div>
          </section>
          <a href="javascript:void(0)" id='new_interval' @click="newInterval" class=" new-group">+ New
                        Interval</a>
        <!-- /.card-body -->
        <div class="card-footer">

          <button type="button" id="submit_btn_id" class="btn btn-primary" @click="saveAvai()">
                            @{{ submit_btn_text }}</button>
        </div>
    </form>
  </div>
@endsection
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.3/vue.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
<script>

  new Vue({
        el: '#manage_slots',
        data: {
            intervals:main_intervals,
            start_intervals: "00:00",
            end_intervals: "01:00",
            class_number: 1,
            selected_int_list: [],
            delete_img: base_url + "/assets/mp2r/images/delet.png",
            submit: false,
            handle_type: 'date',
            submit_btn_text: 'Save',
        },
        methods: {
            selectedData: function(data) {
                this.selected_date_model = data;
            },
            clickHadleType: function(type) {
                this.handle_type = type;
            },
            saveAvai: function() {
                var _this = this;
                  _this.submit_btn_text = 'Saving...';
                  $.ajax({
                      type: "post",
                      url: base_url + '/admin/master_slot/edit',
                      data: {
                          'timzone': timZone,
                          "interval": _this.intervals,
                      },
                      dataType: "json",
                      success: function(response) {
                          _this.submit_btn_text = 'Save';
                          Swal.fire('Success!', 'Intervals Saved', 'success');
                      },
                      error: function(jqXHR) {
                          _this.submit_btn_text = 'Save';
                          var response = $.parseJSON(jqXHR.responseText);
                          if (response.message) {
                              Swal.fire('Error!', response.message, 'error');
                          }
                      }
                  });
                // Swal.fire({
                //     title: 'Confirm!',
                //     text: 'Do you want to set Intervals for ' + _this.handle_type,
                //     showCancelButton: true,
                //     confirmButtonColor: '#3085d6',
                //     cancelButtonColor: '#d33',
                //     confirmButtonText: 'Yes!',
                // }).then((result) => {
                //     if (result.value) {
                //     }
                // });
            },
            deleteInterval: function(index) {
                this.intervals.splice(index, 1);
            },
            newInterval: function() {
                this.intervals.push({
                    seleted_start: "00:00",
                    seleted_end: "01:00",
                    start_times: main_start_times,
                    end_times: main_end_times
                });
            },
        },
        mounted() {

        }
    });
</script>
@endsection