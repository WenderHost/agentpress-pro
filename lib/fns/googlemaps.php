<?php

namespace AgentPress\googlemaps;

function google_map_api( $api ){
  if( ! defined( 'GOOGLE_MAPS_API_KEY' ) )
    return $api;

  $api['key'] = GOOGLE_MAPS_API_KEY;

  return $api;

}

add_filter('acf/fields/google_map/api', __NAMESPACE__ . '\\google_map_api');