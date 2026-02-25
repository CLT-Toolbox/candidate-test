<!DOCTYPE html>
<html>
<head>
    <title>Add Layer</title>
</head>
<body>
    <h1>Add Layer - {{ $layup->name }}</h1>
    
    <form action="{{ route('dashboard.layups.layers.store', $layup) }}" method="POST">
        @csrf
        <input type="number" name="layer_order" placeholder="Layer Order" required>
        <input type="number" step="0.01" name="thickness" placeholder="Thickness" required>
        <input type="number" step="0.01" name="width" placeholder="Width" required>
        <input type="number" step="0.01" name="angle" placeholder="Angle (0-360)" required>
        <button type="submit">Save</button>
    </form>
    
    <a href="{{ route('dashboard.layups.show', $layup) }}">Back</a>
</body>
</html>