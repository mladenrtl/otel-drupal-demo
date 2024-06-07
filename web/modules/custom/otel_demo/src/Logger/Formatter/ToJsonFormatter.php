<?php

namespace Drupal\rtlbase\Logger\Formatter;

use Monolog\Formatter\NormalizerFormatter;

class ToJsonFormatter extends NormalizerFormatter {

  /**
   * {@inheritDoc}
   */
  public function format($record) {
    return $this->toJson($record);
  }

}
