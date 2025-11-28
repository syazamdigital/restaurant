<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #ecf0f1;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
        }
        .success-card {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 90%;
        }
        .success-icon {
            font-size: 6rem;
            color: #27ae60;
            margin-bottom: 20px;
            animation: pulse 1s infinite alternate;
        }
        @keyframes pulse {
            from { transform: scale(1); }
            to { transform: scale(1.05); }
        }
    </style>
</head>
<body>

<div class="success-card">
    <i class="fas fa-check-circle success-icon"></i>
    <h1 class="h3 fw-bold text-success mb-3">Pesanan Anda Telah Diterima!</h1>
    
    <p class="lead">Terima kasih, <b>{{ $name ?? 'Pelanggan' }}</b>.</p>
    
    <div class="alert alert-info border-0 mt-4">
        <p class="mb-1 fw-bold">ID Pesanan Anda:</p>
        <h4 class="text-primary">{{ $orderId ?? 'N/A' }}</h4>
        <p class="mt-3 mb-1 fw-bold">Total Pembayaran:</p>
        <h3 class="text-success">RM {{ number_format($total ?? 0, 2) }}</h3>
    </div>
    
    <p class="mt-4">
        Kami sedang memproses pesanan Anda.
        <span class="fw-bold">Mohon tunggu notifikasi selanjutnya dari Kios.</span>
    </p>

    <a href="{{ route('customer.menu') }}" class="btn btn-warning mt-3">
        <i class="fas fa-redo me-1"></i> Pesan Menu Lain
    </a>
</div>

</body>
</html>