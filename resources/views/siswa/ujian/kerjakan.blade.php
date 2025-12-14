<h1>{{ $ujian->judul }}</h1>

<h3>
    Section: {{ $section->judul_section }}
</h3>

<p>
    Sisa waktu: {{ $sisaDetik }} detik
</p>

<hr>

<h4>Daftar Soal</h4>

@forelse ($section->soal as $soal)
    <div style="margin-bottom:20px; padding:10px; border:1px solid #ccc;">
        <p>
            <strong>No {{ $soal->nomor_urut }}</strong><br>
            {{ $soal->pertanyaan }}
        </p>

        @if ($soal->isPilihanGanda())
            <ul>
                <li>A. {{ $soal->pilihan_a }}</li>
                <li>B. {{ $soal->pilihan_b }}</li>
                <li>C. {{ $soal->pilihan_c }}</li>
                <li>D. {{ $soal->pilihan_d }}</li>
            </ul>
        @else
            <p><em>Jawaban essay</em></p>
            <textarea disabled rows="4" cols="50"></textarea>
        @endif
    </div>
@empty
    <p><em>Tidak ada soal di section ini.</em></p>
@endforelse
