<?php

declare(strict_types=1);

namespace Drupal\drupal_site_forms\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Drupal site forms routes.
 */
final class DrupalSiteFormsController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
