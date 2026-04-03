<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance - techBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px;
        }
        .maintenance-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .maintenance-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            margin-bottom: 15px;
        }
        p {
            color: #666;
            line-height: 1.6;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            margin-top: 20px;
            color: white;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,126,234,0.4);
        }
    </style>

<link rel="icon" type="image/png" sizes="32x32" href="https://img.icons8.com/color/48/repair-tools.png">
<link rel="apple-touch-icon" sizes="180x180" href="https://img.icons8.com/color/48/repair-tools.png">
</head>
<body>
    <div class="maintenance-card">
        <div class="maintenance-icon">
            🔧⚙️
        </div>
        <h1>Under Maintenance</h1>
        <p>We're currently performing scheduled maintenance to improve your experience.</p>
        <p>Please check back soon!</p>
        <button class="btn-primary" onclick="location.reload()">
            <i class="fas fa-sync-alt me-2"></i>Refresh
        </button>
    </div>
</body>
</html>