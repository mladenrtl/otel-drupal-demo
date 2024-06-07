<?php

namespace Drupal\otel_demo\Logger\Formatter;

use Monolog\Formatter\LineFormatter;
use function var_export;

class DrushFormatter extends LineFormatter {

  /**
   * {@inheritdoc}
   */
  protected function convertToString($data): string {
    if ($data === NULL || is_bool($data)) {
      return var_export($data, TRUE);
    }

    if (is_scalar($data)) {
      return (string) $data;
    }

    $result = "";
    array_walk($data, static function ($val, $key) use (&$result) {
      if ($val !== "") {
        if (is_array($val)) {
          $val = var_export($val, TRUE);
        }
        $result .= " | $key=$val";
      }
    });

    return ltrim($result);
  }

}
