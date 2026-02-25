<!DOCTYPE html>
<html>
<head>
    <title>Layup Details</title>
</head>
<body>
    <h1>{{ $layup->name }}</h1>
    <p>Supplier: {{ $layup->supplier->name }}</p>
    
    <h2>Layers</h2>
    <a href="{{ route('dashboard.layups.layers.create', $layup) }}">Add Layer</a>
    
    <table border="1" cellpadding="10">
        <tr>
            <th>Order</th>
            <th>Thickness</th>
            <th>Width</th>
            <th>Angle</th>
        </tr>
        @foreach($layup->layers as $layer)
        <tr>
            <td>{{ $layer->layer_order }}</td>
            <td>{{ $layer->thickness }}</td>
            <td>{{ $layer->width }}</td>
            <td>{{ $layer->angle }}</td>
        </tr>
        @endforeach
    </table>
    
    <br>
    <a href="{{ route('dashboard.suppliers.show', $layup->supplier) }}">Back to Supplier</a>
</body>
</html>