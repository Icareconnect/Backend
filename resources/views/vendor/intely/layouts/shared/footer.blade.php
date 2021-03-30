@if(!isset($after_signup))
  <!-- Footer -->
 <footer>
   <div class="container">
     <div class="row">
       <div class="col-md-9">
         <!-- <a class="mr-4" href="">Carrers</a> -->
         <a class="mr-4" href="{{ route('about-us') }}">About Us</a>
         <a class="mr-4" href="{{ route('contact-us') }}">Contact us</a>
         <!-- <a class="mr-4" href="">Blogs</a> -->
         <!-- <a class="mr-4" href="">Terms and Conditions</a>                    -->
       </div>
       <div class="col-md-3 text-right">
         <a class="mr-4" href="https://www.instagram.com/icareconnectca" target="__blank"><img src="{{ asset('assets/intely/images/ic_insta.png') }}"></a>
         <a class="mr-4" href="https://twitter.com/icareconnectca" target="__blank"><img src="{{ asset('assets/intely/images/ic_twitter.png') }}"></a>
         <a class="" href="https://www.facebook.com/icareconnectca" target="__blank"><img src="{{ asset('assets/intely/images/ic_fb.png') }}"></a>
       </div>
     </div>
     <div class="row mt-5">
       <div class="col-md-2">
         <span>Our Address</span>
         <p class="">iCareConnect Ltd</p>
         <p class="mb-0">2 Bloor Street East, Suite 3500<br> Toronto, Ontario<br> M4W 1A8</p>
         <p><b>www.icareconnect.ca</b></p>
       </div>
       <div class="col-md-3">
         <span>Phone No</span>
         <p class="">1-855-830-(care) 2273<br>1-647-670-(care) 2273</p>
         <span>Email:</span>
         <p class="">support@icareconnect.ca</p>
       </div>
     </div>
   </div>
 </footer>
 <section class="copyright">
   <div class="container">
     <div class="row">
       <div class="col-md-12">
         <a class="mr-4" href="">Terms of Service</a>
         <a href="">Privacy Policy</a>
       </div>
     </div>
   </div>
 </section>
@else
    
@endif