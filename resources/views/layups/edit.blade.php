<!DOCTYPE html>
<html>
<head>
    <title>Edit Layup</title>
</head>
<body>
    <h1>Edit Layup</h1>
    
    <form action="{{ route('layups.update', $layup) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" name="name" value="{{ $layup->name }}" required>
        <button type="submit">Update</button>
    </form>
    
    <a href="{{ route('layups.show', $layup) }}">Back</a>
</body>
</html>