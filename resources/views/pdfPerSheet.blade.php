@foreach ($records as $record)
    <div
        style="text-align: center; font-size: 16px; font-weight: bold; padding-bottom: 10px; border-bottom: 1px solid #ddd; margin-top: 20px;">
        <img src="{{ public_path('images/Logo.png') }}" style="max-width:100px">
        <h2 style="margin: 0; padding: 0;">REIMBURSEMENT REPORT</h2>
        <p style="font-size: 14px; color: #169; margin: 0; padding: 0;">PLN ICON PLUS UNIT LAYANAN NTT</p>
        <p style="font-size: 12px; color: #555; margin: 0; padding: 0;">Generated on:
            {{ \Carbon\Carbon::now('Asia/Makassar')->format('d-m-Y H:i') }}</p>
    </div>

    <!-- Layout for Side-by-Side Content -->
    <div style="margin:10px;">ID : {{ $record->id }}</div>
    <div style="width: 100%; display: inline-block;">
        <div style="width: 30%; display: inline-block; vertical-align: top; padding: 10px; border: 1px solid #000;">
            @if (!in_array('user', $hiddenCols))
                <div style="margin-bottom: 10px;">
                    <strong style="font-size: 14px; color: #666;">Name:</strong>
                    <span style="font-size: 14px; color: #000;">{{ $record->user->name }}</span>
                </div>
            @endif
            @if (!in_array('date', $hiddenCols))
                <div style="margin-bottom: 10px;">
                    <strong style="font-size: 14px; color: #666;">Date:</strong>
                    <span style="font-size: 14px; color: #000;">
                        {{ \Carbon\Carbon::parse($record->date)->format('d-m-Y') }}
                    </span>
                </div>
            @endif
            @if (!in_array('title', $hiddenCols))
                <div style="margin-bottom: 10px;">
                    <strong style="font-size: 14px; color: #666;">Title:</strong>
                    <span style="font-size: 14px; color: #000;">{{ $record->title }}</span>
                </div>
            @endif
            @if (!in_array('price', $hiddenCols))
                <div style="margin-bottom: 10px;">
                    <strong style="font-size: 14px; color: #666;">Price:</strong>
                    <span style="font-size: 14px; color: #000;">Rp
                        {{ number_format($record->price, 0, ',', '.') }}</span>
                </div>
            @endif
            @if (!in_array('is_paid', $hiddenCols))
                <div style="margin-bottom: 10px;">
                    <strong style="font-size: 14px; color: #666;">Status:</strong>
                    <span style="font-size: 14px; color: {{ $record->is_paid ? '#28a745' : '#dc3545' }};">
                        {{ $record->is_paid ? 'Paid' : 'Unpaid' }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Right : Image Section -->
        <div
            style="width: 60%; display: inline-block; padding: 0 0 10px 10px; text-align: center; vertical-align: top;">
            @if ($record->before && $record->after)
                @if (!in_array('before', $hiddenCols))
                    <div style="margin-bottom: 10px;">
                        <img src="{{ storage_path('app/public/' . $record->before) }}" alt="Before"
                            style="max-width: 100%; height: auto; border-radius: 1px;">
                        <p style="font-size: 12px; color: #555;">Before</p>
                    </div>
                @endif
                @if (!in_array('after', $hiddenCols))
                    <div>
                        <img src="{{ storage_path('app/public/' . $record->after) }}" alt="After"
                            style="max-width: 100%; height: auto; border-radius: 8px;">
                        <p style="font-size: 12px; color: #555;">After</p>
                    </div>
                @endif
            @else
                @if (!in_array('documentation', $hiddenCols))
                    <div>
                        <img src="{{ storage_path('app/public/' . $record->documentation) }}" alt="Documentation"
                            style="max-width: 100%; height: auto; border-radius: 8px;">
                        <p style="font-size: 12px; color: #555;">Documentation</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Page Break After Each Record -->
    <div style="page-break-after: always;"></div>
@endforeach
