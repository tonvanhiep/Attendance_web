@push('css')

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
