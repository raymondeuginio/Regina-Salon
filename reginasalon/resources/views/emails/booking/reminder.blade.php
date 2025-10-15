<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengingat Booking Regina Salon</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; padding: 24px;">
        <tr>
            <td>
                <h1 style="font-size: 20px; font-weight: 600; margin-bottom: 16px;">Halo {{ $customerName }},</h1>
                <p style="margin: 0 0 12px;">Terima kasih telah melakukan booking di <strong>{{ $storeName ?? 'Regina Salon' }}</strong>.</p>
                <p style="margin: 0 0 12px;">Berikut detail jadwal Anda:</p>
                <ul style="margin: 0 0 16px 20px; padding: 0;">
                    <li><strong>Tanggal:</strong> {{ $dateLabel }}</li>
                    <li><strong>Waktu:</strong> {{ $timeLabel }}</li>
                    @if($storeAddress)
                        <li><strong>Lokasi:</strong> {{ $storeAddress }}</li>
                    @endif
                </ul>
                <p style="margin: 0 0 12px;">Layanan yang dipesan:</p>
                <ul style="margin: 0 0 16px 20px; padding: 0;">
                    @foreach($services as $service)
                        <li>{{ $service }}</li>
                    @endforeach
                </ul>
                <p style="margin: 0 0 12px;">Jika Anda perlu mengubah jadwal, silakan hubungi kami melalui WhatsApp resmi Regina Salon.</p>
                <p style="margin: 0;">Sampai jumpa di salon!</p>
            </td>
        </tr>
    </table>
</body>
</html>
