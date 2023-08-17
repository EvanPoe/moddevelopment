<?php

/**
 * @file
 * A form to collect an email address for RSVP details.
 */

namespace Drupal\rsvplist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class RSVPForm extends FormBase
{

    /**
     * {@inheritDoc}
     */
    public function getFormId()
    {
        // telling Drupal that this form has the ID of 'rsvplist_email_form'
        return 'rsvplist_email_form';
    }
    /**
     * {@inheritDoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // Attempt to get the fully loaded node object of the viewed page.
        $node = \Drupal::routeMatch()->getParameter('node');

        // Some pages may not be nodes though and $node will be NULL on those pages.
        // If a node was loaded, get the node ID.
        if (!(is_null($node))) {
            $nid = $node->id();
        } else {
            // If a node could not be loaded, default to 0;
            $nid = 0;
        }

        // Establish the $form render array. It has an email text field,
        // a submit button, and a hidden field containing the node ID
        $form['email'] = [
            '#type' => 'textfield',
            '#title' => t('Email address'),
            '#size' => 25,
            '#description' => t("We will send updates to the email address you provide."),
            '#required' => TRUE,
        ];
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => t('RSVP'),
        ];
        $form['nid'] = [
            '#type' => 'hidden',
            '#value' => $nid,
        ];
        return $form;
    }

    /**
     * {@inheritDoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // get the value from the 'email' form and place it in a variable
        $submitted_email = $form_state->getValue('email');
        $this->messenger()->addMessage(t("The form is working! You entered @entry.", ['@entry' => $submitted_email]));
    }
}
