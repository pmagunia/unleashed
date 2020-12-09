<?php

namespace Drupal\unleashed\Plugin\Field\FieldFormatter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\unleashed\Services\CocurSlugify;

/**
 * Plugin implementation of the 'Rot' formatter.
 *
 * @FieldFormatter(
 *   id = "rot",
 *   label = @Translation("ROT-13 text"),
 *   field_types = {
 *     "string",
 *     "text_with_summary",
 *     "text",
 *     "text_long",
 *     "string_long",
 *   },
 *   edit = {
 *     "editor" = "form"
 *   },

 * )
 */
class RotFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'delimiter' => '-',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = array();

    $element['delimiter'] = array(
      '#title' => t('Slug Delimiter'),
      '#type' => 'textfield',
      '#maxlength' => 1,
      '#default_value' => $this->getSetting('delimiter'),
      '#description' => t('The slugified ROT-13 text will be separated with this character.'),
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();

    $delimiter = $this->getSetting('delimiter');
    if ('_empty' == $delimiter) {
      $summary[] = t('No separator has been defined.');
    }
    else {
      $summary[] = t('Slug separator: @delim', array('@delim' => $delimiter));
    }

    return $summary;
  }


  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    $slugifyService = \Drupal::service('unleashed.slugify');
    foreach ($items as $delta => $item) {
      $element[$delta] = ['#markup' => $slugifyService->slugify(_unleashed_rot13($item->value), $this->getSetting('delimiter'))];
    }

    return $element;
  }

}

/*
 * Helper function to convert text to Rot-13
 * https://stackoverflow.com/questions/10453212/implement-rot13-with-php
 */
function _unleashed_rot13($string): string {
  // split into array of ASCII values
  $string = array_map('ord', str_split($string));

  foreach ($string as $index => $char) {
    if (ctype_lower($char)) {
      // for lowercase subtract 97 to get character pos in alphabet
      $dec = ord('a');
    } elseif (ctype_upper($char)) {
      // for uppercase subtract 65 to get character pos in alphabet
      $dec = ord('A');
    } else {
      // preserve non-alphabetic chars
      $string[$index] = $char;
      continue;
    }
    // add 13 (mod 26) to the character
    $string[$index] = (($char - $dec + 13) % 26) + $dec;
  }

  // convert back to characters and glue back together
  return implode(array_map('chr', $string));
}
