<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class DokumenRequest extends FormRequest
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
            'nama' => 'required|max:255',
            'kriteria' => 'required|numeric|between:1,12',
            'sub_kriteria' => 'max:255',
            'catatan' => 'max:255',
            'file' => 'required_without_all:url|mimes:pdf,png,jpg,jpeg|max:102400',
            'url' => 'required_without_all:file|url|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi!',
            'mimes' => ':attribute harus berupa PDF atau gambar',
            'max' => ':attribute maksimal :max karakter',
            'numeric' => ':attribute harus berupa angka',
            'between' => ':attribute harus diantara 1 sampai 9',
            'required_without_all' => ':attribute harus diisi jika :values tidak diisi',
            'url' => ':attribute harus berupa URL yang valid',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama' => 'Nama',
            'kriteria' => 'Kriteria',
            'sub_kriteria' => 'Sub Kriteria',
            'catatan' => 'Catatan',
            'file' => 'File',
            'url' => 'URL',
        ];
    }

    public function all($keys = null): array
    {
        $data = parent::all($keys);

        $mimeType = $this->file('file') ? $this->file('file')->getMimeType() : $data['tipe'] = 'URL';
        if (str_contains($mimeType, 'pdf')) {
            $data['tipe'] = 'PDF';
        } else if (str_contains($mimeType, 'image')) {
            $data['tipe'] = 'Image';
        }

        if ($data['tipe'] != 'URL') {
            $data['path'] = $this->file('file')->store('dokumen');
        } else {
            $data['path'] = $data['url'];
        }

        return $data;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => Auth::user()->id,
            'program_studi_id' => Auth::user()->programStudi->id,
        ]);
    }
}
