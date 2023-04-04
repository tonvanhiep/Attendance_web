@extends('user.layout')

@section('title')
    User - Report
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/user/style.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="commemt-area mt-4">
        <div class="card card-body">
            <h6 class="card-title">Comment</h6>
            <form action="" method="POST">
                <textarea name="comment-body" class="form-control" rows="3" required></textarea>
                <button class="btn btn-primary mt-3">Submit</button>
            </form>
        </div>
        <div class="card card-body shadow-sm mt-3">
            <div class="detail-area">
                <h6 class="user-name mb-1">User
                    <small class="ms-3 text-primary">Comment on: 3-8-2023</small>
                </h6>
                <p class="user-comment mb-1">Hi</p>
            </div>
            <div>
                <a href="" class="btn btn-primary btn-sm me-2"></a>
                <a href="" class="btn btn-danger btn-sm me-2"></a>
            </div>
        </div>
    </div>
</div>
@endsection
