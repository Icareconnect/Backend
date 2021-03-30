$(function() {
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
 /* -------------------------------------Login------------------------ */
  $('#ic__login').on('submit', function(e){
    e.preventDefault();
    $(".login_btn_text").html('<span>Please Wait...</span>');
    $("#ic__login .email_error").html(''); 
    $("#ic__login .password_error").html(''); 
    $("#ic__login .main_error").html(''); 
    var $this = $(this);
    $.ajax({
        type: "post",
        url: base_url+'/custom/login',
        data: $this.serializeArray(),
        dataType: "json",
        success: function (response) {
        $(".login_btn_text").html('<span>Submit</span>'); 
            location.reload();
        },
        error: function (jqXHR) {
        $(".login_btn_text").html('<span>Submit</span>'); 
          var response = $.parseJSON(jqXHR.responseText);
          if(response.errors){
            if(response.errors.email){
              $("#ic__login .email_error").html(response.errors.email[0]);
            }
            if(response.errors.password){
              $("#ic__login .password_error").html(response.errors.password[0]);
            }
          }else if(response.message){
              $("#ic__login .main_error").html(response.message);
          }
        }
    });
  });
  });


jQuery(document).ready(function(){

  $('#query_post').on('submit', function(e){
      e.preventDefault();
      $("#btn_text_val").html('Posting...');
      $("#query_post .main_error").html(''); 
      var $this = $(this);
      $.ajax({
            type: "post",
            url: base_url+'/query_post',
            data: $this.serializeArray(),
            dataType: "json",
            success: function (response) {
            $("#btn_text_val").html('Post a query');
              $('#email').val(''); 
              $('#first_name').val(''); 
              $('#last_name').val(''); 
              $('#subject').val(''); 
              $('#phone_number').val(''); 
              $('#query_data').val(''); 
              Swal.fire('Query Posted!','Your Query has been posted','success');
            },
            error: function (jqXHR) {
            $("#btn_text_val").html('Post a query');
              var response = $.parseJSON(jqXHR.responseText);
              if(response.message){
              Swal.fire('Error!',response.message,'error');
              }
            }
        });
      });

  $('#request_demo').on('submit', function(e){
      e.preventDefault();
      $("#btn_text_val").html('Wait...');
      $("#request_demo .main_error").html(''); 
      var $this = $(this);
      $.ajax({
            type: "post",
            url: base_url+'/request_demo',
            data: $this.serializeArray(),
            dataType: "json",
            success: function (response) {
            $("#btn_text_val").html('Submit');
            Swal.fire(
                'Coming Soon!',
                'Thank you for submitting your details for Demo.',
                'success'
              ).then((result)=>{
                window.location.reload();
              });
            },
            error: function (jqXHR) {
            $("#btn_text_val").html('Submit');
              var response = $.parseJSON(jqXHR.responseText);
              if(response.message){
              Swal.fire('Error!',response.message,'error');
              }
            }
        });
      });

        $('#send_link').on('click', function(e){
            // var input = $('#phone');
            e.preventDefault();
            let isvalid = iti.isValidNumber();
            let phone = iti.getNumber(intlTelInputUtils.numberFormat.E164);
            if(!isvalid){
                Swal.fire('Error!','Phone number not valid','error');
                return false;
            }
            $("#send_link").html('<span>Sending...</span>');
            var $this = $(this);
            $.ajax({
                type: "post",
                url: base_url+'/send_link',
                data: {phone:phone},
                dataType: "json",
                success: function (response) {
                    $('#phone').val('');
                    Swal.fire('Sent Link!','Link has been sent','success');
                    $("#send_link").html('<span>Send Link</span>'); 
                },
                error: function (jqXHR) {
                $("#send_link").html('<span>Send Link</span>');
                  var response = $.parseJSON(jqXHR.responseText);
                  if(response.errors){
                    if(response.errors.phone){
                      Swal.fire('Error!',response.errors.phone[0],'error');
                    }
                  }else if(response.message){
                      Swal.fire('Error!',response.message,'error');
                  }
                }
            });
          });
 });