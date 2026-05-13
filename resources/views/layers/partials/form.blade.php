<div>
    <label class="block text-sm font-medium">Layer Order</label>
    <input type="number" min="1" class="field-input" name="layer_order" value="{{ old('layer_order', $layer->layer_order ?? '') }}" required>
    @error('layer_order')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm font-medium">Thickness</label>
    <input type="number" step="0.01" min="0" class="field-input" name="thickness" value="{{ old('thickness', $layer->thickness ?? '') }}" required>
    @error('thickness')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm font-medium">Width</label>
    <input type="number" step="0.01" min="0" class="field-input" name="width" value="{{ old('width', $layer->width ?? '') }}" required>
    @error('width')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm font-medium">Angle</label>
    <input type="number" step="0.01" class="field-input" name="angle" value="{{ old('angle', $layer->angle ?? '') }}" required>
    @error('angle')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
