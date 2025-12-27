<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KursRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_id'      => ['required','integer','exists:kategori,id'],
            'kurs_adi'         => ['required','string'],
            'permalink'        => ['nullable','string'],
            'ozet'             => ['required','string'],
            'aciklama'         => ['required','string'],

            'gecme_notu'       => ['nullable','integer'],
            'kurs_puani'       => ['nullable','integer'],
            'fiyat'            => ['nullable','integer'],
            'kdv_orani'        => ['nullable','integer'],
            'ucretsiz'         => ['required','in:E,H'],
            'egitim_suresi'    => ['nullable','string'],
            'egitim_sureci'    => ['nullable','string'],
            'sertifika_turu'   => ['nullable','string'],
            'kitap_destegi'    => ['nullable','string'],
            'sinav_basari_orani'=> ['nullable','integer'],
            'ders_sayisi'      => ['nullable','integer'],
            'egitim_seviyesi'  => ['nullable','string'],

            'seo_title'        => ['nullable','string'],
            'seo_description'  => ['nullable','string'],
            'durum'            => ['nullable'],

            // JSON alanlar
            'belgeler'             => ['nullable','array'],
            'belgeler.*'           => ['string'],
            'ozellikler'           => ['nullable','array'],
            'ozellikler.*.ozellik' => ['nullable','string'],
            'neler_ogrenecegim'         => ['nullable','array'],
            'neler_ogrenecegim.*.metin' => ['nullable','string'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.exists' => 'Seçilen kategori mevcut değil.',
            'ucretsiz.in'        => 'Ücretsiz alanı yalnızca E veya H olabilir.',
        ];
    }

}
