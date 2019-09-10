<?php

namespace DoSomething\Gateway\Resources;

use DoSomething\Gateway\Common\ApiResponse;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class NorthstarUser extends ApiResponse implements ResourceOwnerInterface
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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'last_accessed_at',
        'last_messaged_at',
        'last_authenticated_at',
    ];

    /**
     * Get the user's formatted mobile number.
     *
     * @param string $fallback - Text to display if no mobile is set
     * @return mixed|string
     */
    public function prettyMobile($fallback = '')
    {
        if (! isset($this->mobile)) {
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

    /**
     * Returns the identifier of the authorized resource owner.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->attributes['id'];
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }
}
