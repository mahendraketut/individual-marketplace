<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateAccountRequest extends FormRequest
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
            'name' => ['string', 'max:255'],
            'email' => ['string', 'email', 'max:255'],
            'password' => ['string', 'min:8', 'confirmed'],
            'password_confirmation' => ['string', 'min:8'],
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024', // 1MB max
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'image.image' => 'The file must be an image',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg',
            'image.max' => 'The image may not be greater than 1MB'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'validation error',
            'data' => $validator->errors()
        ], 422));
    }
}
