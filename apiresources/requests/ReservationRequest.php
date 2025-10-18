<?php

namespace Igniter\Api\ApiResources\Requests;

use Illuminate\Support\Facades\Request;

use System\Classes\FormRequest;

class ReservationRequest extends FormRequest
{
    public function attributes()
    {
        return [
            'table_id' => lang('admin::lang.reservations.column_table'),
            'location_id' => lang('igniter.reservation::default.label_location'),
            'guest_num' => lang('igniter.reservation::default.label_guest_num'),
            'reserve_date' => lang('igniter.reservation::default.label_date'),
            'reserve_time' => lang('igniter.reservation::default.label_time'),
            'first_name' => lang('igniter.reservation::default.label_first_name'),
            'last_name' => lang('igniter.reservation::default.label_last_name'),
            'email' => lang('igniter.reservation::default.label_email'),
            'telephone' => lang('igniter.reservation::default.label_telephone'),
            'comment' => lang('igniter.reservation::default.label_comment'),
        ];
    }

    public function rules()
    {
        $rules = [
            'table_id' => ['integer', 'sometimes', 'required'],
            'location_id' => ['integer', 'sometimes', 'required'],
            'guest_num' => ['integer', 'sometimes', 'required'],
            'reserve_date' => ['date_format:Y-m-d', 'sometimes', 'required'],
            'reserve_time' => ['date_format:H:i', 'sometimes', 'required'],
            'first_name' => ['between:1,48', 'sometimes', 'required'],
            'last_name' => ['between:1,48', 'sometimes', 'required'],
            'email' => ['email:filter', 'max:96', 'sometimes', 'required'],
            'telephone' => ['sometimes', 'required'],
            'comment' => ['max:520'],
        ];

        return $rules;
    }
}
