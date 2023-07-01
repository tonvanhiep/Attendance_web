@extends('user.layout')

@section('title')
    Chat
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/user/style.css') }}">
    <style>
        #chat3 .form-control {
            border-color: transparent;
        }

        #chat3 .form-control:focus {
            border-color: transparent;
            box-shadow: inset 0px 0px 0px 1px transparent;
        }

        .badge-dot {
            border-radius: 50%;
            height: 10px;
            width: 10px;
            margin-left: 2.9rem;
            margin-top: -.75rem;
        }

        body{
            overflow: hidden;
        }

        .container {
            text-align: initial !important;
        }

        p {
            font-size: 16px !important;
        }

        .overflow-auto::-webkit-scrollbar-track
        {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            border-radius: 5px;
            background-color:lightgray;
        }

        .overflow-auto::-webkit-scrollbar
        {
            width: 6px;
            background-color:lightgray;
        }

        .overflow-auto::-webkit-scrollbar-thumb
        {
            border-radius: 5px;
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
            background-color:grey;
        }

        textarea::-webkit-scrollbar{
            display: none;
        }

        #btn-send-message {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
<section style="width: 100%;height: 91vh;">
    <div class="container" style="
            height: 100%;
            margin: 0;
            width: 100% !important;
            padding-top: 0px !important;
            max-width: none;">
        <div class="row" style="
                width: 100%;
                height: 100%;">
            <div class="col-md-12" style="width: 100%;height: 100%;padding: 0;">
            <div class="card" id="chat3" style="background: none;border-radius: 0px;height: 100%;width: 100%;border: none;">
                <div class="card-body" style="width: 100%;height: 100%;padding: 0;">

                    <div class="row" style="
                            width: 100%;
                            height: 100%;
                            padding-bottom: 12px;">

                        <div class="col-md-6 col-lg-5 col-xl-4 mb-4 mb-md-0" style="
                            height: 100%;
                            border-right: 1px solid mediumseagreen;">
                            <div class="p-3" style="height: 100%;padding-top: 0px !important;">
                                <div class="input-group rounded mb-3">
                                    <button hidden id="btn-return-search" style="background: none; border:none"><i class="fa-solid fa-arrow-left"></i></button>
                                    <div id="div-search" class="input-group rounded" style="background-color: white;" width="90%">
                                        <input id="inp-search" type="search" class="form-control rounded" placeholder="Search" aria-label="Search" aria-describedby="search-addon">
                                        <span class="input-group-text border-0" id="search-addon">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                </div>

                                <div id="div-list-message" data-mdb-perfect-scrollbar="true" style="height: calc(100% - 54px);" class="overflow-auto">
                                    <ul id="list-message-group" class="list-unstyled mb-0">
                                        @foreach ($listMessages as $item)
                                            <li class="p-2 border-bottom message-group">
                                                <a href="{{ route('user.chat.index', ['id' => $item->id_receiver]) }}" class="d-flex justify-content-between">
                                                    <div class="d-flex flex-row">
                                                        <div>
                                                            <img src="{{ asset($item->avatar_group) }}" alt="avatar" class="d-flex align-self-center me-3" style="width: 60px; height:60px; border-radius:100px">
                                                            <span class="badge bg-success badge-dot"></span>
                                                        </div>
                                                        <div class="pt-1">
                                                            <p class="fw-bold mb-0">{{ $item->name_group }}</p>
                                                            @if ($item->id_sender == $user->id)
                                                                <p id="list-mess-group-{{ $item->id_receiver }}" class="small text-muted" style="font-style: italic">
                                                                    You: {{ str_replace('</br>', ' ', $item->content ) }}
                                                                </p>
                                                            @elseif ($item->message_unread > 0)
                                                                <p id="list-mess-group-{{ $item->id_receiver }}" class="small text-muted" style="font-weight: bold">
                                                                    {{ str_replace('</br>', ' ', $item->content ) }}
                                                                </p>
                                                            @else
                                                                <p id="list-mess-group-{{ $item->id_receiver }}" class="small text-muted">
                                                                    {{ str_replace('</br>', ' ', $item->content ) }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="pt-1" id="div-info-group-{{ $item->id_receiver }}">
                                                        <p id="time-mess-group-{{ $item->id_receiver }}" class="small text-muted mb-1 date-time">{{ $item->created_at }}</p>
                                                        @if ($item->message_unread > 0)
                                                            <span class="badge bg-danger rounded-pill float-end" id="mess-unread-group-{{ $item->id_receiver }}">{{$item->message_unread}}</span>
                                                        @endif
                                                    </div>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div hidden id="div-list-search" data-mdb-perfect-scrollbar="true" style="height: calc(100% - 54px);" class="overflow-auto">
                                    <ul id="list-message-search" class="list-unstyled mb-0"></ul>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6 col-lg-7 col-xl-8" style="
                                display: flex;
                                height: 100%;
                                flex-direction: column;
                                align-self: flex-end;">
                            @if (count($messages) > 0 || isset($infoGroup[0]))
                            <div class="d-flex align-items-center pe-3 mb-2" style="justify-content: space-between">
                                <div style="align-items: end;
                                display: flex;">
                                    <img id="group-avatar" style="width: 60px; height: 60px; border-radius:100px" src="{{ count($messages) == 0 ? asset($infoGroup[0]->avatar_group) : asset($messages[0]->avatar_group) }}" alt="avatar 3">
                                    <h4 id="group-name" style="display: inline; padding-left:15px">{{ count($messages) == 0 ? $infoGroup[0]->name_group : $messages[0]->name_group }}</h4>
                                </div>
                                <div>
                                    <i class='fa fa-info-circle' style="width: 40px"></i>
                                </div>
                            </div>

                            <div id="div-chat-box" class="pt-3 pe-3 overflow-auto" data-mdb-perfect-scrollbar="true" style="position: relative; height: 100%;">
                                @foreach ($messages as $item)
                                    @if ($item->id_sender == $user->id)
                                        <div class="d-flex flex-row justify-content-end">
                                            <div style="max-width: 80%; margin-left:auto">
                                                <p class="small p-2 me-3 mb-1 text-white rounded-3 bg-primary">
                                                    @foreach(explode('</br>', $item->content) as $msg)
                                                        {{ $msg }}
                                                        @if(!$loop->last) <br> @endif
                                                    @endforeach
                                                </p>
                                                <p class="small me-3 mb-3 rounded-3 text-muted">{{ $item->created_at }}</p>
                                            </div>
                                            <img class="my-avatar" src="{{ asset($item->avatar) }}" alt="avatar 1" style="width: 45px; height: 45px; border-radius:100px">
                                        </div>
                                    @else
                                        <div class="d-flex flex-row justify-content-start">
                                            <img src="{{ asset($item->avatar) }}" alt="avatar 1" style="width: 45px; height: 45px; border-radius:100px">
                                            <div style="max-width: 80%">
                                                <p class="small p-2 ms-3 mb-1 rounded-3" style="background-color: #f5f6f7;">
                                                    @foreach(explode('</br>', $item->content) as $msg)
                                                        {{ $msg }}
                                                        @if(!$loop->last) <br> @endif
                                                    @endforeach
                                                </p>
                                                <p class="small ms-3 mb-3 rounded-3 text-muted float-end">{{ $item->created_at }}</p>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <div class="text-muted d-flex justify-content-start align-items-center pe-3 pt-3 mt-2">
                                <p hidden id='url-store-message'>{{route('user.chat.store-message')}}</p>
                                <textarea id="content-message" class="form-control" aria-label="With textarea" placeholder="Type message..." style="font-size:16px; resize: none"></textarea>
                                {{-- <a class="ms-1 text-muted" href="#!"><i class="fas fa-paperclip"></i></a>
                                <a class="ms-3 text-muted" href="#!"><i class="fas fa-smile"></i></a> --}}
                                <a class="ms-3" id="btn-send-message"><i class="fas fa-paper-plane"></i></a>
                            </div>

                            @endif

                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</section>
@endsection


@push('js')
    <script src="{{ asset('assets/js/chat.js') }}"></script>
@endpush

