<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($siswa as $s)
            <tr>
                <td>{{ $s->user->name }}</td>
                <td>{{ $s->user->email }}</td>
                <td>{{ $s->user->is_active ? 'Aktif' : 'Nonaktif' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
