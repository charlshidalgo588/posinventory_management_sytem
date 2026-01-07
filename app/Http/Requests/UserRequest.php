<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        /** @var \Illuminate\Http\Request $this */
        return match (true) {
            $this->route()->named('user.login') => [
                'email'    => 'required|string|email|max:255',
                'password' => 'required|string|min:8',
            ],
            $this->route()->named('user.store') => [
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|unique:users|max:255',
                'password' => 'required|string|min:8',
                'address'  => 'required|string|max:255',
                'phone'    => 'required|string|max:20',
            ],
            $this->route()->named('user.update') => [
                'name' => 'required|string|max:255',
            ],
            $this->route()->named('user.email') => [
                'email' => 'required|string|email|max:255',
            ],
            $this->route()->named('user.password') => [
                'password' => 'required|confirmed|min:8',
            ],
            default => [],
        };
    }
}
