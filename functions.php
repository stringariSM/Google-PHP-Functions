<?php
define('GOOGLE_API_KEY', 'xxx-xxx-xxx');
define('GOOGLE_GEOCODING_KEY', 'xxx-xxx-xxx');

public function distance($origin, $destination, $mode='driving')
{
    $origin = str_replace(' ','+',$origin);
    $destination = str_replace(' ','+',$destination);
    $details = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$origin&destinations=$destination&mode=$mode&sensor=false&key=".GOOGLE_API_KEY;
    $json = file_get_contents($details);
    $details = json_decode($json, TRUE);
    return $details;
}


public function coordToStreet($latitude, $longitude)
{
    $details = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&key=".GOOGLE_API_KEY;
    $json = file_get_contents($details);
    $details = json_decode($json, TRUE);

    return $details;
}

public function getAddress($address)
{
    $httpClient = new \Http\Adapter\Guzzle6\Client();
    $provider = new \Geocoder\Provider\GoogleMaps\GoogleMaps($httpClient, null, getenv('GOOGLE_GEOCODING_KEY'));
    $geocoder = new \Geocoder\StatefulGeocoder($provider, 'en');

    $result = $geocoder->geocodeQuery(GeocodeQuery::create($address));
    $first = $result->first();

    $adminLevel = $first->getAdminLevels();
    $state = isset($adminLevel) && $adminLevel ? $adminLevel->get(1) : NULL;
    $city = isset($adminLevel) && $adminLevel ? $adminLevel->get(2) : NULL;

    $data = array(
        'formatted_address' => $first->getFormattedAddress(),
        'latitude'          => $first->getCoordinates()->getLatitude(),
        'longitude'         => $first->getCoordinates()->getLongitude(),
        'street_number'     => $first->getStreetNumber(),
        'street'            => $first->getStreetName(),
        'sub_locality'      => $first->getSubLocality(),
        'zip_code'          => $first->getPostalCode(),
        'city'              => isset($city) && $city ? $city->getName() : NULL,
        'state'             => isset($state) && $state ? $state->getName() : NULL,
    );
    $data = (object) $data;

    return $data;
}
