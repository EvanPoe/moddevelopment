<?php

/**
 * @file
 * Contains the settings for administering the RSVP Form
 */

namespace Drupal\rsvplist\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class RSVPSettingsForm extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    // return the form ID you choose for the Form
    public function getFormId() {
        return 'rsvplist_admin_settings';
    }

    /**
     * {@inheritdoc}
     */
    // return an array of the configuration names that will be editable
    protected function getEditableConfigNames() {
        return [
            'rsvplist.settings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    // return a renderable array of the form to display
    public function buildForm(array $form, FormStateInterface $form_state) {
        // return node content types currently on the site (as an array of strings of the Labels)
        $types = node_type_get_names();
        // retrieves the configuration object that is named 
        $config = $this->config('rsvplist.settings');
        // configuration object we can perform further actions on
        $form['rsvplist_types'] = [
            '#type' => 'checkboxes',
            '#title' => $this->t('The content types to enable RSVP collection for'),
            '#default_value' => $config->get('allowed_types'),
            '#options' => $types,
            '#description' => $this->t('On the specified node types, an RSVP option will be available and can be enabled while the node is being edited.'),
        ];
        // unique, final line of building a config form
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    // final method of settings form
    public function submitForm(array &$form, FormStateInterface $form_state) {
        // remove any empty strings from unused options the user selected
        $selected_allowed_types = array_filter($form_state->getValue('rsvplist_types'));
        // sort alphabetically
        sort($selected_allowed_types);

        // load the config object, set allowed types to the config, and save the config
        $this->config('rsvplist.settings')
            ->set('allowed_types', $selected_allowed_types)
            ->save();

        //no validation included, but it's an option
        
        // no return, the config form base class will take it from here
        parent::submitForm($form, $form_state);
    }
}
