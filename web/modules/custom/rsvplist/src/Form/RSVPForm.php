<?php

/**
 * @file
 * A form to collect an email address for RSVP details.
 */

namespace Drupal\rsvplist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class RSVPForm extends FormBase {

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    // telling Drupal that this form has the ID of 'rsvplist_email_form'
    return 'rsvplist_email_form';
  }
  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
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

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue('email');
    if (!(\Drupal::service('email.validator')->isValid($value))) {
      $form_state->setErrorByName(
        'email',
        $this->t('It appears that %mail is not a valid email. Please try again', ['%mail' => $value])
      );
    }
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // get the value from the 'email' form and place it in a variable
    // $submitted_email = $form_state->getValue('email');
    // $this->messenger()->addMessage(t("The form is working! You entered @entry.", ['@entry' => $submitted_email]));
    try {
      // Get current user ID. Not a full user object
      $uid = \Drupal::currentUser()->id();

      // Demonstration for how to load a full user object of the current user.
      // This $full_user variable is not needed for this code,
      // but is shown for demonstration purposes.
      // Often you need to work with the values of the fields set on the user Entity (name, created date, etc. )
      $full_user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

      // Obtain values as entered into the From
      $nid = $form_state->getValue('nid');
      $email = $form_state->getValue('email');

      $current_time = \Drupal::time()->getRequestTime();
      // End Phase 1

      // Begin Phase 2
      // Start to build a query builder object $query.
      // https://www.drupal.org/docs/8/api/database-api/insert-queries
      $query = \Drupal::database()->insert('rsvplist');

      // Specify the fields that the query will insert into.
      $query->fields([
        'uid',
        'nid',
        'mail',
        'created',
      ]);

      // Set the values of the fields we selected.
      // Note that they must be in the same order as we defined them
      // in the $query->fields([...]) above.
      $query->values([
        $uid,
        $nid,
        $email,
        $current_time,
      ]);

      // Execute the query!
      // Drupal handles the exact syntax of the query automatically!
      $query->execute();
      // End Phase 2

      // Begin Phase 3: Display a success message

      // Provide the form submitter a nice message.
      \Drupal::messenger()->addMessage(
        t('Thank you for your RSVP, you are on the list for the event!')
      );
      // End Phase 3
    } catch (\Exception $error) {
      \Drupal::messenger()->addError(
        t('Unable to save RSVP settings at this time due to database error.
         Please try again.')
      );
    }
  }
}
