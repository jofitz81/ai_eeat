<?php

declare(strict_types=1);

namespace Drupal\ai_eeat\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure AI EEAT settings for this site.
 */
final class SettingsForm extends ConfigFormBase {

  const string CONFIG_NAME = 'ai_eeat.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ai_eeat_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [self::CONFIG_NAME];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['prompt'] = [
      '#type' => 'textarea',
      '#rows' => 17,
      '#title' => $this->t('EEAT generation prompt'),
      '#default_value' => $this->config(self::CONFIG_NAME)->get('prompt') ?? '',
      '#description' => $this->t('Prompt used for generating EEAT evaluations.'),
      '#required' => TRUE,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config(self::CONFIG_NAME)
      ->set('prompt', $form_state->getValue('prompt'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
