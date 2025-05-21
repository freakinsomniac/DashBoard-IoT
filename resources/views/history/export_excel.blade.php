<table>
    <thead>
        <tr>
            <th>Waktu</th>
            <th>Device</th>
            <th>Tipe Sensor</th>
            <th>ID Sensor</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach($histories as $history)
            <tr>
                <td>{{ $history->timestamp }}</td>
                <td>{{ $history->device->name ?? '-' }}</td>
                <td>{{ $history->sensor_type }}</td>
                <td>{{ $history->sensor_id }}</td>
                <td>{{ $history->value }}</td>
            </tr>
        @endforeach
    </tbody>
</table>