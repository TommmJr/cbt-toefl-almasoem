@php
    $isExpired = $sisaDetik <= 0;
@endphp

<h3>
    Sisa waktu section:
    <span id="timer">{{ $sisaDetik }}</span> detik
</h3>

<hr>

@forelse ($section->soal as $soal)
    <div style="margin-bottom:20px; padding:10px; border:1px solid #ccc;">
        <p>
            <strong>No {{ $soal->nomor_urut }}</strong><br>
            {{ $soal->pertanyaan }}
        </p>

        {{-- ================= PILIHAN GANDA ================= --}}
        @if ($soal->isPilihanGanda())
            @foreach ($soal->opsi_jawaban as $key => $label)
                <label style="display:block;">
                    <input type="radio"
                        name="jawaban_{{ $soal->id }}"
                        value="{{ $key }}"
                        @checked(
                            isset($jawabanSiswa[$soal->id]) &&
                            $jawabanSiswa[$soal->id]->jawaban_pilihan === $key
                        )
                        @disabled($isExpired)
                        onchange="simpanJawaban({{ $soal->id }}, '{{ $key }}')"
                    >
                    {{ $key }}. {{ $label }}
                </label>
            @endforeach

        {{-- ================= ESSAY ================= --}}
        @else
            <textarea
                rows="4"
                cols="70"
                placeholder="{{ $isExpired ? 'Waktu habis' : 'Ketik jawaban...' }}"
                @disabled($isExpired)
                oninput="debounceSimpan({{ $soal->id }}, this.value)"
            >{{ $jawabanSiswa[$soal->id]->jawaban_essay ?? '' }}</textarea>
        @endif
    </div>
@empty
    <p><em>Tidak ada soal di section ini.</em></p>
@endforelse

<hr>

{{-- ================= SUBMIT SECTION ================= --}}
@if (! $isExpired)
    <div style="margin-top: 20px;">
        <button
            type="button"
            onclick="autoSubmitSection()"
            id="btn-submit-manual"
            style="padding:10px 20px; font-size:16px; cursor:pointer; background:#004e92; color:white; border:none; border-radius:5px;"
        >
            Lanjut ke Section Berikutnya →
        </button>
    </div>
@else
    <p><em>Waktu section sudah habis. Mengalihkan...</em></p>
@endif

<script>
/* ================= STATE ================= */
let sisaDetik = {{ $sisaDetik }};
const timerEl = document.getElementById('timer');
let debounceTimer = {};
let sudahSubmit = false;

/* ================= AUTOSAVE JAWABAN ================= */
function simpanJawaban(soalId, jawaban) {
    console.log('KIRIM JAWABAN', soalId, jawaban);

    fetch("{{ route('siswa.ujian.simpanJawaban') }}", {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            sesi_id: {{ $sesi->id }},
            soal_id: soalId,
            jawaban: jawaban
        })
    })
    .then(res => res.json())
    .then(data => {
        console.log('RESPON SERVER', data);
    })
    .catch(err => {
        console.error('AUTOSAVE ERROR', err);
    });
}

/* ================= DEBOUNCE ESSAY ================= */
function debounceSimpan(soalId, jawaban) {
    clearTimeout(debounceTimer[soalId]);
    debounceTimer[soalId] = setTimeout(() => {
        simpanJawaban(soalId, jawaban);
    }, 500);
}

/* ================= SUBMIT SECTION ================= */
    function autoSubmitSection() {
        fetch("{{ route('siswa.ujian.submitSection') }}", {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                sesi_id: {{ $sesi->id }}
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        })
        .catch(err => console.error(err));
    }

/* ================= TIMER ================= */
if (timerEl) {
    const interval = setInterval(() => {
        sisaDetik--;

        if (sisaDetik <= 0) {
            sisaDetik = 0;
            timerEl.innerText = 0;
            clearInterval(interval);

            document
                .querySelectorAll('input, textarea, button')
                .forEach(el => el.disabled = true);

            autoSubmitSection();
        } else {
            timerEl.innerText = sisaDetik;
        }
    }, 1000);
}
</script>
