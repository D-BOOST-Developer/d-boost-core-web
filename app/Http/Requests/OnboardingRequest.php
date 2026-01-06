<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OnboardingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Business Information
            'business_name' => ['required', 'string', 'max:255'],
            'business_category' => ['required', 'string', 'max:150'],
            'business_logo_path' => ['nullable', 'string'],
            
            // Business Metrics
            'business_age_month' => ['required', 'integer', 'min:0', 'max:1200'], // Max 100 years
            'monthly_revenue_range' => ['required', 'string', 'max:50'],
            
            // Contact Information
            'owner_name' => ['required', 'string', 'max:150'],
            'contact_email' => ['required', 'email', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            
            // Additional Information
            'usage_reason' => ['nullable', 'string', 'max:1000'],
            
            // Module Selection
            'selected_modules' => ['nullable', 'array'],
            'selected_modules.*' => ['exists:modules,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'business_name.required' => 'Nama bisnis wajib diisi.',
            'business_category.required' => 'Kategori bisnis wajib dipilih.',
            'business_age_month.required' => 'Usia bisnis wajib diisi.',
            'business_age_month.integer' => 'Usia bisnis harus berupa angka.',
            'business_age_month.min' => 'Usia bisnis tidak boleh negatif.',
            'monthly_revenue_range.required' => 'Rentang pendapatan bulanan wajib dipilih.',
            'owner_name.required' => 'Nama pemilik wajib diisi.',
            'contact_email.required' => 'Email kontak wajib diisi.',
            'contact_email.email' => 'Format email tidak valid.',
            'selected_modules.*.exists' => 'Modul yang dipilih tidak valid.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'business_name' => 'nama bisnis',
            'business_category' => 'kategori bisnis',
            'business_age_month' => 'usia bisnis',
            'monthly_revenue_range' => 'rentang pendapatan',
            'owner_name' => 'nama pemilik',
            'contact_email' => 'email kontak',
            'contact_phone' => 'nomor telepon',
            'usage_reason' => 'alasan penggunaan',
            'selected_modules' => 'modul yang dipilih',
        ];
    }
}
