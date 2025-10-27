<div class="page-header">
    <h3 class="fw-bold">{{ $title }}</h3>
    <ul class="breadcrumbs">
        <li class="nav-home text-muted">
            <a href="/home">
                <i class="icon-home"></i>
            </a>
        </li>
        @foreach ($breadcrumbs ?? [] as $breadcrumb)
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item {{ $loop->last ? 'fw-bold' : 'text-muted' }}">
            @if (!empty($breadcrumb['url']))
                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
            @else
                {{ $breadcrumb['label'] }}
            @endif
        </li>
        @endforeach
    </ul>
</div>