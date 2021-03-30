$(function () {
    $("#example1").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
    $('#customers_pagination').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
     $(function () {
    // Summernote
      $('.page_body').summernote()
    });
     $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(".delete-page").click(function(e){
          e.preventDefault();
          var page_id = $(this).attr('data-page_id');
          Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
          }).then((result) => {
            if (result.value) {
                $.ajax({
                   type:'DELETE',
                   url:base_url+'/admin/pages/'+page_id,
                   data:{id:page_id},
                   success:function(data){
                      Swal.fire(
                        'Deleted!',
                        'Page has been deleted.',
                        'success'
                      ).then((result)=>{
                        window.location.reload();
                      });
                   }
                });
              }
          });
    
    });
    $(".delete-filter").click(function(e){
          e.preventDefault();
          var filter_id = $(this).attr('data-filter_id');
          Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
          }).then((result) => {
            if (result.value) {
                $.ajax({
                   type:'DELETE',
                   url:base_url+'/admin/filters/'+filter_id,
                   data:{id:filter_id},
                   success:function(data){
                      Swal.fire(
                        'Deleted!',
                        'Filter has been deleted.',
                        'success'
                      ).then((result)=>{
                        window.location.reload();
                      });
                   }
                });
              }
          });
    
    });

    $(".delete-cluster").click(function(e){
          e.preventDefault();
          var cluster_id = $(this).attr('data-cluster_id');
          Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
          }).then((result) => {
            if (result.value) {
                $.ajax({
                   type:'DELETE',
                   url:base_url+'/admin/cluster/'+cluster_id,
                   data:{id:cluster_id},
                   success:function(data){
                      Swal.fire(
                        'Deleted!',
                        'Cluster has been deleted.',
                        'success'
                      ).then((result)=>{
                        window.location.reload();
                      });
                   }
                });
              }
          });
    
    });
    $(".delete-banner").click(function(e){
          e.preventDefault();
          var banner_id = $(this).attr('data-banner_id');
          Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
          }).then((result) => {
            if (result.value) {
                $.ajax({
                   type:'DELETE',
                   url:base_url+'/admin/banner/'+banner_id,
                   data:{id:banner_id},
                   success:function(data){
                      Swal.fire(
                        'Deleted!',
                        'Banner has been deleted.',
                        'success'
                      ).then((result)=>{
                        window.location.reload();
                      });
                   }
                });
              }
          });
    
    });
    $('.edit-category').on('click', function() {
      var id = $(this).data('id');
      var name = $(this).data('name');
      var url = "{{ url('categories') }}/" + id;

      $('#editCategoryModal form').attr('action', url);
      $('#editCategoryModal form input[name="name"]').val(name);
    });

    $("#app_name_domain").keyup(function(){
      $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var domain__name = $("#app_name_domain").val();
      var letters = /^[0-9a-zA-Z_ ]+$/;
      if($("#app_name_domain").val().match(letters)){
        $('.app_name_domain_error').html('');
      }else{
        $('.app_name_domain_error').html('Please enter alphanumeric value');
        return false;
      }
      if(domain__name.length >= 3){
        //<img src="loader.gif" />
          $("#domain_name_checking").html('Checking availability...');
          $.ajax({
               type:'POST',
               url:base_url+'/admin/check-domain',
               data:{domain:domain__name},
               success:function(response){
                  $("#domain_name_checking").html('Domain');
                  if(response.status=='success'){
                    $("#domain_name").removeClass("is-warning");
                    $("#domain_name").removeClass("is-invalid");
                    $("#domain_name").addClass("is-valid");
                    $("#domain_name").val(response.domain);
                  }else{
                    $("#domain_name").removeClass("is-warning");
                    $("#domain_name").removeClass("is-valid");
                    $("#domain_name").addClass("is-invalid");
                    $("#domain_name").val('');
                  }
               }
            });
      }
    });
    $("#service__name").keyup(function(){
      var domain__name = $("#service__name").val();
      var letters = /^[a-z]+$/;
      if($("#service__name").val().match(letters)){
        $('.service__name_error').html('');
      }else{
        $('.service__name_error').html('Please enter small letters only');
        return false;
      }
    });
    $('.my-colorpicker1').colorpicker();

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('#profile-img-tag').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    function readURL2(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('#profile-img-tag-icon').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#ct-img-file").change(function(){
        readURL(this);
    }); 
    $("#image_icon").change(function(){
        readURL2(this);
    });
    $(".js-example-tokenizer").select2({
      tags: true,
      multiple: true,
      placeholder: "Add Option Multiple",
      tokenSeparators: [','],
    });
    $(".category_listing").select2({placeholder: "Add Option Multiple"});

    $(".category_to_service").select2({
      placeholder: "Select a Category",
      allowClear: true
    });

    $('.slider').bootstrapSlider();
    $('#reservation').daterangepicker();
  });