@extends('user.layout')

@section('title')
    Attendance detail
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/user/style.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-4">
        <form action="{{ route('user.attendance.detail') }}" method="GET" class="my-2">
            <br>
            <div class="row p-2">
                <div class="container">
                    <div class="form-group row">
                        <label for="date" class="col-form-label col-sm-1">Date</label>
                        <div class="col-sm-3">
                            <input type="date" name="date" id="date" class="form-control input-sm"value="{{ $condition['from'] }}" max="{{ $condition['today'] }}">
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-primary"title="search">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="row my-5">
            <div id="content">
                <div class="col" style="float: none;">
                    <table class="table bg-white rounded shadow-sm  table-hover">
                        <thead>
                            <tr>
                                <td scope="col">Time</td>
                                <td scope="col">Timekeeper</td>
                                <td scope="col">Office</td>
                                <td scope="col">Result Recognition</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($list as $item)
                                @php $dt = new DateTime($item->timekeeping_at); @endphp
                                <tr>
                                    <td class="time">
                                        <p class="fw-bold">{{ $dt->format('H:i:s') }}</p>
                                    </td>
                                    <td class="timekeeper">
                                        <p>{{ $item->timekeeper_name }}</p>
                                    </td>
                                    <td class="office">
                                        <p>{{ $item->office_name }}</p>
                                    </td>
                                    <td class="result">
                                        <a href="{{ asset('storage/image-checkin/' . $item->face_image) }}"> Image</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
