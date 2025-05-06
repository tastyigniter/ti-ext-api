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
        $method = Request::method();

        $rules = [
            'table_id' => ['integer'],
            'location_id' => ['integer'],
            'guest_num' => ['integer'],
            'reserve_date' => ['date_format:Y-m-d'],
            'reserve_time' => ['date_format:H:i'],
            'first_name' => ['between:1,48'],
            'last_name' => ['between:1,48'],
            'email' => ['email:filter', 'max:96'],
            'telephone' => [],
            'comment' => ['max:520'],
        ];
        
        if ($method == 'post') {
            $rules['table_id'][] = 'required';
            $rules['location_id'][] = 'required';
            $rules['guest_num'][] = 'required';
            $rules['reserve_date'][] = 'required';
            $rules['reserve_time'][] = 'required';
            $rules['first_name'][] = 'required';
            $rules['last_name'][] = 'required';
            $rules['email'][] = 'required';
            $rules['telephone'][] = 'required';
        }

        return $rules;
    }
}
