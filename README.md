# Drupal module that will interact with the DHL API 
This Drupal module is used to find DHL office locations.
# Depandancies to run this module
  * PHP Version    - 8.2
  * My SQL Version - 8.0
  * Drupal Version - 10.0
# Steps to run the module
  * Create a folder name as custom inside the Drupal project example - your_project/modules/custom
  * Clone the repo within your Drupal project inside the Drupal project under your_project/modules/custom/
  * After cloning the repo we are able to see location_finder folder to your custom module, Try clearing the Drupal cache by going to "Configuration" -> "Development" -> "Performance" and clicking on 
    "Clear all caches".
  * The next step is to activate or install our custom module by going to "Extend" and finding "Location Finder API Page" Click on the checkbox and then scroll 
    down to the bottom and install the module by clicking the "Install" button.
  * To run this module in the browser and add this endpoint to your site URL like *http://your_site_domain/locations-deatils*.
  * The module should provide a form for entering the country, city, and postal code. For example Czechia, Prague, 11000.
  * After submitting the form, a list of locations should be displayed who are working on weekends as well as those that have an odd number in their address
  * Each location should be output in yaml format.