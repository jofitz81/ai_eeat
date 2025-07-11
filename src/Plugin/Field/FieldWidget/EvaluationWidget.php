<?php

declare(strict_types=1);

namespace Drupal\ai_eeat\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ai_image_alt_text\ProviderHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the 'ai_eeat_evaluation_widget' field widget.
 */
#[FieldWidget(
  id: 'ai_eeat_evaluation_widget',
  label: new TranslatableMarkup('EEAT evaluation'),
  field_types: ['ai_eeat_evaluation'],
)]
final class EvaluationWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the plugin instance.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $element['eeat_evaluation'] = [
      '#type' => 'details',
      '#title' => $this->t('E-E-A-T Evaluation'),
      '#open' => TRUE,
      '#prefix' => '<div id="eeat-evaluation-wrapper">',
      '#suffix' => '</div>',
    ];

    $element['eeat_evaluation']['assess_button'] = [
      '#type' => 'button',
      '#value' => $this->t('Assess'),
      '#ajax' => [
        'callback' => [$this, 'ajaxAssessButton'],
        'event' => 'click',
        'wrapper' => 'eeat-evaluation-wrapper',
      ],
    ];

    $element['eeat_evaluation']['value'] = [
      '#type' => 'textfield',
      '#default_value' => $items[$delta]->value ?? NULL,
    ];

    return $element;
  }

  public function ajaxAssessButton(array &$form, FormStateInterface $form_state) {
    $body_text = $form_state->getValue('body')[0]['value'];
    $evaluation = 49;
    $field_name = $this->fieldDefinition->getName();
    $element = &$form[$field_name]['widget'][0]['eeat_evaluation'];
    $element['value']['#value'] = $evaluation;
    return $element;
  }

  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as &$value) {
      $value['value'] = $value['eeat_evaluation']['value'];
    }
    return $values;
  }

}
