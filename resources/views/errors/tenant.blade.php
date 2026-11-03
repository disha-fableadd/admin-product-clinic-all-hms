<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Account unavailable</title>
    <style>
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
               font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif; background: #f4f6fb; color: #1f2937; }
        .card { background: #fff; max-width: 460px; width: calc(100% - 32px); padding: 40px 32px; border-radius: 14px;
                box-shadow: 0 10px 30px rgba(15, 23, 42, .08); text-align: center; }
        .icon { width: 56px; height: 56px; border-radius: 50%; margin: 0 auto 18px; display: flex; align-items: center;
                justify-content: center; font-size: 28px; background: #fee2e2; color: #b91c1c; }
        .icon.warn { background: #fef3c7; color: #b45309; }
        h1 { font-size: 20px; margin: 0 0 10px; }
        p { margin: 0 0 8px; line-height: 1.55; color: #4b5563; }
        .code { margin-top: 18px; font-size: 12px; color: #9ca3af; letter-spacing: .04em; }
    </style>
</head>
<body>
    @php
        $titles = [
            'NOT_FOUND' => 'Domain not registered',
            'PRODUCT_MISMATCH' => 'Wrong product',
            'INACTIVE' => 'Account inactive',
            'PRODUCT_INACTIVE' => 'Product disabled',
            'EXPIRED' => 'Subscription expired',
            'PENDING' => 'Payment pending',
            'NOT_PROVISIONED' => 'Setup in progress',
            'UNREACHABLE' => 'Temporarily unavailable',
        ];
        $isWarning = in_array($reason ?? '', ['EXPIRED', 'PENDING', 'NOT_PROVISIONED', 'UNREACHABLE'], true);
    @endphp
    <div class="card">
        <div class="icon {{ $isWarning ? 'warn' : '' }}">{{ $isWarning ? '!' : '×' }}</div>
        <h1>{{ $titles[$reason ?? ''] ?? 'Account unavailable' }}</h1>
        <p>{{ $message ?? 'Your account is currently unavailable. Please contact the administrator.' }}</p>
        @if (! empty($expiryDate))
            <p>Expired on: <strong>{{ \Carbon\Carbon::parse($expiryDate)->format('d M Y') }}</strong></p>
        @endif
        <div class="code">{{ $reason ?? 'UNKNOWN' }}</div>
    </div>
</body>
</html>

