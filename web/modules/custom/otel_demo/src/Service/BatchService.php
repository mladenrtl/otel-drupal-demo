<?php

namespace Drupal\otel_demo\Service;

use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BatchService {

  /**
   * @return array
   *
   * @see batch_get()
   */
  public function &get(): ?array {
    return batch_get();
  }

  /**
   * @param array $definition
   *
   * @see batch_set()
   */
  public function set(array $definition): void {
    batch_set($definition);
  }

  /**
   * @param \Drupal\Core\Url|string $redirect
   * @param \Drupal\Core\Url $url
   * @param $callback
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse|null
   *
   * @see batch_process()
   */
  public function process($redirect = NULL, Url $url = NULL, $callback = NULL): ?RedirectResponse {
    return batch_process($redirect, $url, $callback);
  }

}
