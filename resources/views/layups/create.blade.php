<!DOCTYPE html>
<html>
<head>
    <title>Add Layup</title>
</head>
<body>
    <h1>Add Layup - {{ $supplier->name }}</h1>
    
    <form action="{{ route('dashboard.suppliers.layups.store', $supplier) }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Layup Name" required>
        <button type="submit">Save</button>
    </form>
    
    <a href="{{ route('dashboard.suppliers.show', $supplier) }}">Back</a>
</body>
</html>