<?php
/**
 * DHL Location Finder functions
 *
 * @package dhl-location-api
 */

namespace Drupal\location_finder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Management of the DHL Location Finder form.
 */

class DHLLocationFinderController extends FormBase {
    /**
	 * Get Form ID.
	 * 
	 */
    public function getFormId() {
        return "my_module_form";
    }

    /**
	 * Build Form for enter location.
     * 
	 * @param Array $form Contains the form fields.
	 * @param Array $form_state Contains the form states.
     * 
	 * @return array
	 */
    public function buildForm( array $form, FormStateInterface $form_state ) {
        $form['country']    =  [
                                    '#type' => 'select',
                                    '#title' => $this->t('Country'),
                                    '#options' => \Drupal::service('country_manager')->getList(),
                                    '#wrapper_attributes' => ['class' => 'col-md-12'],
                                    '#empty_value' => '',
                                    '#required' => true,
                                ];
        $form['city']       =   [
                                    '#type' => 'textfield',
                                    '#title' => 'City Name',
                                    '#required' => true,
                                ];
        $form['postal_code'] =  [
                                    '#type' => 'textfield',
                                    '#title' => 'Postal Code',
                                    '#required' => true,
                                ];
        $form['submit'] =       [
                                    '#type' => 'submit',
                                    '#value' => 'Submit',
                                ];
        return $form;
    }

    /**
	 * Validate Form fields.
     * 
	 * @param Array $form Contains the form fields.
	 * @param Object $form_state Contains the form states.
     * 
	 * @return string
	 */
    public function validateForm( array &$form, FormStateInterface $form_state ) {
        if ( strlen( $form_state->getValue('country') ) < 1) {
            $form_state->setErrorByName('country','Please make sure country name length is more than 5');
        }

        if ( strlen( $form_state->getValue('city') ) < 3 ) {
            $form_state->setErrorByName('country','Please make sure city name length is more than 5');
        }

        if ( strlen( $form_state->getValue('postal_code') ) < 4) {
            $form_state->setErrorByName('country','Please make sure postal code length is more than 5');
        }
    }

    /**
	 * Submit data and display location in yml.
     * 
	 * @param Array $form Contains the form fields.
	 * @param Object $form_state Contains the form states.
     * 
	 * @return string
	 */
    public function submitForm( array &$form, FormStateInterface $form_state ) {  
        \Drupal::messenger()->addMessage("User Details Submitted successfully");
        $country        = $form_state->getValue('country');
        $city           = $form_state->getValue('city');
        $postal_code    = $form_state->getValue('postal_code');
        $locations_data = $this->fetch_locations( $country, $city, $postal_code );
        $new_location = [];

       if( $locations_data ) {
            foreach ( $locations_data['locations'] as $key => $location ) {
                $location_url   = explode('-',$location['url']);
                $location_id    = $location_url[1];
                $opening_hours  = $location['openingHours'];
                $desiredKey     = 'dayOfWeek';
                $desiredValue   = 'http://schema.org/Sunday';

                if ( strlen($location_id) % 2 == 0 || (isset($opening_hours[$desiredKey]) && $opening_hours[$desiredKey] === $desiredValue)) {
                    $new_location[] = $location;
                } 
            }
            \Drupal::service('session')->set('locations', $new_location);
            $form_state->setRedirect('location_finder.display_location');
       }
    }

    /**
	 * DHL Location API calling.
     * 
	 * @param string $country Contains the country code.
	 * @param string $city Contains the city.
	 * @param int    $postal_code Contains the pin code.
     * 
	 * @return array
	 */
    public function fetch_locations( $country, $city, $postal_code ) {
        $data = '';
        // Define the API endpoint URL
        $api_url = API_URL.'?countryCode='.$country.'&postalCode='.$postal_code.'&addressLocality='.$city;

        // Set up authentication if required
        $headers = [
            'DHL-API-Key' => DHL_API_KEY,
        ];

        // Make a GET request to the API
        $response = \Drupal::httpClient()->get($api_url, ['headers' => $headers]);

        // Check the response status code
        if ($response->getStatusCode() == 200) {
            // Process the JSON response
            $data = json_decode($response->getBody(), true );
        } else {
            \Drupal::logger('API request failed with status code ' . $response->getStatusCode(), 'error');
        }
        return $data;
    }
}