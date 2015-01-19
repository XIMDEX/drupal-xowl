<?php

/**
 * @file
 * Definition of \Drupal\xowl\Plugin\CKEditorPlugin\Xowl.
 */
 
namespace Drupal\xowl\Plugin\CKEditorPlugin;
 

use Drupal\Component\Plugin\PluginBase ;
use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;
use Drupal\ckeditor\CKEditorPluginInterface ;
/**
 * Defines the "drupallink" plugin.
 *
 * @CKEditorPlugin(
 *   id = "Xowl",
 *   label = @Translation("Drupal link"),
 *   module = "xowl"
 * )
 */
class Xowl extends CKEditorPluginBase implements CKEditorPluginInterface {
    public function isInternal() {
         error_log( 'isInternal:' . $this->getPluginId()  );
        
        return false  ;
    }
    public function getDependencies(Editor $editor) { 
        error_log( 'dependencies' );
        return array() ;
    }
  /**
   * {@inheritdoc}
   */
  public function getFile() {
     error_log( 'fichero' );
    error_log( drupal_get_path('module', 'xowl') . '/js/plugins/xowl/plugin.js'  ) ;
    return drupal_get_path('module', 'xowl') . '/js/plugins/xowl/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
     error_log( 'librerias' );
    print_R( $editor ) ;
    return array(
  //   'core/drupal.ajax',
    );
  }
 
  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    print_R ($editor) ;
    error_log( 'getConfig') ; 
    return array(
//    'drupalLink_dialogTitleAdd' => t('Add Link'),
  //    'drupalLink_dialogTitleEdit' => t('Edit Link'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    error_log( 'botones') ;
     return array(
    //       'TextColor' => array(
    //    'label' => t('Text Color'),
     //  ),
       
    );
  }
  public function getPluginDefinition( ) {
     error_log( 'definición' );

  }

} 