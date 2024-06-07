<?php

namespace Drupal\otel_demo\ResourceWriter;

use InvalidArgumentException;

class StreamWriterFactory {

  /**
   * @param string $stream
   *
   * @return StreamWriterInterface|null
   */
  public function get(string $stream): ?StreamWriterInterface {
    try {
      return new StreamWriter($stream);
    }
    catch (InvalidArgumentException $e) {
      return NULL;
    }
  }

}
