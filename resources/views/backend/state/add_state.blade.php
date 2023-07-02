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

								<h6 class="card-title">Add State</h6>

								<form  method="POST" action="{{ route('store.state') }}" class="forms-sample" enctype="multipart/form-data">
									@csrf
									<div class="mb-3">
										<label for="exampleInputUsername1" class="form-label">State Name</label>
										<input type="text" name="state_name" class="form-control @error('state_name') is-invalid @enderror">
										@error('state_name')
										<span class="text-danger">{{ $message }}</span>
										@enderror
									</div>
									<div class="mb-3">
										<label for="exampleInputUsername1" class="form-label">State Image</label>
										<input class="form-control" name="state_image" type="file" id="image">
									</div>
									<div class="mb-3">
										<label for="exampleInputUsername1" class="form-label"></label>
										 <img id="showImage" class="wd-80 rounded-circle" src="{{ url('upload/no_image.jpg')}} " alt="state_image">
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

	<script type="text/javascript">
		$(document).ready(function(){
			$('#image').change(function(e){
				var reader = new FileReader();
				reader.onload = function(e){
					$('#showImage').attr('src',e.target.result);
				}
				reader.readAsDataURL(e.target.files['0']);
			});
		});

	</script>

@endsection