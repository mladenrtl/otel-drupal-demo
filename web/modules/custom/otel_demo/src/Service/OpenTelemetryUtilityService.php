<?php

namespace Drupal\otel_demo\Service;

use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\API\Trace\SpanInterface;
use OpenTelemetry\Context\Context;

class OpenTelemetryUtilityService {
  public static function getCurrentOtelSpan(): ?SpanInterface {
    $scope = Context::storage()->scope();
    if ($scope === NULL) {
      return NULL;
    }
    return Span::fromContext($scope->context());
  }

}
