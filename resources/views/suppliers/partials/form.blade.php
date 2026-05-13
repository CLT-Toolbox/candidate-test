<div>
    <label class="block text-sm font-medium">Name</label>
    <input class="field-input" name="name" value="{{ old('name', $supplier->name ?? '') }}" required>
    @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-medium">Code</label>
    <input class="field-input" name="code" value="{{ old('code', $supplier->code ?? '') }}">
    @error('code')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-medium">Address</label>
    <textarea class="field-textarea" name="address" rows="3">{{ old('address', $supplier->address ?? '') }}</textarea>
    @error('address')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
