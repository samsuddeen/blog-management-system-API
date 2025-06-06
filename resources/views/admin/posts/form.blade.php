@extends('admin.layouts.app')
@section('content')

<div id="app-content">
    <div class="app-content-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12">
                    <div class="mb-5">
                        <h3 class="mb-0">{{ isset($post) ? 'Edit Post' : 'Add Post' }}</h3>
                    </div>
                </div>
            </div>
            <div>
                <form action="{{ isset($post) ? route('post.update', $post->id) : route('post.store') }}"
                    method="POST" enctype="multipart/form-data">
                  @csrf
                  @if(isset($post))
                      @method('PUT')
                  @endif
                <div class="row">
                    <div class="col-lg-8 col-12">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title', $post->title ?? '') }}" >
                                    @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="editor1" name="body" rows="6">{{ old('body', $post->body ?? '') }}</textarea>
                                    @error('body') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" name="category_id" >
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ @$category->id }}" {{ isset($post) && $post->category_id == $category->id ? 'selected' : '' }}>{{ @$category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-12">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="1" {{ isset($post) && $post->status == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ isset($post) && $post->status == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                               

                                <div class="mb-4">
                                    <h4 class="mb-4">Featured Image</h4>
                                    <div class="input-group">
                                        <input id="thumbnail" class="form-control" type="text" name="image" value="{{ old('image', $post->image ?? '') }}" >
                                        <span class="input-group-btn">
                                            <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                                                <i class="fa-solid fa-image"></i> Choose
                                            </a>
                                        </span>
                                    </div>
                                    <img id="holder" src="{{ old('image', $post->image ?? '') }}" style="margin-top:15px;max-height:100px;">
                                    @error('image') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($post) ? 'Update' : 'Create' }} post
                            </button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>

<script>
    $(document).ready(function () {
        $('#lfm').filemanager('image');

        $('#thumbnail').on('change', function () {
            var imageUrl = $(this).val();
            if (imageUrl) {
                $('#holder').attr('src', imageUrl).show();
            } else {
                $('#holder').hide();
            }
        });
    });
</script>
    <script>
    CKEDITOR.replace('editor1');
</script>
@endpush
