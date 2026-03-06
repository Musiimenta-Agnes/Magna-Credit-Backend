<div class="space-y-4">
    @if($getRecord() && $getRecord()->national_id_image)
        <div>
            <p class="text-sm font-semibold text-gray-700 mb-2">National ID Image</p>
            <a href="{{ Storage::disk('public')->url($getRecord()->national_id_image) }}" target="_blank">
                <img src="{{ Storage::disk('public')->url($getRecord()->national_id_image) }}"
                     class="rounded-lg border border-gray-200 shadow max-h-72 object-contain cursor-pointer hover:opacity-90 transition"
                     alt="National ID" />
            </a>
            <p class="text-xs text-gray-400 mt-1">Click image to open full size</p>
        </div>
    @else
        <p class="text-sm text-gray-400 italic">No National ID image submitted.</p>
    @endif

    @if($getRecord() && $getRecord()->collateral_images && count($getRecord()->collateral_images) > 0)
        <div>
            <p class="text-sm font-semibold text-gray-700 mb-2">Collateral Images</p>
            <div class="grid grid-cols-2 gap-3">
                @foreach($getRecord()->collateral_images as $image)
                    <a href="{{ Storage::disk('public')->url($image) }}" target="_blank">
                        <img src="{{ Storage::disk('public')->url($image) }}"
                             class="rounded-lg border border-gray-200 shadow max-h-48 w-full object-contain cursor-pointer hover:opacity-90 transition"
                             alt="Collateral Image" />
                    </a>
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mt-1">Click any image to open full size</p>
        </div>
    @else
        <p class="text-sm text-gray-400 italic">No collateral images submitted.</p>
    @endif
</div>
