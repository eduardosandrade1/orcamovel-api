<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'clientName' => 'required|string|max:255',
            'colorVehicle' => 'required|string|max:255',
            'contactValue' => 'required|string|max:255',
            'infosVehicle' => 'array',
            'plateName' => 'required|string|max:10',
            'priceParts' => 'required|string',
            'typeParts' => 'required|array',
            'typeService' => 'required|array',
            'typeVehicle' => 'required|string|max:100',
        ];
    }
}
