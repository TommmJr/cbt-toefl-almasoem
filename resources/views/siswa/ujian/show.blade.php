<h1>{{ $ujian->judul }}</h1>

<p>{{ $ujian->deskripsi }}</p>

<h3>Section Ujian</h3>
<ul>
@foreach ($ujian->sections as $section)
    <li>
        {{ $section->judul_section }}
        ({{ $section->durasi_menit }} menit)
    </li>
@endforeach
</ul>

<hr>

<a href="{{ route('siswa.ujian.akses') }}">
    <button>
        Mulai Ujian
    </button>
</a>


<a href="{{ route('siswa.ujian.index') }}">← Kembali</a>
