@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'list-unstyled text-danger small mb-0']) }}>
        @foreach ((array) $messages as $message)
            <li class="d-flex align-items-center gap-1">
                <i class="bi bi-exclamation-circle"></i>
                {{ $message }}
            </li>
        @endforeach
    </ul>
@endif
