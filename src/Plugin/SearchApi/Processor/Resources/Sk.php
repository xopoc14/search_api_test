<?php

/**
 * @file
 * Contains \Drupal\search_api\Plugin\SearchApi\Processor\Resources\Sk.
 */

namespace Drupal\search_api\Plugin\SearchApi\Processor\Resources;

class Sk implements unicodeList {

  /**
   * {@inheritdoc}
   */
  public function getRegularExpression() {
    return '\x{21}\x{23}\x{25}';
  }
}