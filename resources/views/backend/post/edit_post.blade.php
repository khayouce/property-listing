@extends('admin.admin_dashboard')
@section('admin')
<script src="{{ asset('backend/assets/js/jquery-3.7.0.min.js') }}"></script>
<div class="page-content">

<div class="row profile-body">

<!-- middle wrapper start -->
<div class="col-md-12 col-xl-12 middle-wrapper">
<div class="row">

<div class="card">
<div class="card-body">

			<h6 class="card-title">Edit Post</h6>

			<form  method="POST" action="{{ route('update.post') }}" class="forms-sample" enctype="multipart/form-data">
				@csrf

                <input type="hidden" name="id" value="{{ $post->id }}">

				 <div class="row">
<div class="col-sm-6">
    <div class="form-group mb-3">
        <label class="form-label">Post Title</label>
        <input type="text" name="post_title" class="form-control" value="{{ $post->post_title }}">
    </div>
</div><!-- Col -->
<div class="col-sm-6">
    <div class="form-group mb-3">
        <label class="form-label">Blog Category</label>
        <select name="blogcat_id" class="form-select" id="exampleFormControlSelect1">
                        <option selected="" disabled="">Select Category</option>
                        @foreach($blogcat as $cat)
                        <option value="{{ $cat->id }}" {{ $cat->id == $post->blogcat_id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
    </div>
</div><!-- Col -->
</div>

<div class="col-sm-12">
    <div class="mb-3">
        <label class="form-label">Short Description</label>
        <textarea class="form-control" name="short_description" id="exampleFormControlTextarea1" rows="3">{{ $post->short_description }}</textarea>
    </div>
</div><!-- Col -->

<div class="col-sm-12">
    <div class="mb-3">
        <label class="form-label">Long Description</label>
        <textarea class="form-control" name="long_description" id="tinymceExample" rows="10">{!! $post->long_description !!}</textarea>
    </div>
</div><!-- Col -->

 <div class="col-sm-6">
    <div class="form-group mb-3">
        <label class="form-label">Post Tags</label>
       <input name="post_tags" id="tags" value="{{ $post->post_tags }}" />
    </div>
</div><!-- Col -->

				<div class="mb-3">
					<label for="exampleInputUsername1" class="form-label">State Photo</label>
					<input class="form-control" name="post_image" type="file" id="image">
				</div>
				<div class="mb-3">
					<label for="exampleInputUsername1" class="form-label"></label>
					 <img id="showImage" class="wd-80 rounded-circle" src="{{ asset($post->post_image) }} " alt="post_image">
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