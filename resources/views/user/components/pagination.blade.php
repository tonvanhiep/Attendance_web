@push('css')
    <style>
        html, body{
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
            color: #222;
        }

        a{
            text-decoration: none;
        }

        p, li, a{
            font-size: 14px;
        }

        /* GRID */

        .twelve { width: 100%; }
        .eleven { width: 91.53%; }
        .ten { width: 83.06%; }
        .nine { width: 74.6%; }
        .eight { width: 66.13%; }
        .seven { width: 57.66%; }
        .six { width: 49.2%; }
        .five { width: 40.73%; }
        .four { width: 32.26%; }
        .three { width: 23.8%; }
        .two { width: 15.33%; }
        .one { width: 6.866%; }

        /* COLUMNS */

        .col {
            display: block;
            float:left;
            margin: 1% 0 1% 1.6%;
        }

        .col:first-of-type {
            margin-left: 0;
        }

        .container{
            width: 100%;
            max-width: 940px;
            margin: 0 auto;
            position: relative;
            text-align: center;
        }

        /* CLEARFIX */

        .cf:before,
        .cf:after {
            content: " ";
            display: table;
        }

        .cf:after {
            clear: both;
        }

        .cf {
            *zoom: 1;
        }

        /* GENERAL STYLES */

        .pagination{
            padding: 0 0 20px 0;
        }

        .pagination ul{
            margin: 0;
            padding: 0;
            list-style-type: none;
        }

        .pagination a{
            display: inline-block;
            padding: 10px 18px;
            color: #222;
        }

        /* TWO */

        .p2 .is-active li{
            font-weight: bold;
            border-bottom: 3px solid #1E2774;
        }
    </style>
@endpush


<div class="container">
    <div class="pagination p2">
        <ul>
            @if ($pagination['currentPage'] >= 3)
                <a onclick="pagination(1);">
                    <li>&laquo;</li>
                </a>
            @endif

            @if ($pagination['currentPage'] >= 2)
                <a onclick="pagination({{ $pagination['currentPage'] - 1 }})">
                    <li>&lsaquo;</li>
                </a>
            @endif

            @for ($i = 1; $i <= $pagination['lastPage']; $i++)
                @if (($i > 3 && $i < $pagination['currentPage'] - 2) || ($i > $pagination['currentPage'] + 2 && $i < $pagination['lastPage'] - 2))
                        <a>&hellip;</a>

                    @php
                        if ($i > 2 && $i < $pagination['currentPage'] - 1) {
                            $i = $pagination['currentPage'] - 2;
                        }
                        else if ($i > $pagination['currentPage'] + 1 && $i < $pagination['lastPage'] - 1) {
                            $i = $pagination['lastPage'] - 2;
                        }
                    @endphp
                @else
                    <a @if ($i == $pagination['currentPage']) class="is-active" @endif onclick="pagination({{ $i }});">
                        <li>{{ $i }}</li>
                    </a>
                @endif

            @endfor

            @if ($pagination['currentPage'] < $pagination['lastPage'])
                <a onclick="pagination({{ $pagination['currentPage'] + 1 }});">
                    <li>&rsaquo;</li>
                </a>
            @endif

            @if ($pagination['currentPage'] < $pagination['lastPage'] - 1)
                <a onclick="pagination({{ $pagination['lastPage'] }});">
                    <li>&raquo;</li>
                </a>
            @endif
        </ul>
    </div>
</div>
