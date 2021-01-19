<?php

namespace Igniter\Api\ApiResources\Requests;

use Igniter\Api\Classes\ApiRequest;

class ReservationRequest extends ApiRequest
{
    public function rules()
    {
        return [
            ['table_id', 'lang:admin::lang.reservations.column_table', 'required|integer'],
            ['location_id', 'lang:igniter.reservation::default.label_location', 'required|integer'],
            ['guest_num', 'lang:igniter.reservation::default.label_guest_num', 'required|integer'],
            ['reserve_date', 'lang:igniter.reservation::default.label_date', 'required|date_format:Y-m-d'],
            ['reserve_time', 'lang:igniter.reservation::default.label_time', 'required|date_format:H:i'],
            ['first_name', 'lang:igniter.reservation::default.label_first_name', 'required|between:1,48'],
            ['last_name', 'lang:igniter.reservation::default.label_last_name', 'required|between:1,48'],
            ['email', 'lang:igniter.reservation::default.label_email', 'required|email:filter|max:96'],
            ['telephone', 'lang:igniter.reservation::default.label_telephone', 'required'],
            ['comment', 'lang:igniter.reservation::default.label_comment', 'max:520'],
        ];
    }
}
