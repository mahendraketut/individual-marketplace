<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
            'name.required' => 'The name field is required.',
            'name.string' => 'The name field must be a string.',
            'name.max' => 'The name field must not exceed 255 characters.',
            'description.string' => 'The description field must be a string.',
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price field must be a number.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.numeric' => 'The quantity field must be a number.',
            'category_id.required' => 'The category_id field is required.',
            'category_id.exists' => 'The selected category_id is invalid.',
            'brand_id.required' => 'The brand_id field is required.',
            'brand_id.exists' => 'The selected brand_id is invalid.',
            'images.*.image' => 'The image must be an image.',
            'images.*.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            'images.*.max' => 'The image may not be greater than 2048 kilobytes.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->first()
        ], 422));
    }
}
