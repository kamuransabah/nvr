<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
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
        $rules = [
            'baslik' => 'required|string|max:160',
            'ozet' => 'required|string',
            'icerik' => 'required|string',
            'kategori_id' => 'required|integer|exists:kategori,id',
            'kurs_id' => 'nullable|array', // Select2 çoklu seçim için array olmalı
            'kurs_id.*' => 'integer|exists:kurslar,id', // Seçilen her kurs ID geçerli olmalı
            'durum' => 'nullable|in:0,1',
            'resim' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'seo_title' => 'nullable|string|max:160',
            'seo_description' => 'nullable|string|max:160',
            'yayin_tarihi' => 'nullable|date',
        ];


        return $rules;
    }
}
