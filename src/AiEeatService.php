<?php

declare(strict_types=1);

namespace Drupal\ai_eeat;

use Drupal\ai_helper\AiInterface;

/**
 * Provides functions for EEAT evaluator.
 */
final class AiEeatService {

  /**
   * Constructs an AiEeatService object.
   */
  public function __construct(
    private readonly AiInterface $aiChat,
  ) {}

  /**
   * Obtain the evaluation from the AI provider.
   */
  public function evaluate($text) {
    $message_texts = [];
    $message_texts[] = 'Only provide a single numeric value out of 100';
    $message_texts[] = 'Use these recommendations:';
    $message_texts[] = 'Does the content provide original information, reporting, research, or analysis?';
    $message_texts[] = 'Does the content provide a substantial, complete, or comprehensive description of the topic?';
    $message_texts[] = 'Does the content provide insightful analysis or interesting information that is beyond the obvious?';
    $message_texts[] = 'If the content draws on other sources, does it avoid simply copying or rewriting those sources, and instead provide substantial additional value and originality?';
    $message_texts[] = 'Does the main heading or page title provide a descriptive, helpful summary of the content?';
    $message_texts[] = 'Does the main heading or page title avoid exaggerating or being shocking in nature?';
    $message_texts[] = 'Is this the sort of page you\'d want to bookmark, share with a friend, or recommend?';
    $message_texts[] = 'Would you expect to see this content in or referenced by a printed magazine, encyclopedia, or book?';
    $message_texts[] = 'Does the content provide substantial value when compared to other pages in search results?';
    $message_texts[] = 'Does the content have any spelling or stylistic issues?';
    $message_texts[] = 'Is the content produced well, or does it appear sloppy or hastily produced?';
    $message_texts[] = 'Is the content mass-produced by or outsourced to a large number of creators, or spread across a large network of sites, so that individual pages or sites don\'t get as much attention or care?';
    $message_texts[] = 'Evaluate the following text:';
    $message_texts[] = $text;

    return $this->aiChat->makeAiRequest($message_texts);
  }

}
