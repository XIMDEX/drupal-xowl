<?php

namespace Drupal\xowl\Form ;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class ServerForm extends FormBase  {

    /**
     * {@inheritdoc}.
     */
    public function getFormId() {
        return 'xowl_endpoint';
    }

    /**
     * {@inheritdoc}.
     */
    public function buildForm(array $form, FormStateInterface $form_state) {

        $config = \Drupal::config('xowl.settings');
        $currentValue  = $config->get('endpoint');


        $form['overview'] = array(
            '#markup' => t('This interface allows the user to integrate the XOwl module with Drupal'),
            '#prefix' => '<p>',
            '#suffix' => '</p>',
        );

        $form['xowl_endpoint'] = array(
            '#title' => t('Endpoint'),
            '#description' => t('Set the endpoint of the XOwl server. Example: http://domain.com:port/xowl'),
            '#type' => 'textfield',
            '#required' => TRUE,
            '#default_value' => $currentValue ,
        );
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Save'),
            '#button_type' => 'primary',
        );

        $form['overview2'] = array(
            '#prefix' => '<p>',
            '#markup' => t('This interface allows the user to select content types that will be enabled to use Xowl for enrichment'),
            '#suffix' => '</p>',
        );





        // Get the content_types names:
        $contentTypes = node_type_get_names();



        $currentValues = $config->get('enabledContentTypes', array());




        $form['xowl_enabled_content_types'] = array(
            '#type' => 'checkboxes',
            '#title' => t('Select the content types that will have Xowl plugin'),
            '#options' => $contentTypes,
            '#default_value' => $currentValues,
        );

        $current_forms_values = \Drupal\Component\Utility\String::checkPlain( $config->get('allowedForms', ''));
        $form['xowl_allowed_forms'] = array(
            '#type' => 'textarea',
            '#title' => t('Set other allowed forms where it\'ll use the xowl plugin'),
            '#description' => t('Set a set of forms separated by commas'),
            '#default_value' => $current_forms_values,
        );


        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        /*
        if (strlen($form_state->getValue('phone_number')) < 3) {
          $form_state->setErrorByName('phone_number', $this->t('The phone number is too short. Please enter a full phone number.'));
        }
        */
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $value = $form_state->getValue('xowl_endpoint')  ;

        \Drupal::configFactory()->getEditable('xowl.settings')
            ->set('endpoint',  $form_state->getValue('xowl_endpoint' ) )
            ->set('enabledContentTypes',  $form_state->getValue('xowl_enabled_content_types' ) )
            ->set('allowedForms',  $form_state->getValue('xowl_allowed_forms' ) )
            ->save();
        //   \Drupal::config()->set('xowl_endpoint',  $value ) ;

        drupal_set_message($this->t('XOwl endpoint updated to @url', array('@url' => $form_state->getValue('xowl_endpoint'))));


    }

}
