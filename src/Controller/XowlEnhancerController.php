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
     $service = new \Drupal\xowl\Service\StanbolService( $xowlEndpoint ) ;
    $content = filter_input(INPUT_POST, 'content');


    $cleaner = new \Drupal\xowl\Service\StanbolCleaner( $content , ' ' ) ;
    $cleaned = $cleaner->clean();
    $stanbolResponse  = $service->suggest( $cleaned  )->getData() ;


    foreach( $stanbolResponse['semantic'] as $key => $values ) {

      $stanbolResponse['semantic'][ $key ]['start'] = $cleaner->getNewOffset( $values['start']);
      $stanbolResponse['semantic'][ $key ]['end'] =   $cleaner->getNewOffset( $values['end'] ,$values['end'] -  $values['start']   );


    }


  $text = $content ;

  $offset = 0 ;

  foreach( $stanbolResponse['semantic'] as $key => $values ) {
      $entity = $values['entities'][0] ;
      $numSuggestions = count( $values['entities'] ) ;

      $tag = "<a href=\"{$entity['uri']}\" class=\"xowl-suggestion\" data-cke-annotation=\"{$entity['label']}\" data-cke-type=\"{$entity['type']}\" data-cke-suggestions=\"{$numSuggestions}\">{$values['text']}</a>";


      $start = $values['start'] + $offset ;
      $end = $values['end'] + $offset ;

      $length = mb_strlen($values['text'] ) ; 

      $text = $this->subFromString( $text , $start, $length, $tag ) ;

      $offset +=  ( mb_strlen( $tag) -  mb_strlen( $values['text']) )     ;

  }
//   print_R( $text ) ;
  
  $stanbolResponse['text'] = $text ;







    header('Content-type: application/json');
    die( json_encode( $stanbolResponse ) ) ;
 
  }
     private function subFromString( $string, $start, $length, $sub = '' ) {
        $result =  mb_substr( $string , 0, $start ) . $sub  . mb_substr( $string , $start + $length ) ;
        return $result ;
    }
 
}