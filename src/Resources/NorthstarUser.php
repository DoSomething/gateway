<?php

namespace DoSomething\Northstar\Resources;

use DoSomething\Northstar\Common\APIResponse;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class NorthstarUser extends APIResponse
{
    /**
     * Create a new User from the given API response.
     * @param $attributes
     */
    public function __construct($attributes)
    {
        parent::__construct($attributes);
    }

    /**
     * Get the user's display name.
     * @return mixed|string
     */
    public function displayName()
    {
        if (! empty($this->first_name) && ! empty($this->last_name)) {
            return $this->first_name.' '.$this->last_name;
        }

        if (! empty($this->first_name) && ! empty($this->last_initial)) {
            return $this->first_name.' '.$this->last_initial.'.';
        }

        return $this->id;
    }

    /**
     * Get the user's formatted mobile number.
     *
     * @param string $fallback - Text to display if no mobile is set
     * @return mixed|string
     */
    public function prettyMobile($fallback = '')
    {
        if (!isset($this->mobile)) {
            return $fallback;
        }

        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $formattedNumber = $phoneUtil->parse($this->mobile, 'US');

            return $phoneUtil->format($formattedNumber, PhoneNumberFormat::INTERNATIONAL);
        } catch (\libphonenumber\NumberParseException $e) {
            return $this->number;
        }
    }
}
