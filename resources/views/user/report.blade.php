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
            @if (session('success'))
                <h6 class="alert alert-primary">{{ session('success') }}</h6>
            @endif

            @if (session('warning'))
                <h6 class="alert alert-danger">{{ session('warning') }}</h6>
            @endif
            <div class="card card-body">
                <h6 class="card-title">Comment</h6>
                <form action="{{ route('user.report.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <textarea name="comment_body" class="form-control" rows="3" required></textarea>
                    <button class="btn btn-primary mt-3">Submit</button>
                </form>
            </div>
            @forelse ($list as $item)
                <div class="card card-body shadow-sm mt-3">
                    <div class="detail-area">
                        <h6 class="user-name mb-1">{{ $user->last_name . ' ' . $user->first_name }}
                            <small class="ms-3 text-primary">Comment on: {{ $item->created_at }} </small>
                        </h6>
                        <p class="user-comment mb-1">{{ $item->comment }}</p>
                    </div>
                    <div>
                        <a href="" class="btn btn-primary btn-sm me-2">Edit</a>
                        <a href="" class="btn btn-danger btn-sm me-2">Delete</a>
                    </div>
                </div>
            @empty
                <h6>No Comment Yet</h6>
            @endforelse
        </div>
    </div>
@endsection
