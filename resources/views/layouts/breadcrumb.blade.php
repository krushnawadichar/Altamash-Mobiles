@if (isset($breadcrumbs) && is_array($breadcrumbs))
    <nav aria-label="breadcrumb" class="py-2">
        <ol class="breadcrumb mb-0">
            @foreach ($breadcrumbs as $breadcrumb)
                @if ($loop->last)
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $breadcrumb['label'] }}
                    </li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{ $breadcrumb['url'] ?? '#' }}" class="text-decoration-none">
                            {{ $breadcrumb['label'] }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
@else
    <nav aria-label="breadcrumb" class="py-2">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <i class="fas fa-home me-1"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                @yield('page-title', 'Page')
            </li>
        </ol>
    </nav>
@endif