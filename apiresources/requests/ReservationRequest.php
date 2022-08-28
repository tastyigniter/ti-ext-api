<?php

namespace Igniter\Api\ApiResources\Requests;

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
        return [
            'table_id' => ['required', 'integer'],
            'location_id' => ['required', 'integer'],
            'guest_num' => ['required', 'integer'],
            'reserve_date' => ['required', 'date_format:Y-m-d'],
            'reserve_time' => ['required', 'date_format:H:i'],
            'first_name' => ['required', 'between:1,48'],
            'last_name' => ['required', 'between:1,48'],
            'email' => ['required', 'email:filter', 'max:96'],
            'telephone' => ['required'],
            'comment' => ['max:520'],
        ];
    }
}
