<?php

namespace Bitmovin\api\enum;

class CloudRegion
{
    const AWS_PREFIX = "AWS_";
    const GOOGLE_PREFIX = "GOOGLE_";

    const GOOGLE_EUROPE_WEST_1 = 'GOOGLE_EUROPE_WEST_1';
    const AWS_US_EAST_1 = 'AWS_US_EAST_1';
    const AWS_US_WEST_1 = 'AWS_US_WEST_1';
    const AWS_US_WEST_2 = 'AWS_US_WEST_2';
    const AWS_EU_WEST_1 = 'AWS_EU_WEST_1';
    const AWS_EU_CENTRAL_1 = 'AWS_EU_CENTRAL_1';
    const AWS_AP_NORTHEAST_1 = 'AWS_AP_NORTHEAST_1';
    const AWS_AP_NORTHEAST_2 = 'AWS_AP_NORTHEAST_2';
    const AWS_AP_SOUTHEAST_1 = 'AWS_AP_SOUTHEAST_1';
    const AWS_AP_SOUTHEAST_2 = 'AWS_AP_SOUTHEAST_2';
    const AWS_AP_SOUTH_1 = 'AWS_AP_SOUTH_1';
    const AWS_SA_EAST_1 = 'AWS_SA_EAST_1';
    const GOOGLE_US_CENTRAL_1 = 'GOOGLE_US_CENTRAL_1';
    const GOOGLE_US_EAST_1 = 'GOOGLE_US_EAST_1';
    const GOOGLE_US_WEST_1 = 'GOOGLE_US_WEST_1';
    const GOOGLE_ASIA_EAST_1 = 'GOOGLE_ASIA_EAST_1';
    const AZURE_EUROPE_WEST = 'AZURE_EUROPE_WEST';
    const NORTH_AMERICA = 'NORTH_AMERICA';
    const SOUTH_AMERICA = 'SOUTH_AMERICA';
    const EUROPE = 'EUROPE';
    const AFRICA = 'AFRICA';
    const ASIA = 'ASIA';
    const AUSTRALIA = 'AUSTRALIA';
    const KUBERNETES = 'EXTERNAL';
    const AWS = 'AWS';
    const GOOGLE = 'GOOGLE';
    const AUTO = 'AUTO';
    const EXTERNAL = 'EXTERNAL';


    /**
     * @return array(string)
     */
    public static function getAvailableValues()
    {
        $reflection = new \ReflectionClass(get_called_class());

        return array_values($reflection->getConstants());
    }
}