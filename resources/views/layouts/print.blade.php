<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Document' }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        body {
            background-color: white !important;
            color: black !important;
            font-family: 'Inter', sans-serif;
            padding: 40px;
        }
        .print-container {
            max-width: 900px;
            margin: 0 auto;
            border: 1px solid #eee;
            padding: 40px;
            box-shadow: none;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header img {
            max-height: 60px;
        }
        .header-info {
            text-align: right;
        }
        .section-title {
            background: #f8f9fa;
            padding: 10px;
            font-weight: bold;
            margin-top: 20px;
            border-left: 4px solid #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            border-top: 1px solid #333;
            width: 200px;
            text-align: center;
            padding-top: 10px;
            margin-top: 60px;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
            .print-container {
                border: none;
                padding: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn btn-primary">Print Now</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>
    
    <div class="print-container">
        @yield('content')
    </div>
</body>
</html>
