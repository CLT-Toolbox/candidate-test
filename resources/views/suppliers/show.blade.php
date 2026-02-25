<!DOCTYPE html>
<html>
<head>
    <title>Supplier Details</title>
</head>
<body>
    <h1>{{ $supplier->name }}</h1>
    
    <h2>Layups</h2>
    <a href="{{ route('suppliers.layups.create', $supplier) }}">Add Layup</a>
    
    <ul>
        @foreach($supplier->layups as $layup)
        <li>
            {{ $layup->name }} 
            ({{ $layup->layers->count() }} layers)
            - <a href="{{ route('layups.show', $layup) }}">View</a>
        </li>
        @endforeach
    </ul>
    
    <a href="{{ route('suppliers.index') }}">Back</a>
</body>
</html>