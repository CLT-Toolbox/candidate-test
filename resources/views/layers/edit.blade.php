<!DOCTYPE html>
<html>
<head>
    <title>Edit Layer</title>
</head>
<body>
    <h1>Edit Layer</h1>
    
    <form action="{{ route('layups.layers.update', $layer) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="number" name="layer_order" value="{{ $layer->layer_order }}" required>
        <input type="number" step="0.01" name="thickness" value="{{ $layer->thickness }}" required>
        <input type="number" step="0.01" name="width" value="{{ $layer->width }}" required>
        <input type="number" step="0.01" name="angle" value="{{ $layer->angle }}" required>
        <button type="submit">Update</button>
    </form>
    
    <a href="{{ route('layups.show', $layer->layup) }}">Back</a>
</body>
</html>