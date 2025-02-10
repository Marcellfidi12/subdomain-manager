<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Subdomain</title>
    <!-- Include Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        /* Body Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: url('1.jpg') no-repeat center center fixed; 
            background-size: cover;
            color: #fff;
            margin: 0;
            height: 100vh;
        }

        .container {
            max-width: 450px;
            background-color: rgba(255, 255, 255, 0.5); /* Semi-transparent background */
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-top: 100px;
        }

        h2 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        h3 {
            font-size: 20px;
            color: #333;
            text-align: center;
            margin-top: 40px;
        }

        .form-label {
            font-size: 14px;
            color: #333;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 12px;
            font-size: 16px;
            height: 45px;
            margin-bottom: 20px;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 14px;
            font-size: 16px;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            transform: translateY(-2px);
        }

        .list-group-item {
            padding: 12px 20px;
            font-size: 16px;
            background-color: #fafafa;
            border: none;
            margin-bottom: 10px;
        }

        .list-group-item .btn {
            margin-left: 10px;
        }

        /* Toast Styles */
        .toast {
            min-width: 250px;
            max-width: 350px;
            opacity: 1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            animation: toastSlideIn 0.5s ease-in-out, toastFadeOut 0.5s ease-in-out 4.5s forwards;
            border-radius: 8px;
        }

        .toast-body {
            padding: 12px 20px;
            font-size: 14px;
            line-height: 1.5;
        }

        @keyframes toastSlideIn {
            0% { transform: translateX(100%); }
            100% { transform: translateX(0); }
        }

        @keyframes toastFadeOut {
            0% { opacity: 1; }
            100% { opacity: 0; transform: translateX(100%); }
        }

        /* Responsive Design */
        @media (max-width: 576px) {
            .container {
                padding: 20px;
                margin-top: 40px;
            }
        }

        .icon-btn {
            font-size: 18px;
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Free Subdomain</h2>

        <!-- Form untuk memasukkan subdomain dan IP -->
        <form action="{{ route('subdomain.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="subdomain" class="form-label">Subdomain:</label>
                <input type="text" class="form-control" id="subdomain" name="subdomain" value="{{ old('subdomain') }}" required>
            </div>

            <div class="mb-3">
                <label for="ipAddress" class="form-label">IP Address:</label>
                <input type="text" class="form-control" id="ipAddress" name="ipAddress" value="{{ old('ipAddress') }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Tambah Subdomain</button>
        </form>

        <h3>Daftar Subdomain yang Terdaftar:</h3>
        <marquee>
            @foreach($subdomains as $subdomain)
                <span>
                    {{ $subdomain['name'] }} (IP: {{ $subdomain['content'] }})
                </span>
            @endforeach
        </marquee>
    </div>

    <!-- Toast notification container -->
    <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 11">
        <div id="toast-container"></div>
    </div>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Toast Notification JS -->
    <script>
        // Function to show a toast message
        function showToast(message, type) {
            const toastContainer = document.getElementById('toast-container');
            
            const toast = document.createElement('div');
            toast.classList.add('toast');
            toast.classList.add('fade');
            toast.classList.add('show');
            toast.classList.add('bg-' + type);
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            toast.innerHTML = `\
                <div class="toast-body">
                    ${message}
                </div>
            `;
            toastContainer.appendChild(toast);

            // Automatically remove toast after 5 seconds (with fade-out animation)
            setTimeout(() => {
                toast.classList.remove('show');
                toast.classList.add('fade');
                setTimeout(() => {
                    toast.remove();
                }, 500); // Delay removal to allow fade-out animation
            }, 5000);
        }

        // Show success toast on successful actions (Adding, Editing, Deleting Subdomain)
        @if (session('success'))
            showToast('{{ session('success') }}', 'success');
        @elseif (session('error'))
            showToast('{{ session('error') }}', 'danger');
        @endif
    </script>

</body>
</html>
