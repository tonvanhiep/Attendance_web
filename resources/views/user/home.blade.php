@extends('user.layout')

@section('title')
    User - Home
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/user/style.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-4">
        {{-- <div class="row g-3 my-2">
            <div class="col-md-3">
                <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                    <div>
                        <h3 class="fs-2">720</h3>
                        <p class="fs-5">Products</p>
                    </div>
                    <i class="fas fa-gift fs-1 primary-text border rounded-full secondary-bg p-3"></i>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                    <div>
                        <h3 class="fs-2">4920</h3>
                        <p class="fs-5">Sales</p>
                    </div>
                    <i class="fas fa-hand-holding-usd fs-1 primary-text border rounded-full secondary-bg p-3"></i>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                    <div>
                        <h3 class="fs-2">3899</h3>
                        <p class="fs-5">Delivery</p>
                    </div>
                    <i class="fas fa-truck fs-1 primary-text border rounded-full secondary-bg p-3"></i>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                    <div>
                        <h3 class="fs-2">%25</h3>
                        <p class="fs-5">Increase</p>
                    </div>
                    <i class="fas fa-chart-line fs-1 primary-text border rounded-full secondary-bg p-3"></i>
                </div>
            </div>
        </div> --}}
        <form  method="GET" class=" my-2">
            @csrf
            <br>
            <div class="row p-2">
                <div class="container">
                    <div class="form-group row">
                        <label for="date" class="col-form-label col-sm-1">From</label>
                        <div class="col-sm-3">
                            <input type="date" name="from" id="from" class="form-control input-sm"
                                value="{{ $condition['from'] }}" max="{{ $condition['today'] }}">
                        </div>
                        <label for="date" class="col-form-label col-sm-1">To</label>
                        <div class="col-sm-3">
                            <input type="date" name="to" id="to" class="form-control input-sm"
                                value="{{ $condition['to'] }}" max="{{ $condition['today'] }}">
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-primary" name="seach" title="search">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="row my-5">
            <h3 class="fs-4 mb-3">TIMESHEET</h3>
            <div class="col">
                <table class="table bg-white rounded shadow-sm  table-hover">
                    <thead>
                        <tr>
                            {{-- <th scope="col" width="50">Month</th> --}}
                            <th scope="col">Present</th>
                            <th scope="col">Late</th>
                            <th scope="col">Early</th>
                            <th scope="col">Off</th>
                            <th scope="col">Total {{ $list['working_day'] }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $list['present'] }}</td>
                            <td>{{ $list['late'] }}</td>
                            <td>{{ $list['early'] }}</td>
                            <td>{{ $list['off'] }}</td>
                            <td>{{ $list['total'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
