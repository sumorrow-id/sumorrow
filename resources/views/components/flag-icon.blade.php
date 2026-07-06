@props(['code', 'size' => 'w-5 h-3.5'])

@if ($code === 'en')
    <svg viewBox="0 0 60 40" {{ $attributes->class(['rounded-[2px] shrink-0', $size]) }} aria-hidden="true">
        <rect width="60" height="40" fill="#B22234" />
        <g fill="#fff">
            <rect y="3.08" width="60" height="3.08" />
            <rect y="9.23" width="60" height="3.08" />
            <rect y="15.38" width="60" height="3.08" />
            <rect y="21.54" width="60" height="3.08" />
            <rect y="27.69" width="60" height="3.08" />
            <rect y="33.85" width="60" height="3.08" />
        </g>
        <rect width="24" height="21.54" fill="#3C3B6E" />
    </svg>
@else
    <svg viewBox="0 0 60 40" {{ $attributes->class(['rounded-[2px] shrink-0', $size]) }} aria-hidden="true">
        <rect width="60" height="20" fill="#CE1126" />
        <rect y="20" width="60" height="20" fill="#fff" />
    </svg>
@endif
