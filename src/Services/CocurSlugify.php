<?php

namespace Drupal\unleashed\Services;

use Cocur\Slugify\Slugify;

/**
 * Slugifies text
 */
class CocurSlugify {

  /**
   * {@inheritdoc}
   */
  /**
   * Show the author of the node.
   *
   * @param string $text
   * The text to be slugified
   *
   * @return string
   * Return the slugified version of the string.
   */
  public function slugify ($text, $delimiter) {
    $slugify = new Slugify();
    return $slugify->slugify($text, $delimiter);
  }
}
