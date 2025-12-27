<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DersRequest extends FormRequest
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
            'baslik' => 'required|string|max:250',
            'ozet' => 'nullable|string|max:250',
            'bolum_id' => 'required|integer|exists:bolumler,id',
            'ders_suresi' => 'required|integer',
            'video_kaynak_id' => 'nullable|string',
            'sira' => 'nullable|integer',
            'durum' => 'nullable|boolean',
        ];
    }
}
