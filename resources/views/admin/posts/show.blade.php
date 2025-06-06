@extends('admin.layouts.app')

@section('content')
<div id="app-content">
    <div class="app-content-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Post Details</h4>
                            <a href="{{ route('post.index') }}" class="btn btn-primary">Back to List</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- post Image -->
                                <div class="col-md-6">
                                    <img src="{{ asset($post->image) }}" alt="{{ $post->title }}" class="img-fluid rounded">
                                </div>

                                <!-- post Details Table -->
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Title</th>
                                            <td>{{ $post->title }}</td>
                                        </tr>
                                        <tr>
                                            <th>Slug</th>
                                            <td>{{ $post->slug }}</td>
                                        </tr>
                                        <tr>
                                            <th>Content</th>
                                            <td>{!! $post->body !!}</td>
                                        </tr>
                                        <tr>
                                            <th>Category</th>
                                            <td>{{ $post->category->name ?? '' }}</td>
                                        </tr>
                                       
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="badge {{ $post->status ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $post->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created By</th>
                                            <td>{{ $post->user->name ?? 'Admin' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>{{ $post->created_at->format('d M, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Updated At</th>
                                            <td>{{ $post->updated_at->format('d M, Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div> <!-- card-body end -->
                    </div> <!-- card end -->
                </div> <!-- col end -->
            </div> <!-- row end -->
        </div> <!-- container-fluid end -->
    </div> <!-- app-content-area end -->
</div> <!-- app-content end -->
@endsection
