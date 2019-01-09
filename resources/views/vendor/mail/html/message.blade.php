@component('mail::layout')
    {{-- One Click Action --}}
    @isset($oneclickaction)
        @slot('oneclickaction')
            @component('mail::oneclickaction', ['url' => $oneclickaction['url'], 'description' => $oneclickaction['description']])
                {{ $oneclickaction['body'] }}
            @endcomponent
        @endslot
    @endisset

    {{-- Preheader --}}
    @isset($preheader)
        @slot('preheader')
            @component('mail::preheader')
                {{ $preheader }}
            @endcomponent
        @endslot
    @endisset

    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            &copy; {{ date('Y') }}, Netgruppen på {{ config('app.name') }}.
        @endcomponent
    @endslot
@endcomponent