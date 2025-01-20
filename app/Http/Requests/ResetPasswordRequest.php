<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiRequest;

class ResetPasswordRequest extends ApiRequest
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
            'email' => 'required|email|exists:users,email', // Ensure the email exists in the DB
            'password' => 'required|min:8|confirmed', // Password confirmation validation
            'token' => 'required|string', // Ensure the token is provided
        ];
    }
}
