<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TipeSection;

class StoreUjianSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ujian_id'      => ['required'],
            'judul_section' => ['required'],
            'tipe_section'  => [
                'required',
                'string',
                'in:' . implode(',', TipeSection::values()),
            ],
            'urutan'        => ['required'],
            'durasi_menit'  => ['required'],
        ];

        if ($this->session->isSectionExpired()) {
    abort(403, 'Waktu section sudah habis');
}

    }

    

}
