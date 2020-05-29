<?php

namespace Drupal\monitor_status_agent\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

/**
 * Class MonitorStatusAgentController.
 */
class MonitorStatusAgentController {

  /**
   * .
   */
  public function access(AccountInterface $account) {
    return AccessResult::allowedIf($account->id() === '1');
  }

}
