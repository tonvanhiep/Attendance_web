@extends('user.layout')

@section('title')
    User - Profile
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/user/style.css') }}">
@endpush

@section('content')
    <section>
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <img src="{{ asset($user->avatar) }}"
                                alt="avatar" class="rounded-circle img-fluid" style="width: 150px;">
                            <h5 class="my-3">{{ $user->first_name . ' ' . $user->last_name }}</h5>
                            <p class="text-muted mb-1">{{ $user->position }}</p>
                            <p class="text-muted mb-4">{{ $account->email }}</p>
                            {{-- <div class="d-flex justify-content-center mb-2">
                                <button type="button" class="btn btn-primary">Edit</button>
                                <button type="button" class="btn btn-danger ms-1">Report</button>
                            </div> --}}
                        </div>
                    </div>
                    <h5>Reset Password</h5>
                    <form method="POST" action="{{ route('user.reset-pass') }}">
                        @csrf
                        <div class="mb-3">
                            <input type="password" class="form-control" name="old-pass" placeholder="Old Password" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" name="new-pass" placeholder="New Password" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" name="re-new-pass" placeholder="Confirm New Password" required>
                        </div>
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Full name</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $user->last_name . ' ' . $user->first_name }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Email</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $account->email }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Phone</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $user->phone_number }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Address</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $user->address }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Office</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $user->office_name }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Department</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $user->department }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Position</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $user->position }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Working day</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">
                                        @php
                                            $weekday = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                            $arr = explode('|', $user->working_day);
                                            for ($i = 0; $i < count($arr) ; $i++) {
                                                echo $weekday[$arr[$i] - 1];
                                                if($i < count($arr) - 1) echo ' - ';
                                            }

                                        @endphp
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Salary</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $user->salary }}</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
