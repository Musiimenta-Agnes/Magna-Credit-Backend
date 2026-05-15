<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;padding:4px 0;">
    @foreach($stats as $stat)
    <div style="background:#fff;border-radius:16px;padding:0;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid #e8f0fe;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;position:relative;"
        onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 12px 28px rgba(0,0,0,0.10)'"
        onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)'">

        {{-- Top accent bar --}}
        <div style="height:4px;background:{{ $stat['color'] }};width:100%;"></div>

        <div style="padding:22px 24px;display:flex;align-items:flex-start;gap:18px;">
            {{-- Icon --}}
            <div style="width:52px;height:52px;border-radius:14px;background:{{ $stat['bg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                <x-dynamic-component :component="'heroicon-o-' . $stat['icon']" style="width:26px;height:26px;color:{{ $stat['color'] }};"/>
            </div>

            {{-- Text --}}
            <div style="flex:1;min-width:0;">
                <p style="font-size:10.5px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.10em;margin:0 0 8px 0;">{{ $stat['label'] }}</p>
                <p style="font-size:24px;font-weight:800;color:#0f172a;margin:0 0 5px 0;line-height:1.1;letter-spacing:-0.02em;">{{ $stat['value'] }}</p>
                <p style="font-size:12px;color:#64748b;margin:0;font-weight:500;">{{ $stat['description'] }}</p>
            </div>
        </div>

        {{-- Bottom subtle gradient --}}
        <div style="height:3px;background:linear-gradient(90deg,{{ $stat['color'] }}22,transparent);width:100%;"></div>
    </div>
    @endforeach
</div>