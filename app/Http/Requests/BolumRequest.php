<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BolumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bolum_adi' => 'required|string|max:250',
            'aciklama' => 'nullable|string|max:250',
            'sira' => 'nullable|integer',
            'durum' => 'nullable|boolean',
        ];
    }

    public function messages() {
        return [
            'bolum_adi.max' => 'Bölüm adı en fazla 250 karakter olabilir.',
            // … gerekirse
        ];
    }
}
