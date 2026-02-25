<!DOCTYPE html>
<html>
<head>
    <title>Layups - {{ $supplier->name }}</title>
</head>
<body>
    <h1>Layups - {{ $supplier->name }}</h1>
    
    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <a href="{{ route('suppliers.layups.create', $supplier) }}">Add Layup</a>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Layers</th>
            <th>Actions</th>
        </tr>
        @foreach($layups as $layup)
        <tr>
            <td>{{ $layup->id }}</td>
            <td>{{ $layup->name }}</td>
            <td>{{ $layup->layers->count() }}</td>
            <td>
                <a href="{{ route('layups.show', $layup) }}">View</a>
                <a href="{{ route('layups.edit', $layup) }}">Edit</a>
                <form action="{{ route('layups.destroy', $layup) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
    
    <br>
    <a href="{{ route('suppliers.show', $supplier) }}">Back to Supplier</a>
</body>
</html>