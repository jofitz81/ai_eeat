<?php

declare(strict_types=1);

namespace Drupal\ai_eeat;

use Drupal\ai_helper\AiInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Provides functions for EEAT evaluator.
 */
final class AiEeatService {

  /**
   * Constructs an AiEeatService object.
   */
  public function __construct(
    private readonly AiInterface $aiChat,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Obtain the evaluation from the AI provider.
   */
  public function evaluate($text) {
    $message_texts = [];
    $message_texts[] = $this->configFactory->get('ai_eeat.settings')->get('prompt');
    $message_texts[] = $text;

    return $this->aiChat->makeAiRequest($message_texts);
  }

}
