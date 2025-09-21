<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gymly - <?php echo isset($pageTitle) ? $pageTitle : 'Fitness Management'; ?></title>
    <!-- Bootstrap CSS -->
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563EB;       /* Primary blue for fitness theme */
            --primary-dark: #1D4ED8;  /* Darker blue */
            --primary-light: #EBF4FF; /* Light blue background */
            --accent: #10B981;        /* Green accent for fitness */
            --text-dark: #1E2A38;     /* Dark text */
            --text-light: #64748B;    /* Light text */
            --border: #E2E8F0;        /* Border color */
            --white: #FFFFFF;         /* White */
            --gray-50: #F9FAFB;       /* Light gray background */
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gray-50);
            color: var(--text-dark);
            line-height: 1.6;
        }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        
        .auth-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo {
            max-height: 60px;
            margin-bottom: 1rem;
        }
        
        .auth-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        
        .auth-subtitle {
            color: var(--text-light);
            margin-bottom: 2rem;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            padding: 0.875rem 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        
        .btn-primary {
            background-color: var(--primary);
            border: none;
            padding: 0.875rem 1.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
        }
        
        .btn-google {
            background-color: var(--white);
            color: var(--text-dark);
            border: 1px solid var(--border);
            padding: 0.875rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .btn-google:hover {
            border-color: var(--primary);
            background-color: var(--primary-light);
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: var(--text-light);
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: var(--border);
        }
        
        .divider-text {
            padding: 0 1rem;
            font-size: 0.875rem;
        }
        
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
        }
        
        .forgot-link:hover {
            text-decoration: underline;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            color: var(--text-light);
        }
        
        .auth-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .auth-link:hover {
            text-decoration: underline;
        }
        
        /* Alert styling */
        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .alert-danger {
            background-color: #FEF2F2;
            border-color: #FECACA;
            color: #B91C1C;
        }
        
        .alert-success {
            background-color: #F0FDF4;
            border-color: #BBF7D0;
            color: #15803D;
        }
        
        /* Homepage specific styles (if needed) */
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 4rem 0;
            border-radius: 0 0 24px 24px;
        }
        
        .feature-card {
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-4px);
        }
        
        .feature-icon {
            background: var(--primary-light);
            color: var(--primary);
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <main>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>