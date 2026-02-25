<!DOCTYPE html>
<html>
<head>
    <title>Add Supplier</title>
</head>
<body>
    <h1>Add Supplier</h1>
    
    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Supplier Name" required>
        <button type="submit">Save</button>
    </form>
    
    <a href="{{ route('suppliers.index') }}">Back</a>
</body>
</html>