<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTagRequest extends FormRequest
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
            'name' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('tags')->ignore($this->route('tag'))
            ],
            'slug' => [
                'nullable', 
                'string', 
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('tags')->ignore($this->route('tag'))
            ],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Il nome del tag è obbligatorio.',
            'name.unique' => 'Questo nome di tag è già stato utilizzato.',
            'name.max' => 'Il nome del tag non può superare i 255 caratteri.',
            'slug.unique' => 'Questo slug è già stato utilizzato.',
            'slug.regex' => 'Lo slug può contenere solo lettere minuscole, numeri e trattini.',
            'color.regex' => 'Il colore deve essere un codice esadecimale valido (es. #FF0000).',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('name') && !$this->has('slug')) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->name)
            ]);
        }
    }
}

