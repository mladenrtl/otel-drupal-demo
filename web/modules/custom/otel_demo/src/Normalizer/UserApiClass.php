<?php

namespace Drupal\otel_demo\Normalizer;

use Drupal\serialization\Normalizer\TypedDataNormalizer;

class UserApiClass extends TypedDataNormalizer {
  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []): float|int|bool|\ArrayObject|array|string|null
  {
    $data = parent::normalize($object, $format, $context);
//    dd($data);
    // transform your data here
    // You'll likely need to run some checks on the $entity or $data
    // variables and include conditionals so that only the items
    // you are interested in are altered
    return $data;
  }
}
