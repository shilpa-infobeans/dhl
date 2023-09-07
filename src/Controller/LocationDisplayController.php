<?php
/**
 * DHL Location Display functions
 *
 * @package dhl-location-api
 */

namespace Drupal\location_finder\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Response;

class LocationDisplayController extends ControllerBase {
    /**
     * Management of the DHL Location Display.
     */
    public function locations() {
       $data = \Drupal::service('session')->get('locations', []);
       $location_data = [];
       if( $data ){
            foreach ($data as $key => $location) {
                $new_location['locationName'] =  $location['name'];
                $address = (array) $location['place']['address'];
                $new_location['address'] =  $address    ;
                $new_location['openingHours'] =  $location['openingHours'];
                $location_data[] = $new_location;
            }
       }
        $yamlData = Yaml::dump( $location_data  );
        \Drupal::service('session')->remove('locations');
        $response = new Response($yamlData);
        $response->headers->set('Content-Type', 'text/yaml');
        return $response;
    }
}
