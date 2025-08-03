<?php

declare(strict_types=1);

namespace Drupal\ai_eeat\Plugin\Field\FieldWidget;

use Drupal\ai_eeat\AiEeatService;
use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
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
    private readonly AiEeatService $aiEeatService,
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
      $container->get('ai_eeat.service'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    $setting = ['target_field' => 'body'];
    return $setting + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    // @todo Make this a drop-down of text fields on this entity.
    $element['target_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Target field'),
      '#description' => $this->t('The text field containing the main content of this entity'),
      '#default_value' => $this->getSetting('target_field'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    return [
      $this->t('Target field: @target_field', ['@target_field' => $this->getSetting('target_field')]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $value = $items[$delta]->value ?? '';
    $is_assessed = !empty($value);

    $element['eeat_evaluation'] = [
      '#type' => 'details',
      '#title' => $this->t('E-E-A-T Evaluation'),
      '#open' => TRUE,
      '#prefix' => '<div id="eeat-evaluation-wrapper">',
      '#suffix' => '</div>',
    ];

    $element['eeat_evaluation']['display'] = [
      '#markup' => $value,
      '#access' => $is_assessed,
      '#prefix' => '<div>',
      '#suffix' => '</div>',
    ];

    $element['eeat_evaluation']['assess_button'] = [
      '#type' => 'button',
      '#value' => !$is_assessed ? $this->t('Assess') : $this->t('Re-assess'),
      '#ajax' => [
        'callback' => [$this, 'ajaxAssessButton'],
        'event' => 'click',
        'wrapper' => 'eeat-evaluation-wrapper',
      ],
    ];

    $element['eeat_evaluation']['value'] = [
      '#type' => 'hidden',
      '#default_value' => $value,
    ];

    return $element;
  }

  public function ajaxAssessButton(array &$form, FormStateInterface $form_state) {
    $body_text = $form_state->getValue('body')[0]['value'];
    $evaluation = $this->aiEeatService->evaluate($body_text);
    $field_name = $this->fieldDefinition->getName();
    $element = &$form[$field_name]['widget'][0]['eeat_evaluation'];
    $element['display']['#markup'] = $evaluation;
    $element['display']['#access'] = TRUE;
    $element['value']['#value'] = $evaluation;
    $element['assess_button']['#value'] = $this->t('Re-assess');
    return $element;
  }

  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as &$value) {
      $value['value'] = $value['eeat_evaluation']['value'];
    }
    return $values;
  }

}
