@extends('admin.layout')


@section('title')
    Attendance Detail
@endsection


@push('css')
    <link rel="stylesheet" href="{{asset('assets/css/report.css');}}">
    <link rel="stylesheet" href="{{asset('assets/css/attendance.css');}}">
@endpush


@section('content')
    <h3 class="i-name"> </h3>

    <div id="content" class="container" style="padding-bottom: 20px">
        <div class="row"  id="employee-info">
            <div class="col-6">
                <div class="row">
                    <div class="col">
                        <label>ID: </label><p>{{ $detail[0]->id }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label>First name:</label><p class="fw-bold">{{ $detail[0]->first_name }}</p>
                    </div>
                    <div class="col">
                        <label>Last name:</label><p class="fw-bold">{{ $detail[0]->last_name }}</p>
                    </div>
                    <div class="col">
                        <label>Gender: </label>
                        @switch($detail[0]->gender)
                            @case(1)
                                <p>Male</p>
                                @break
                            @case(0)
                                <p>Female</p>
                                @break
                            @default
                                <p>Other</p>
                        @endswitch
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label>Office: </label><p>{{ $detail[0]->office_name }}</p>
                    </div>
                    <div class="col">
                        <label>Department: </label><p>{{ $detail[0]->department }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label>Timkeeper: </label><p>{{ $detail[0]->timekeeper_name }}</p>
                    </div>
                    <div class="col">
                        <label>Timkeeping at: </label><p>{{ $detail[0]->timekeeping_at }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label>Attendance status: </label>
                        @switch($detail[0]->status)
                            @case(1)
                                <p class="fw-bold">Successful confirmation</p>
                                @break
                            @case(2)
                                <p class="fw-bold" style="color: #ffc107">Waiting for confirmation</p>
                                @break
                            @case(3)
                                <p class="fw-bold" style="color: #dc3545">Confirm failure</p>
                                @break
                        @endswitch
                    </div>
                </div>

            </div>
            <div class="col-4">
                <img style="max-width: 100%; height: auto;" src="{{ asset($detail[0]->avatar) }}" alt="avatar-{{ $detail[0]->first_name . $detail[0]->last_name }}">
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-6">
                <p>Image Check-In:</p>
                <img style="max-width: 100%; height: auto;" src="{{ asset('storage/image-checkin/') . '/' . $detail[0]->face_image }}" alt="image-checkin-{{ $detail[0]->first_name . $detail[0]->last_name }}">
            </div>
            <div class="col-6">
                <p>Image Recogination:</p>
                <img style="max-width: 100%; height: auto;" src="{{ asset($detail[0]->image_url) }}" alt="image-recogination-{{ $detail[0]->first_name . $detail[0]->last_name }}">
            </div>
        </div>

        <hr>
        <p hidden id="link-update">{{ route('admin.attendance.updateStatus') }}</p>

        @if ($detail[0]->status == 2)
            <div class="row">
                <label>Attendance Confirm:</label>
                <div class="col-10">
                    <select id="option-status" class="form-select" aria-label="Default select example">
                        <option selected value="1">Accept</option>
                        <option value="3">Reject</option>
                    </select>
                </div>
                <div class="col-2">
                    <button id="status-btn" type="button" class="btn btn-success">Save</button>
                </div>
            </div>

        @else
            <div class="row">
                <label>Change status:</label>
                <div class="col-10">
                    <select id="option-status" class="form-select" aria-label="Default select example">
                        <option @if ($detail[0]->status == 1) selected @endif value="1">Accept</option>
                        <option @if ($detail[0]->status == 3) selected @endif value="3">Reject</option>
                    </select>
                </div>
                <div class="col-2">
                    <button id="status-btn" type="button" class="btn btn-warning" disabled>Change</button>
                </div>
            </div>
        @endif

    </div>

@endsection


@push('js')
    <script>
        document.getElementById('option-status').onchange = function() {
            document.getElementById('status-btn').disabled = false;
        }

        document.getElementById('status-btn').onclick = function() {
            var e = document.getElementById("option-status");
            $.ajax ({
                type: 'POST',
                cache: false,
                url: document.getElementById('link-update').textContent,
                data: {
                    "id": window.location.pathname.slice(25),
                    "status": e.value
                },
                success: function(data) {
                    //document.getElementById('content').innerHTML = data;
                    alert('Update successfull')
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                },
                error: function(data) {
                    alert('Update fail')
                },
            });

        }
    </script>
@endpush
