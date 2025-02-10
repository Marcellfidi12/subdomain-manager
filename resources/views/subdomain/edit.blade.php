<!-- resources/views/subdomain/edit.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subdomain</title>
</head>
<body>
    <h2>Edit Subdomain: {{ $subdomain['name'] }}</h2>

    <!-- Menampilkan pesan sukses atau error -->
    @if (session('success'))
        <p style="color: green">{{ session('success') }}</p>
    @elseif (session('error'))
        <p style="color: red">{{ session('error') }}</p>
    @endif

    <form action="{{ route('subdomain.update', $subdomain['id']) }}" method="POST">
        @csrf
        @method('PUT')

        <label for="ipAddress">IP Address:</label><br>
        <input type="text" id="ipAddress" name="ipAddress" value="{{ old('ipAddress', $subdomain['content']) }}" required><br><br>

        <input type="submit" value="Update IP Address">
    </form>

    <form action="{{ route('subdomain.destroy', $subdomain['id']) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this subdomain?');">
        @csrf
        @method('DELETE')
        <input type="submit" value="Delete Subdomain" style="background-color: red; color: white;">
    </form>
</body>
</html>
