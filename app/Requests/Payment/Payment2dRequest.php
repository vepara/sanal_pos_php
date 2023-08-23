<?php

namespace App\Requests\Payment;

class Payment2dRequest extends BasePaymentRequest
{

    /**
     * @return false|string
     */
    public function toJson(): bool|string
    {
        $parent = parent::toArray();

        $child = get_object_vars($this);

        $data = array_merge($parent, $child);
        return json_encode($data);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $parent = parent::toArray();

        $child = get_object_vars($this);

        $data = array_merge($parent, $child);
        return $data;

    }


}
