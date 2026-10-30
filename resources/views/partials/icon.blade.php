@php
    $name = $name ?? 'dot';
    $size = $size ?? 16;
    $stroke = $stroke ?? 1.8;
    $cls = $class ?? '';
@endphp
<svg xmlns="http://www.w3.org/2000/svg" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24"
     fill="none" stroke="currentColor" stroke-width="{{ $stroke }}" stroke-linecap="round" stroke-linejoin="round"
     class="ico {{ $cls }}" aria-hidden="true" style="flex:0 0 auto;vertical-align:-2px">
    @switch($name)
        @case('filter')
            <path d="M3 5h18"/><path d="M6 12h12"/><path d="M10 19h4"/>
            @break
        @case('chevron-up')
            <path d="m6 15 6-6 6 6"/>
            @break
        @case('close')
            <path d="M6 6l12 12"/><path d="M18 6 6 18"/>
            @break
        @case('search')
            <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
            @break
        @case('home')
            <path d="m3 10 9-7 9 7v9a2 2 0 0 1-2 2h-4v-6H9v6H5a2 2 0 0 1-2-2z"/>
            @break
        @case('compass')
            <circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2 5-5 2 2-5z"/>
            @break
        @case('ticket')
            <path d="M3 9V7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2a3 3 0 0 0 0 6v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a3 3 0 0 0 0-6z"/>
            <path d="M12 7v10"/>
            @break
        @case('user')
            <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
            @break
        @case('users')
            <circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M17 5.5a3.5 3.5 0 0 1 0 6.9"/><path d="M18.5 20a6 6 0 0 0-2-4.4"/>
            @break
        @case('map-pin')
            <path d="M12 21s7-6.1 7-11a7 7 0 1 0-14 0c0 4.9 7 11 7 11z"/><circle cx="12" cy="10" r="2.6"/>
            @break
        @case('map')
            <path d="m9 4 6 2 6-2v14l-6 2-6-2-6 2V6z"/><path d="M9 4v14M15 6v14"/>
            @break
        @case('calendar')
            <rect x="3" y="5" width="18" height="16" rx="2.5"/><path d="M8 3v4M16 3v4M3 10h18"/>
            @break
        @case('clock')
            <circle cx="12" cy="12" r="9"/><path d="M12 7.5V12l3.2 2"/>
            @break
        @case('check')
            <path d="m4 12.5 5 5L20 6.5"/>
            @break
        @case('check-circle')
            <circle cx="12" cy="12" r="9"/><path d="m8 12.5 2.6 2.6L16 9.5"/>
            @break
        @case('x')
            <path d="M6 6l12 12M18 6 6 18"/>
            @break
        @case('lock')
            <rect x="4" y="10" width="16" height="11" rx="2.5"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>
            @break
        @case('shield')
            <path d="M12 3l7.5 3v5.5c0 4.4-3.1 8.2-7.5 9.5-4.4-1.3-7.5-5.1-7.5-9.5V6z"/>
            @break
        @case('phone')
            <path d="M5 3.5h3l1.8 4-2.2 1.6a12 12 0 0 0 6.3 6.3l1.6-2.2 4 1.8v3a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 3 5.7 2 2 0 0 1 5 3.5z"/>
            @break
        @case('heart')
            <path d="M12 20s-7.5-4.6-7.5-9.6A4.4 4.4 0 0 1 12 7.4a4.4 4.4 0 0 1 7.5 3c0 5-7.5 9.6-7.5 9.6z"/>
            @break
        @case('star')
            <path d="m12 3.5 2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.9l-5.2 2.7 1-5.8-4.3-4.1 5.9-.9z"/>
            @break
        @case('download')
            <path d="M12 4v11"/><path d="m7.5 11 4.5 4.5 4.5-4.5"/><path d="M4 19h16"/>
            @break
        @case('upload')
            <path d="M12 19V8"/><path d="m7.5 12 4.5-4.5 4.5 4.5"/><path d="M4 5h16"/>
            @break
        @case('cloud-up')
            <path d="M6.5 17a4 4 0 0 1 .6-8 5.5 5.5 0 0 1 10.4 1.4A3.5 3.5 0 0 1 17.5 17"/><path d="M12 21v-7"/><path d="m9 16.5 3-3 3 3"/>
            @break
        @case('print')
            <path d="M7 9V4h10v5"/><rect x="4" y="9" width="16" height="7" rx="2"/><path d="M7 16h10v4H7z"/>
            @break
        @case('copy')
            <rect x="9" y="9" width="11" height="11" rx="2"/><path d="M15 5.5A2.5 2.5 0 0 0 12.5 3H6a2 2 0 0 0-2 2v8"/>
            @break
        @case('edit')
            <path d="M4 20h4l10-10-4-4L4 16z"/><path d="m14 6 4 4"/>
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14"/>
            @break
        @case('minus')
            <path d="M5 12h14"/>
            @break
        @case('trash')
            <path d="M4 7h16"/><path d="M9 7V4h6v3"/><path d="M6 7l1 13h10l1-13"/>
            @break
        @case('logout')
            <path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3"/><path d="M10 8l-4 4 4 4"/><path d="M6 12h9"/>
            @break
        @case('grid')
            <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>
            @break
        @case('list')
            <path d="M8 6h13M8 12h13M8 18h13"/><path d="M3.5 6h.01M3.5 12h.01M3.5 18h.01"/>
            @break
        @case('chart')
            <path d="M4 20V4"/><path d="M4 20h16"/><rect x="7" y="12" width="3" height="5"/><rect x="12" y="8" width="3" height="9"/><rect x="17" y="5" width="3" height="12"/>
            @break
        @case('file')
            <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/>
            @break
        @case('bell')
            <path d="M18 15V10a6 6 0 1 0-12 0v5l-1.5 3h15z"/><path d="M10 21a2 2 0 0 0 4 0"/>
            @break
        @case('card')
            <rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3 10h18"/>
            @break
        @case('settings')
            <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-2.9 1.2 2 2 0 1 1-4 0 1.7 1.7 0 0 0-2.9-1.2l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1A1.7 1.7 0 0 0 4.6 15a2 2 0 1 1 0-4 1.7 1.7 0 0 0 1.4-2.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1A1.7 1.7 0 0 0 12 4.6a2 2 0 1 1 4 0 1.7 1.7 0 0 0 2.9 1.4l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1A1.7 1.7 0 0 0 19.4 11a2 2 0 1 1 0 4z"/>
            @break
        @case('tag')
            <path d="M12.6 3H20a1 1 0 0 1 1 1v7.4a2 2 0 0 1-.6 1.4l-7.6 7.6a2 2 0 0 1-2.8 0l-6.4-6.4a2 2 0 0 1 0-2.8l7.6-7.6a2 2 0 0 1 1.4-.6z"/><circle cx="16.5" cy="7.5" r="1.4"/>
            @break
        @case('camera')
            <path d="M4 8h3l1.5-2h7L17 8h3a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/><circle cx="12" cy="13" r="3.4"/>
            @break
        @case('chat')
            <path d="M21 12a8 8 0 0 1-8 8H8l-4 3v-4.5A8 8 0 0 1 13 4a8 8 0 0 1 8 8z"/>
            @break
        @case('plane')
            <path d="M10.5 13.5 3 11l18-7-7 18-2.5-7.5z"/>
            @break
        @case('alert')
            <path d="M12 4.5 21 20H3z"/><path d="M12 10v4.5M12 17.5h.01"/>
            @break
        @case('info')
            <circle cx="12" cy="12" r="9"/><path d="M12 11v5.5M12 7.7h.01"/>
            @break
        @case('refresh')
            <path d="M20 11a8 8 0 1 0-2.3 6.3"/><path d="M20 4v7h-7"/>
            @break
        @case('arrow-right')
            <path d="M4 12h15"/><path d="m13 6 6 6-6 6"/>
            @break
        @case('arrow-left')
            <path d="M20 12H5"/><path d="m11 18-6-6 6-6"/>
            @break
        @case('chevron-down')
            <path d="m6 9 6 6 6-6"/>
            @break
        @case('paperclip')
            <path d="M20 11.5 12 19.5a5 5 0 0 1-7-7l8-8a3.5 3.5 0 0 1 5 5l-8 8a2 2 0 0 1-3-3l7-7"/>
            @break
        @case('sparkle')
            <path d="M12 3.5 13.8 9l5.5 1.8-5.5 1.8L12 18l-1.8-5.4L4.7 10.8 10.2 9z"/>
            @break
        @case('bus')
            <rect x="4" y="4" width="16" height="13" rx="2.5"/><path d="M4 11h16"/><path d="M7.5 20v-2M16.5 20v-2"/><circle cx="8" cy="14" r=".8"/><circle cx="16" cy="14" r=".8"/>
            @break
        @default
            <circle cx="12" cy="12" r="3"/>
    @endswitch
</svg>
