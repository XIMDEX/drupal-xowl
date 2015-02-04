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
         
        return false  ;
    }
    public function getDependencies(Editor $editor) { 
         return array() ;
    }
  /**
   * {@inheritdoc}
   */
  public function getFile() {
     return drupal_get_path('module', 'xowl') . '/js/plugins/xowl/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return array(
  //   'core/drupal.ajax',
    );
  }
 
  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
      return array(
//    'drupalLink_dialogTitleAdd' => t('Add Link'),
  //    'drupalLink_dialogTitleEdit' => t('Edit Link'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
      return array(
    //       'TextColor' => array(
    //    'label' => t('Text Color'),
     //  ),
       
    );
  }
  public function getPluginDefinition( ) {

  }

} 