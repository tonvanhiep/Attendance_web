@extends('admin.layout')


@section('title')
    Add Staff
@endsection


@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/add.css') }}">
@endpush


@section('content')
    <h3 class="i-name">Staff List / Add New Staff</h3>

    <form class="board" action="{{ route('admin.staff.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <h4>Add New Staff</h4>
        <div class="input-container">
            <div class="container-top">
                <div class="board-left">
                    <div class="long form" >
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name">
                    </div>
                    <div class="long form" >
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name">
                    </div>

                    <div  class="long form" >
                        <label for="numberphone">Phone</label>
                        <input type="text" name="numberphone">
                    </div>
                    <div  class="long form" >
                        <label for="address">Address</label>
                        <input type="text" name="address">
                    </div>
                    <div class="long form" >
                        <label for="birth_day">Date of birth</label>
                        <input type="date" name="birth_day">
                    </div>
                    <div class="form">
                        <label>Gender</label>
                        <div>
                            <label for="male">Male</label>
                            <input type="radio" name="gender" id="male" value="1" {{ old('gender') === '1' ? 'checked' : '' }}>
                        </div>
                        <div>
                            <label for="female">Female</label>
                            <input type="radio" name="gender" id="female" value="0" {{ old('gender') === '0' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="long form" >
                        <label for="department">Department</label>
                        <input type="text" name="department">
                    </div>
                    <div class="long form" >
                        <label for="position">Position</label>
                        <input type="text" name="position">
                    </div>
                    <div class="long form" >
                        <label for="office_id">Office</label>
                        {{-- <input type="number" name="office_id"> --}}
                        <select name="office_id" style="
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
                    <div class="long form" >
                        <label for="working_day">Working_day</label>
                        <input type="checkbox" name="working_day[]" value="2">Mon
                        <input type="checkbox" name="working_day[]" value="3">Tue
                        <input type="checkbox" name="working_day[]" value="4">Wed
                        <input type="checkbox" name="working_day[]" value="5">Thu
                        <input type="checkbox" name="working_day[]" value="6">Fri
                        <input type="checkbox" name="working_day[]" value="7">Sat
                        <input type="checkbox" name="working_day[]" value="1">Sun
                    </div>
                    {{-- <div class="long form" >
                        <label for="note">Content</label>
                        <textarea id="editor" name="note" rows="10"></textarea>
                    </div> --}}
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
                            <img id="img-preview" src="https://www.shareicon.net/data/512x512/2017/01/06/868320_people_512x512.png" />
                        </div>
                    </div>
                    <div class="short form" >
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
                            <input type="radio" name="fl_admin" id="admin" value="1" {{ old('fl_admin') === '1' ? 'checked' : '' }}>
                        </div>
                        <div>
                            <label for="user">User</label>
                            <input type="radio" name="fl_admin" id="user" value="0" {{ old('fl_admin') === '0' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="short form" >
                        <label for="email">Email</label>
                        <input type="email" name="email">
                    </div>
                    <div class="short form">
                        <label for="password">Password</label>
                        <input type="text" name="password">
                    </div>
                    <div class="short form">
                        <label for="confirm">Confirm Password</label>
                        <input type="text" name="confirm">
                    </div>
                </div>
            </div>
            <h5 style="margin-bottom: 15px">About Contract</h5>
            <div class="container-top">
                <div class="board-left">
                    <div class="row">
                        <div class="form">
                            <label for="join_day">Join day</label>
                            <input type="date" name="join_day" >
                        </div>
                    </div>
                </div>
                <div class="board-right">
                    <div class="short form">
                        <label for="salary">Salary</label>
                        <input type="number" name="salary">
                    </div>
                </div>
            </div>

            <h5 style="margin-bottom: 15px">Face Recognition</h5>
            <div class="container-top" style="display: block">
                <div class="input-group" style="margin-bottom: 15px">
                    <input type="file" id="inp-face" name="image_url[]" accept="img/*" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" multiple aria-label="Upload">
                </div>

                <style>
                    .img-preview {
                        max-width: 200px;
                        margin: 5px;
                    }
                </style>
                <div style="display: flex; flex-wrap:wrap">
                    <div id="div-face-upload" style="display: flex; flex-wrap:wrap"></div>
                </div>
            </div>
        </div>
        <div class="d-grid gap-2">
            <button style="margin:auto; min-width: 50%; background-color:#323FAE" type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk" style="padding-right: 10px"></i>
                <span>Save</span>
            </button>
        </div>
    </form>

    @if (session('Success'))
        {{ session('Success') }}
    @endif

    @if (count($errors) > 0)
        @foreach ($errors->all() as $error)
            {{ $error }} <br>
        @endforeach
    @endif
@endsection

{{-- @section('scripts')
    <script>
        ClassicEditor
            .create( document.querySelector( '#editor' ) )
            .catch( error => {
                console.error( error );
            } );
    </script>
@endsection --}}
@push('js')
    <script>
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
    </script>
@endpush
