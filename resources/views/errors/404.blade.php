<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Clinic System</title>
    @vite(['resources/css/app.css'])
    <style>
        .error-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f8fafc; padding: 2rem; }
        .error-card { background: white; border-radius: 2rem; box-shadow: 0 24px 48px -8px rgb(0 0 0 / 0.14); padding: 3rem; text-align: center; max-width: 520px; width: 100%; border: 1px solid #f1f5f9; }
        .error-code { font-family: 'Poppins', sans-serif; font-size: 7rem; font-weight: 800; line-height: 1; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a78bfa 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .error-icon { font-size: 2.5rem; color: #6366f1; margin-bottom: 0.5rem; }
        .error-title { font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; }
        .error-message { color: #64748b; margin-bottom: 2rem; }
        .error-actions { display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap; }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-card">
            <div class="error-icon"><i class="fas fa-map-signs"></i></div>
            <div class="error-code">404</div>
            <h1 class="error-title">Page Not Found</h1>
            <p class="error-message">The page you are looking for doesn't exist or may have been moved. Please check the address or navigate back to a valid destination.</p>
            <div class="error-actions">
                <a href="{{ url('/') }}" class="btn btn-primary"><i class="fas fa-home me-2"></i>Go Home</a>
                <a href="javascript:history.back()" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Go Back</a>
            </div>
        </div>
    </div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</body>
</html>