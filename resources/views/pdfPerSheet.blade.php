@foreach ($records as $record)
    <div
        style="font-size: 16px; font-weight: bold; padding-bottom: 10px; border-bottom: 1px solid #ddd; margin-top: 20px; width:100%">
        <div style="width: 20%; display: inline-block">
            <img src="{{ public_path('images/Logo.png') }}" style="max-width:100px">
        </div>
        <div style="width: 60%; display: inline-block; text-align:center">
            <h2 style="margin: 0; padding: 0;">REIMBURSEMENT REPORT</h2>
            <p style="font-size: 14px; color: #169; margin: 0; padding: 0;">PLN ICON PLUS UNIT LAYANAN NTT</p>
            <p style="font-size: 12px; color: #555; margin: 0; padding: 0;">Generated on:
                {{ \Carbon\Carbon::now('Asia/Makassar')->format('d-m-Y H:i') }}</p>
        </div>
    </div>

    <!-- Layout for Side-by-Side Content -->
    <div style="margin:10px;">ID : {{ $record->id }}</div>
    <div style="width: 100%; display: inline-block;">
        <div style="width: 20%; display: inline-block; vertical-align: top; padding: 10px; border: 1px solid #000;">
            @if (!in_array('user', $hiddenCols))
                <div style="margin-bottom: 10px;">
                    <strong style="font-size: 14px; color: #666;">Name:</strong>
                    <span style="font-size: 14px; color: #000;">{{ $record->user->name }}</span>
                </div>
            @endif
            @if (!in_array('date', $hiddenCols))
                <div style="margin-bottom: 10px;">
                    <strong style="font-size: 14px; color: #666;">Date:</strong>
                    <span
                        style="font-size: 14px; color: #000;">{{ \Carbon\Carbon::parse($record->date)->format('d-m-Y') }}</span>
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

        <!-- Right : Image Section with Conditions -->
        <div
            style="max-width: 70%; display: inline-block; padding: 0 0 10px 10px; text-align: center; vertical-align: top;">
            @if ($record->before && $record->after)
                @php
                    // Assuming the image orientation is determined by width and height
                    $beforeImagePath = storage_path('app/public/' . $record->before);
                    $afterImagePath = storage_path('app/public/' . $record->after);
                    [$beforeWidth, $beforeHeight] = getimagesize($beforeImagePath);
                    [$afterWidth, $afterHeight] = getimagesize($afterImagePath);
                    $isBeforePortrait = $beforeHeight > $beforeWidth;
                    $isAfterPortrait = $afterHeight > $afterWidth;
                @endphp

                <!-- Portrait + Portrait: Side by Side -->
                @if ($isBeforePortrait && $isAfterPortrait)
                    <div style="display: inline-block; width: 35%; margin-right: 5px;">
                        <img src="{{ $beforeImagePath }}" alt="Before" style="width: 100%; max-height: 50%;">
                        <p style="font-size: 12px; color: #555;">Before</p>
                    </div>
                    <div style="display: inline-block; width: 35%;">
                        <img src="{{ $afterImagePath }}" alt="After" style="width: 100%; max-height: 50%;">
                        <p style="font-size: 12px; color: #555;">After</p>
                    </div>
                    <!-- Landscape + Landscape: Up and Down -->
                @elseif (!$isBeforePortrait && !$isAfterPortrait)
                    <div style="margin-bottom: 10px;">
                        <img src="{{ $beforeImagePath }}" alt="Before" style="width: 100%; max-height: 25%;">
                        <p style="font-size: 12px; color: #555;">Before</p>
                    </div>
                    <div>
                        <img src="{{ $afterImagePath }}" alt="After" style="width: 100%; max-height: 25%;">
                        <p style="font-size: 12px; color: #555;">After</p>
                    </div>
                    <!-- Landscape + Portrait or Portrait + Landscape: Up and Down with Max Height Constraint -->
                @elseif ((!$isBeforePortrait && $isAfterPortrait) || ($isBeforePortrait && !$isAfterPortrait))
                    <div style="display: inline-block; width: 70%; margin-right: 5px;">
                        <img src="{{ $beforeImagePath }}" alt="Before" style="width: auto; max-height: 25%;">
                        <p style="font-size: 12px; color: #555;">Before</p>
                    </div>
                    <div style="display: inline-block; width: 70%; margin-right: 5px;">
                        <img src="{{ $afterImagePath }}" alt="After" style="width: auto; max-height: 25%;">
                        <p style="font-size: 12px; color: #555;">After</p>
                    </div>
                @endif
            @else
                @if (!in_array('documentation', $hiddenCols))
                    <div>
                        <img src="{{ storage_path('app/public/' . $record->documentation) }}" alt="Documentation"
                            style="width: 70%; max-height: 50%;">
                        <p style="font-size: 12px; color: #555;">Documentation</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Page Break After Each Record -->
    <div style="page-break-after: always;"></div>
@endforeach
