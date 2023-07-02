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

								<h6 class="card-title">Edit Amenities</h6>

								<form  method="POST" action="{{ route('update.amenities') }}" class="forms-sample">
									@csrf

									<input type="hidden" name="id" value="{{ $amenities->id }}">
									<div class="mb-3">
										<label for="exampleInputUsername1" class="form-label">Amenities</label>
										<input type="text" name="amenities_name" class="form-control @error('amenities_name') is-invalid @enderror" value="{{ $amenities->amenities_name }}" >
										@error('amenities_name')
										<span class="text-danger">{{ $message }}</span>
										@enderror
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

@endsection