<?php

namespace App\Http\Requests\Admin\Contract;

use App\Http\Requests\CoreRequest;
use App\Traits\CustomFieldsRequestTrait;

class UpdateRequest extends CoreRequest
{
    use CustomFieldsRequestTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // "Sans date d'echeance" : la date de fin est ignoree, meme si
        // le champ masque contient encore une valeur.
        if ($this->without_duedate == 'yes' || trim((string) $this->end_date) === '') {
            $this->merge(['end_date' => null]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $setting = company();
        $rules = [
            'client_id' => 'required',
            'subject' => 'required',
            'amount' => 'required',
            'contract_type' => 'required|exists:contract_types,id',
            'start_date' => 'required|date_format:"' . $setting->date_format . '"',
            'end_date' => 'nullable|date_format:"' . $setting->date_format . '"|after_or_equal:start_date',
        ];

        $rules = $this->customFieldRules($rules);

        return $rules;
    }

    public function attributes()
    {
        $attributes = [];

        $attributes = $this->customFieldsAttributes($attributes);

        return $attributes;
    }

}
