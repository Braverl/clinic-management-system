<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance - Clinic System</title>
    @vite(['resources/css/app.css'])
    <style>
        .error-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f8fafc; padding: 2rem; }
        .error-card { background: white; border-radius: 2rem; box-shadow: 0 24px 48px -8px rgb(0 0 0 / 0.14); padding: 3rem; text-align: center; max-width: 520px; width: 100%; border: 1px solid #f1f5f9; }
        .error-icon { font-size: 2.5rem; color: #6366f1; margin-bottom: 0.5rem; }
        .error-title { font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; }
        .error-message { color: #64748b; margin-bottom: 2rem; }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-card">
            <div class="error-icon"><i class="fas fa-tools"></i></div>
            <h1 class="error-title">We'll Be Right Back</h1>
            <p class="error-message">The system is currently undergoing maintenance. Please check back shortly.</p>
            <a href="javascript:window.location.reload()" class="btn btn-primary"><i class="fas fa-sync me-2"></i>Refresh</a>
        </div>
    </div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</body>
</html>