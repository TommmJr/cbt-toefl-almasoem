<h1>Daftar Ujian</h1>

<ul>
@forelse ($ujians as $ujian)
    <li>
        <a href="{{ route('ujian.show', $ujian->id) }}">
            {{ $ujian->judul }}
        </a>
        <br>
        Status:
        @if ($ujian->isBelumMulai())
            Belum Mulai
        @elseif ($ujian->isAktif())
            Sedang Berlangsung
        @else
            Sudah Selesai
        @endif
    </li>
@empty
    <li>Belum ada ujian.</li>
@endforelse
</ul>
