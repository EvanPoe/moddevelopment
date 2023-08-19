<?php

/**
 * @file
 * Creates a block which displays the RSVPForm contained in RSVPForm.php
 */

namespace Drupal\rsvplist\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;


/**
 * Provides the RSVP main block.
 *
 * @Block(
 *   id = "rsvp_block",
 *   admin_label = @Translation("The RSVP Block")
 * )
 */
class RSVPBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // returns a renderable form array (if it is passed a form Class that Implements FormInterface)
    return \Drupal::formBuilder()->getForm('Drupal\rsvplist\Form\RSVPForm');
  }

  /**
   * {@inheritdoc}
   */
  public function blockAccess(AccountInterface $account) {
    // If viewing a node, get the fully loaded node object.
    $node = \Drupal::routeMatch()->getParameter('node');
    // perform an access check to determine if the current user has correct permission
    if (!(is_null($node))) {
      // allowedIfHasPermission takes two parameters: account to check, and the machine name of the permission to check for
      return AccessResult::allowedIfHasPermission($account, 'view rsvplist');
    }

    return AccessResult::forbidden();


    // Some pages may not be nodes, although we could not display the block using the Block Settings through
    // the Block UI at /admin/structure/block, instead we are programmatically controlling to only display
    // this block on node pages using AccessResult::allowedIfHasPermission($account, 'view rsvplist')

    // if (!(is_null($node))) {
    //   $enabler = \Drupal::service('rsvplist.enabler');
    //   if ($enabler->isEnabled($node)) {

    //     return AccessResult::allowedIfHasPermission($account, 'view rsvplist');
    //   }
    // }

    // return AccessResult::forbidden();
  }
}
