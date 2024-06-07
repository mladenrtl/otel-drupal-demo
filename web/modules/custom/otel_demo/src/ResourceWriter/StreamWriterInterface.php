<?php

namespace Drupal\otel_demo\ResourceWriter;

interface StreamWriterInterface {

  /**
   * @param string $log
   */
  public function write(string $log);

}
