<table>
    <thead>
        <tr>
            <th>Waktu</th>
            <th>Device</th>
            <th>Tipe Sensor</th>
            <th>Nilai Suhu (&deg;C)</th>
            <th>Nilai pH</th>
            <th>Nilai Tinggi (cm)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($histories as $history)
            <tr>
                <td>{{ $history->timestamp }}</td>
                <td>{{ $history->device->name ?? '-' }}</td>
                <td>{{ $history->sensor_type ?? '-' }}</td>
                <td>{{ $history->value_temp ?? '-' }}</td>
                <td>{{ $history->value_ph ?? '-' }}</td>
                <td>{{ $history->value_height ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>