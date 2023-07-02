@extends('admin.admin_dashboard')
@section('admin')
<script src="{{ asset('backend/assets/js/jquery-3.7.0.min.js') }}"></script>
<div class="page-content">

        <div class="row profile-body">
        
          <!-- middle wrapper start -->
          <div class="col-md-8 col-xl-8 middle-wrapper">
            <div class="row">
              
              <div class="card">
              <div class="card-body">

                <h6 class="card-title">Add Agent</h6>

                <form id="myForm" method="POST" action="{{ route('store.agent') }}" class="forms-sample">
                  @csrf
                  <div class="form-group mb-3">
                    <label for="exampleInputUsername1" class="form-label">Agent Name</label>
                    <input type="text" name="name" class="form-control">
                  </div>
                   <div class="form-group mb-3">
                    <label for="exampleInputUsername1" class="form-label">Agent Email</label>
                    <input type="email" name="email" class="form-control">
                  </div>

                   <div class="form-group mb-3">
                    <label for="exampleInputUsername1" class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control">
                  </div>

                   <div class="form-group mb-3">
                    <label for="exampleInputUsername1" class="form-label">Agent Address</label>
                    <input type="text" name="address" class="form-control">
                  </div>

                   <div class="form-group mb-3">
                    <label for="exampleInputUsername1" class="form-label">Agent Password</label>
                    <input type="password" name="password" class="form-control">
                  </div>
                  
                  <button type="submit" class="btn btn-primary me-2">Save Changes</button>
  
                </form>

              </div>
            </div>
  
            </div>
          </div>
          <!-- middle wrapper end -->
          <!-- right wrapper start -->
       
          <!-- right wrapper end -->
        </div>

      </div>

  </script>

  <script type="text/javascript">
    $(document).ready(function (){
        $('#myForm').validate({
            rules: {
                name: {
                    required : true,
                }, 
                email: {
                    required : true,
                }, 
                phone: {
                    required : true,
                }, 
                password: {
                    required : true,
                }, 
                
            },
            messages :{
                name: {
                    required : 'Please Enter Your Name',
                }, 
                email: {
                    required : 'Please Enter Your Email',
                }, 
                phone: {
                    required : 'Please Enter Your Mobile Number',
                }, 
                password: {
                    required : 'Please Enter Your Password',
                }, 
                 

            },
            errorElement : 'span', 
            errorPlacement: function (error,element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight : function(element, errorClass, validClass){
                $(element).addClass('is-invalid');
            },
            unhighlight : function(element, errorClass, validClass){
                $(element).removeClass('is-invalid');
            },
        });
    });
    
</script>

@endsection