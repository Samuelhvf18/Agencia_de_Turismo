<nav class="space-y-1 text-xs font-semibold">
    @php
        $links = [
            [
                'route'  => 'guia.mis-tours',
                'icon'   => 'fa-map-marked-alt',
                'label'  => 'Mis tours asignados',
                'active' => request()->routeIs('guia.*'),
                'roles'  => ['guia'],
            ],

            [
                'route'  => 'chofer.mis-viajes',
                'icon'   => 'fa-road',
                'label'  => 'Mis viajes y salidas',
                'active' => request()->routeIs('chofer.*'),
                'roles'  => ['chofer'],
            ],
        ];
    @endphp

    @foreach($links as $link)
        @role($link['roles'])
            <a href="{{ route($link['route']) }}" 
                class="flex items-center gap-3 min-h-[44px] px-3 py-2.5 rounded-xl border transition-all duration-200 {{ $link['active'] ? 'bg-gradient-to-r from-[#C96F4A] to-[#b85c3b] text-white border-white/10 shadow-md' : 'text-white/80 border-transparent hover:bg-white/10 hover:text-white hover:translate-x-0.5' }}">
                <i class="fa-solid {{ $link['icon'] }} w-4 text-center text-sm {{ $link['active'] ? '' : 'opacity-90' }}"></i>
                <span>{{ $link['label'] }}</span>
            </a>
        @endrole
    @endforeach
</nav>