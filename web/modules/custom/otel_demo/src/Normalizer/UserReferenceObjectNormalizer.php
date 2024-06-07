<?php

namespace Drupal\otel_demo\Normalizer;

use Drupal\jsonapi\Normalizer\ResourceObjectNormalizer;

class UserReferenceObjectNormalizer extends ResourceObjectNormalizer {
  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []): float|int|bool|\ArrayObject|array|string|null {
    $data = parent::normalize($object, $format, $context);
    // transform your data here
    // You'll likely need to run some checks on the $entity or $data
    // variables and include conditionals so that only the items
    // you are interested in are altered
    return $data;
  }

}
