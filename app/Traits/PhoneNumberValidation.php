<?php

namespace App\Traits;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

trait PhoneNumberValidation
{
    protected function formatPhoneNumber(string $phoneNumber, string $countryCode = 'TZ'): string
    {
        if (empty($phoneNumber)) {
            throw new \InvalidArgumentException(__('Phone number is required'));
        }
        try {
            $formattedNumber = $this->formatInternationalPhoneNumber($phoneNumber, $countryCode);
            if (!$formattedNumber) {
                throw new \InvalidArgumentException(__('Invalid phone number format'));
            }
           return $formattedNumber;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(__('Invalid phone number: ').$e->getMessage());
        }
    }

    public function formatInternationalPhoneNumber($phoneNumber, $regionCode): false|string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($phoneNumber, strtoupper($regionCode));

            if ($phoneUtil->isValidNumber($numberProto)) {
                return $phoneUtil->format($numberProto, PhoneNumberFormat::E164);
            } else {
                return false;
            }
        } catch (NumberParseException $e) {
            return false;
        }
    }

    public function validateWhatsAppNumber($phone): bool
    {
        return (bool)preg_match('/^\d{9,15}$/', $phone);
    }
}
