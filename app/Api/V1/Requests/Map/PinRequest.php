<?php

namespace App\Api\V1\Requests\Map;

use Config;
use Dingo\Api\Http\FormRequest;

class PinRequest extends FormRequest
{
    public function rules()
    {
        return [
            'tags' => 'required',
        ];
    }

    public function authorize()
    {
        return true;
    }

}
