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

<a href="{{ route('ujian.index') }}">← Kembali</a>
