<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; padding: 4px 0;">
    @foreach($stats as $stat)
    <div style="background:#fff;border-radius:16px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 4px 16px rgba(0,0,0,0.04);border:1px solid #f1f5f9;display:flex;align-items:flex-start;gap:16px;transition:transform 0.2s,box-shadow 0.2s;position:relative;overflow:hidden;"
        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.10)'"
        onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 1px 3px rgba(0,0,0,0.06),0 4px 16px rgba(0,0,0,0.04)'">
        <div style="position:absolute;top:0;left:0;right:0;height:3px;background:{{ $stat['color'] }};border-radius:16px 16px 0 0;"></div>
        <div style="width:48px;height:48px;border-radius:12px;background:{{ $stat['bg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <x-dynamic-component :component="'heroicon-o-' . $stat['icon']" style="width:24px;height:24px;color:{{ $stat['color'] }};"/>
        </div>
        <div style="flex:1;min-width:0;">
            <p style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.06em;margin:0 0 6px 0;">{{ $stat['label'] }}</p>
            <p style="font-size:22px;font-weight:700;color:#0f172a;margin:0 0 4px 0;line-height:1.2;word-break:break-all;">{{ $stat['value'] }}</p>
            <p style="font-size:12px;color:#64748b;margin:0;">{{ $stat['description'] }}</p>
        </div>
    </div>
    @endforeach
</div>
