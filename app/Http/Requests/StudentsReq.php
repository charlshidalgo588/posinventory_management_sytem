<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentsReq extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // You can add authorixation logic here  
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:students',
            'age' => 'required|integer|min:0',
            'contact' => 'nullabe|string|max:11',
        ];
    }
    public function messages()
    {
        return[
            'name.required' => 'name is required',
            'email.required' => 'email is required',
            'email.email' => 'email must be a valid email address',
            'email.unique' => 'email has been already taken',
        ];
    }
}
