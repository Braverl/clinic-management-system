<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - Clinic System</title>
    @vite(['resources/css/app.css'])
    <style>
        .error-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f8fafc; padding: 2rem; }
        .error-card { background: white; border-radius: 2rem; box-shadow: 0 24px 48px -8px rgb(0 0 0 / 0.14); padding: 3rem; text-align: center; max-width: 520px; width: 100%; border: 1px solid #f1f5f9; }
        .error-code { font-family: 'Poppins', sans-serif; font-size: 7rem; font-weight: 800; line-height: 1; background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .error-icon { font-size: 2.5rem; color: #0ea5e9; margin-bottom: 0.5rem; }
        .error-title { font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; }
        .error-message { color: #64748b; margin-bottom: 2rem; }
        .error-actions { display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap; }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-card">
            <div class="error-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="error-code">419</div>
            <h1 class="error-title">Session Expired</h1>
            <p class="error-message">Your session has expired or the security token for this request is invalid. This can happen when a page is left open for a long time. Please refresh the page and try again.</p>
            <div class="error-actions">
                <a href="{{ url('/') }}" class="btn btn-primary"><i class="fas fa-redo me-2"></i>Start Over</a>
                <a href="{{ route('login') }}" class="btn btn-secondary"><i class="fas fa-sign-in-alt me-2"></i>Login</a>
            </div>
        </div>
    </div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</body>
</html>