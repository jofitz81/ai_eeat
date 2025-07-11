<?php

declare(strict_types=1);

namespace Drupal\ai_eeat\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'EEAT evaluation' formatter.
 */
#[FieldFormatter(
  id: 'ai_eeat_evaluation_formatter',
  label: new TranslatableMarkup('EEAT evaluation'),
  field_types: ['ai_eeat_evaluation'],
)]
final class EvaluationFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];
    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#markup' => $item->value,
      ];
    }
    return $element;
  }

}
