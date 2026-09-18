@props(['items'])

<nav class="text-xs text-slate-500 flex flex-wrap items-center gap-1.5 mb-4" aria-label="Migas de TRD">
    @foreach($items as $i => $item)
        @if($i > 0)
            <span class="text-slate-300">/</span>
        @endif
        @if(!empty($item['href']))
            <a href="{{ $item['href'] }}" wire:navigate class="hover:text-slate-800">{{ $item['label'] }}</a>
        @else
            <span class="text-slate-800 font-medium">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
