<h1>Masukkan Token Ujian</h1>

@if (session('error'))
    <p style="color:red">{{ session('error') }}</p>
@endif

<form method="POST" action="{{ route('siswa.ujian.akses.post') }}">
    @csrf

    <label>Kode Token</label><br>
    <input type="text" name="kode_token" maxlength="6" required>
    <br><br>

    <button type="submit">Mulai Ujian</button>
</form>

<a href="{{ route('siswa.ujian.index') }}">← Kembali</a>
