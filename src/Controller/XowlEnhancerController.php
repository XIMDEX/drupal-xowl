<?php

/**
 * @file
 * Contains \Drupal\xowl\Controller\XowlController.
 */

namespace Drupal\xowl\Controller;
use Drupal\Core\Controller\ControllerBase;

use Drupal\Core\Url;

/**
 * Controller routines for block example routes.
 */
class XowlEnhancerController  extends ControllerBase {
    /**
     * A simple page to explain to the developer what to do.
     */
    public function enhance() {

        $config = \Drupal::config('xowl.settings');
        $xowlEndpoint  = $config->get('endpoint');
        $content = filter_input(INPUT_POST, 'content');




        $client = new \GuzzleHttp\Client();

        $headers = array( );

        $data = array(
            'token' =>  '000-00000-000' ,
            'content' => $content

        );


        $response = $client->post( $xowlEndpoint . 'v1/enhance', [
            'headers' => $headers,
            'allow_redirects' => true,
            'body' => $data,
            'timeout' => 30
        ]);

        // Expected result.
        $data = json_decode( (string)$response->getBody() ,true  );



        header('Content-type: application/json');
        die( json_encode( $data  ) ) ;

    }
    private function subFromString( $string, $start, $length, $sub = '' ) {
        $result =  mb_substr( $string , 0, $start ) . $sub  . mb_substr( $string , $start + $length ) ;
        return $result ;
    }

}