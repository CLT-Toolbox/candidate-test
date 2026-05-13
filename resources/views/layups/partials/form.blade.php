<div>
    <label class="block text-sm font-medium">Name</label>
    <input class="field-input" name="name" value="{{ old('name', $layup->name ?? '') }}" required>
    @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm font-medium">Description</label>
    <textarea class="field-textarea" name="description" rows="3">{{ old('description', $layup->description ?? '') }}</textarea>
    @error('description')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
