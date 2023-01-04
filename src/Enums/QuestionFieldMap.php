<?php

namespace Marshmallow\NovaFormbuilder\Enums;

use Marshmallow\NovaFormbuilder\Enums\Traits\Mutators;

enum QuestionFieldMap: string
{
    use Mutators;

    case FIRST_NAME = 'first_name';
    case LAST_NAME = 'last_name';
    case COMPANY_NAME = 'company_name';
    case ADDRESS = 'address';
    case COUNTRY_ID = 'country_id';
    case EMAIL = 'email';
    case PHONE_NUMBER = 'phone_number';
    case CITY = 'city';
    case STREET = 'street';
    case ZIPCODE = 'zipcode';
    case HOUSE_NUMBER = 'house_number';
    case HOUSE_NUMBER_ADDON = 'house_number_addon';

    public function title(): string
    {
        return match ($this) {
            self::EMAIL => __('E-mail'),
            self::FIRST_NAME => __('First name'),
            self::LAST_NAME => __('Last name'),
            self::COMPANY_NAME => __('Company name'),
            self::PHONE_NUMBER => __('Phone number'),
            self::ADDRESS => __('Address'),
            self::COUNTRY_ID => __('Country ID'),
            self::CITY => __('City'),
            self::STREET => __('Street'),
            self::ZIPCODE => __('Zipcode'),
            self::HOUSE_NUMBER => __('House number'),
            self::HOUSE_NUMBER_ADDON => __('House number addon'),
        };
    }
}
