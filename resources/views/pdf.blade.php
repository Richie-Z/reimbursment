@foreach ($records as $record)
    <table width="100%" style="border-collapse: collapse; margin-bottom: 30px; font-family: Arial, sans-serif;">
        <tr>
            <!-- Image Section -->
            <td style="border: 1px solid #ddd; padding: 10px; width: 50%; vertical-align: top; background-color: #f9f9f9;">
                @if ($record->before && $record->after)
                    <div style="text-align: center; margin-bottom: 10px;">
                        <!-- 'Before' Image -->
                        <img src="{{ storage_path('app/public/' . $record->before) }}" alt="Before" width="100%" style="max-height: 150px; border-radius: 8px; margin-bottom: 5px;">
                        <p style="font-size: 12px; color: #555;">Before</p>
                    </div>
                    <div style="text-align: center;">
                        <!-- 'After' Image -->
                        <img src="{{ storage_path('app/public/' . $record->after) }}" alt="After" width="100%" style="max-height: 150px; border-radius: 8px; margin-bottom: 0px;">
                        <p style="font-size: 12px; color: #555;">After</p>
                    </div>
                @else
                    <div style="text-align: center;">
                        <!-- Documentation Image -->
                        <img src="{{ storage_path('app/public/' . $record->documentation) }}" alt="Documentation" width="100%" style="max-height: 150px; border-radius: 8px; margin-bottom: 5px;">
                    </div>
                @endif
            </td>

            <!-- Text Details Section -->
            <td style="padding: 15px; width: 50%; vertical-align: top;">
                <div style="margin-bottom: 10px;">
                    <strong style="font-size: 14px; color: #333;">Name:</strong>
                    <span style="font-size: 14px; color: #666;">{{ $record->user->name }}</span>
                </div>
                <div style="margin-bottom: 10px;">
                    <strong style="font-size: 14px; color: #333;">Date:</strong>
                    <span style="font-size: 14px; color: #666;">{{ \Carbon\Carbon::parse($record->date)->format('d-m-Y') }}</span>
                </div>
                <div style="margin-bottom: 10px;">
                    <strong style="font-size: 14px; color: #333;">Title:</strong>
                    <span style="font-size: 14px; color: #666;">{{ $record->title }}</span>
                </div>
                <div style="margin-bottom: 10px;">
                    <strong style="font-size: 14px; color: #333;">Price:</strong>
                    <span style="font-size: 14px; color: #666;">Rp {{ number_format($record->price, 0, ',', '.') }}</span>
                </div>
                <div style="margin-bottom: 10px;">
                    <strong style="font-size: 14px; color: #333;">Status:</strong>
                    <span style="font-size: 14px; color: {{ $record->is_paid ? '#28a745' : '#dc3545' }};">
                        {{ $record->is_paid ? 'Paid' : 'Unpaid' }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Divider Between Records -->
    <hr style="border: none; border-top: 1px solid #ddd; margin: 25px 0;">
@endforeach

