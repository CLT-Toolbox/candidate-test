<!DOCTYPE html>
<html>
<head>
    <title>Suppliers</title>
</head>
<body>
    <h1>Suppliers</h1>
    
    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <a href="{{ route('suppliers.create') }}">Add Supplier</a>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
        @foreach($suppliers as $supplier)
        <tr>
            <td>{{ $supplier->id }}</td>
            <td>{{ $supplier->name }}</td>
            <td>
                <a href="{{ route('suppliers.show', $supplier) }}">View</a>
                <a href="{{ route('suppliers.edit', $supplier) }}">Edit</a>
                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</body>
</html>