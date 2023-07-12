@extends('admin.layout')


@section('title')
    Add Staff
@endsection


@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/add.css') }}">
    <style>
        .img-preview {
            max-width: 200px;
            margin: 5px;
        }

        .dropdown-toggle::after {
            content: none;
        }
    </style>
@endpush


@section('content')
    <style>
        canvas {
            position: absolute;
            top: 0;
            left: 0;
        }
    </style>
    <p id="url-face-api" hidden>{{ asset('assets/face-api') }}</p>
    <div class="modal" id="myModal">
        <div class="modal-dialog"
            style="
        min-width: 500px;
        width: 50% !important;
        max-width: 1000px;">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Upload face recognition image</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-footer" style="display: block">
                    <div class="input-group" style="margin-bottom: 15px">
                        <input form="form-user-info" type="file" id="inp-face" name="face[]" accept="img/*"
                            class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" multiple
                            aria-label="Upload">
                    </div>
                    <div>
                        <div style="display: flex; justify-content: center;">
                            <p hidden id="processing-noti">Processing...</p>
                        </div>
                        <div hidden id="div-alert-error" class="alert alert-danger" role="alert"></div>
                    </div>
                    <div style="display: flex; flex-wrap:wrap; margin-bottom:30px">
                        <div id="div-face-upload" style="display: flex; flex-wrap:wrap"></div>
                    </div>
                    <div style="display: flex; justify-content: center;">
                        <button type="button" class="btn btn-success" style="min-width: 50%;"
                            data-bs-dismiss="modal">OK</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal" id="myModal2">
        <div class="modal-dialog"
            style="
        min-width: fit-content;
        width: 50% !important;
        max-width: 1000px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Face scan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-footer" style="display: block">
                    <div id="div-scan"
                        style="display: flex; justify-content: center; align-items: center; flex-direction:column">
                        <div style="height: 40px">
                            <h5 id="action-name" style="height: 25px"></h5>
                        </div>

                        <div id="webcam" style="width: fit-content; height: fit-content; position: relative;">
                            <video id="video" width="720" height="560" autoplay muted
                                style="border: solid; border-radius: 1000px;"></video>
                            {{-- <h2 id="text-loading">Loading...</h2> --}}
                        </div>

                        <div style="display: flex; flex-wrap:wrap; margin-bottom:30px; max-width: 50vw">
                            <div id="div-face-scan" style="display: flex; flex-wrap:wrap; justify-content: center;">
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-around; margin-top:30px">
                        <button id="btn-start" type="button" class="btn btn-success" style="min-width: 45%;">Start</button>
                        <button id="btn-done" type="button" class="btn btn-warning" data-bs-dismiss="modal"
                            style="min-width: 45%;">Done</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h3 class="i-name">Staff List / Add New Staff</h3>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" style="margin: 30px;">>
            {{ session('success') }}
            <a href="#" class="close" data-bs-dismiss="alert" aria-label="close">
                <i class="fa-solid fa-x"></i>
            </a>
        </div>
    @endif

    @if (count($errors) > 0)
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger alert-dismissible" style="margin: 30px;">>
                {{ $error }}
                <a href="#" class="close" data-bs-dismiss="alert" aria-label="close">
                    <i class="fa-solid fa-x"></i>
                </a>
            </div>
        @endforeach
    @endif

    <form id="form-user-info" class="board" action="{{ route('admin.staff.store') }}" method="post"
        enctype="multipart/form-data">
        @csrf
        <h4>Add New Staff</h4>
        <div class="input-container">
            <div class="container-top" style="display: flex; flex-wrap: wrap;">
                <div class="board-left" style="max-width: 800px">
                    <div class="long form">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" required>
                    </div>
                    <div class="long form">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" required>
                    </div>

                    <div class="long form">
                        <label for="numberphone">Phone</label>
                        <input type="text" name="numberphone" required>
                    </div>
                    <div class="long form">
                        <label for="address">Address</label>
                        <input type="text" name="address" required>
                    </div>
                    <div class="long form">
                        <label for="birth_day">Date of birth</label>
                        <input type="date" name="birth_day" required>
                    </div>
                    <div class="form">
                        <label>Gender</label>
                        <div>
                            <label for="male">Male</label>
                            <input type="radio" name="gender" id="male" value="1"
                                {{ old('gender') === '1' ? 'checked' : '' }}>
                        </div>
                        <div>
                            <label for="female">Female</label>
                            <input type="radio" name="gender" id="female" value="0"
                                {{ old('gender') === '0' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="long form">
                        <label for="department">Department</label>
                        <input type="text" name="department" required>
                    </div>
                    <div class="long form">
                        <label for="position">Position</label>
                        <input type="text" name="position" required>
                    </div>
                    <div class="long form">
                        <label for="office_id">Office</label>
                        {{-- <input type="number" name="office_id"> --}}
                        <select name="office_id"
                            style="
                            flex: 1;
                            display: inline-block;
                            padding: 5px 10px;
                            border-radius: 5px;
                            border: 1px solid #6F6F6F;
                        ">
                            @foreach ($office as $item)
                                <option value="{{ $item->id }}">{{ $item->office_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="long form">
                        <label for="working_day">Working day</label>
                        <input type="checkbox" name="working_day[]" value="2">Mon
                        <input type="checkbox" name="working_day[]" value="3">Tue
                        <input type="checkbox" name="working_day[]" value="4">Wed
                        <input type="checkbox" name="working_day[]" value="5">Thu
                        <input type="checkbox" name="working_day[]" value="6">Fri
                        <input type="checkbox" name="working_day[]" value="7">Sat
                        <input type="checkbox" name="working_day[]" value="1">Sun
                    </div>
                    <div class="long form" >
                        <label for="start-time">Start time</label>
                        <input type="time" name="start_time" required>
                    </div>
                    <div class="long form" >
                        <label for="end-time">End time</label>
                        <input type="time" name="end_time" required>
                    </div>
                </div>
                <div class="board-right">
                    @push('css')
                        <style>
                            #file-input {
                                display: none;
                            }

                            .preview {
                                padding: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                flex-direction: column;
                                width: 100%;
                                max-width: 350px;
                                margin: auto;
                                background-color: rgb(255, 255, 255);
                                box-shadow: 0 0 20px rgba(170, 170, 170, 0.2);
                            }

                            img {
                                width: 100%;
                                object-fit: cover;
                            }
                        </style>
                    @endpush
                    <div class="short form">
                        <div class="preview">
                            <img id="img-preview"
                                src="https://www.shareicon.net/data/512x512/2017/01/06/868320_people_512x512.png" />
                        </div>
                    </div>
                    <div class="short form">
                        <label for="file-input">Avatar</label>
                        <input type="file" name="avatar" accept="img/*" id="file-input">
                    </div>

                    <div class="short form">
                        <label for="name">User Name</label>
                        <input type="text" name="name">
                    </div>
                    <div class="form">
                        <label>Role</label>
                        <div>
                            <label for="admin">Admin</label>
                            <input type="radio" name="fl_admin" id="admin" value="1"
                                {{ old('fl_admin') === '1' ? 'checked' : '' }}>
                        </div>
                        <div>
                            <label for="user">User</label>
                            <input type="radio" name="fl_admin" id="user" value="0"
                                {{ old('fl_admin') === '0' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="short form">
                        <label for="email">Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="short form">
                        <label for="password">Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="short form">
                        <label for="confirm">Confirm Password</label>
                        <input type="password" name="confirm" required>
                    </div>
                </div>
            </div>
            <h5 style="margin-bottom: 15px">About Contract</h5>
            <div class="container-top" style="display: flex; flex-wrap: wrap;">
                <div class="board-left">
                    <div class="row">
                        <div class="form">
                            <label for="join_day">Join day</label>
                            <input type="date" name="join_day" required>
                        </div>
                    </div>
                </div>
                <div class="board-right">
                    <div class="short form">
                        <label for="salary">Salary</label>
                        <input type="number" name="salary" required>
                    </div>
                </div>
            </div>

            <div style="margin-bottom:10px; display: flex; justify-content: space-between; flex-wrap: wrap;">
                <h5 style="margin-bottom: 15px; display:inline">Face Recognition</h5>
                <div class="dropdown">
                        <button style="background-color: #323FAE" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-plus" style="padding-right: 10px"></i>Add Image
                        </button>
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#myModal">Upload image</button></li>
                            <li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#myModal2">Face scan</button></li>
                        </ul>
                </div>
                <div hidden id="arr-detection"></div>
            </div>
            <div class="container-top" style="display: block">
                <div style="display: flex; flex-wrap:wrap">
                    <div id="div-face-available" style="display: flex; flex-wrap:wrap">
                        {{-- @foreach ($list as $item)
                            <img src="{{ asset($item->image_url) }}" class="rounded img-preview" alt="...">
                        @endforeach --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="d-grid gap-2">
            <button style="margin:auto; min-width: 50%; background-color:#323FAE" type="button" id="btn-submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk" style="padding-right: 10px"></i>
                <span>Save</span>
            </button>
        </div>
    </form>
@endsection


@push('js')
    <script defer src="{{ asset('assets/face-api/face-api.min.js') }}"></script>
    <script defer type="module" src="{{ asset('assets/face-api/edit-staff.js') }}"></script>
    {{-- <script>
        const input = document.getElementById('file-input');
        const image = document.getElementById('img-preview');

        input.addEventListener('change', (e) => {
            if (e.target.files.length) {
                const src = URL.createObjectURL(e.target.files[0]);
                image.src = src;
            }
        });

        const ipnFileElement = document.getElementById('inp-face')
        const resultElement = document.getElementById('div-face-upload')
        // const validImageTypes = ['image/gif', 'image/jpeg', 'image/png']

        ipnFileElement.addEventListener('change', function(e) {
            const files = e.target.files
            resultElement.innerHTML = ''
            for (let i = 0; i < files.length; i++) {
                const file = files[i]
                const fileType = file['type']

                const fileReader = new FileReader()
                fileReader.readAsDataURL(file)

                fileReader.onload = function() {
                    const url = fileReader.result
                    resultElement.insertAdjacentHTML(
                        'beforeend',
                        `<img src="${url}" alt="${file.name}" class="rounded img-preview" />`
                    )}
            }
        })
    </script> --}}
@endpush
