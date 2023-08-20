<?php

/**
 * @file
 * Contains the RSVP Enabler service.
 */

namespace Drupal\rsvplist;

use Drupal\Core\Database\Connection;
use Drupal\node\Entity\Node;

class EnablerService {
  // define the database connection object that will be injected into this service
  protected $database_connection;

  // define the constuctor of this EnablerService Class
  public function __construct(Connection $connection) {
    $this->database_connection = $connection;
  }

  /**
   * Checks if an individual node is RSVP enabled.
   *
   * @param Node $node
   * @return bool
   *  whether or not the node is enabled for the RSVP functionality.
   */
  public function isEnabled(Node &$node) {
    if ($node->isNew()) {
      return FALSE;
    }
    try {
      $select = $this->database_connection->select('rsvplist_enabled', 're');
      $select->fields('re', ['nid']);
      $select->condition('nid', $node->id());
      $results = $select->execute();

      // return TRUE/FALSE if the nid is found/not found
      return !(empty($results->fetchCol()));
    } catch (\Exception $error) {
      \Drupal::messenger()->addError(
        t('Unable to determine RSVP settings at this time. Please try again.')
      );
      return NULL;
    }
  }

  /**
   * Sets an individual node to be RSVP enabled.
   *
   * @param Node $node
   * @throws Exception
   */
  public function setEnabled(Node $node) {
    // takes a node parameter and inserts its ID into the rsvplist_enable table
    try {
      if (!($this->isEnabled($node))) {
        $insert = $this->database_connection->insert('rsvplist_enabled');
        $insert->fields(['nid']);
        $insert->values([$node->id()]);
        $insert->execute();
      }
    } catch (\Exception $error) {
      \Drupal::messenger()->addError(
        t('Unable to save RSVP settings at this time. Please try again.')
      );
    }
  }

  /**
   * Deletes RSVP enabled settings for an individual node.
   *
   * @param Node $node
   */
  public function delEnabled(Node $node) {
    try {
      $delete = $this->database_connection->delete('rsvplist_enabled');
      $delete->condition('nid', $node->id());
      $delete->execute();
    } catch (\Exception $error) {
      \Drupal::messenger()->addError(
        t('Unable to save RSVP settings at this time. Please try again.')
      );
    }
  }
}
