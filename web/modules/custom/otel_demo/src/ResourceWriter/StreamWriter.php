<?php

namespace Drupal\otel_demo\ResourceWriter;

use InvalidArgumentException;
use function fclose;
use function fopen;
use function fwrite;
use function strpos;

class StreamWriter implements StreamWriterInterface {

  /**
   * @var resource|null
   */
  private $stream;

  /**
   * @param $stream
   */
  public function __construct($stream) {
    if (is_resource($stream) === TRUE) {
      $this->stream = $stream;
    }
    elseif (is_string($stream) === TRUE) {
      $this->stream = $this->createStreamResource($stream);
    }
    else {
      throw new InvalidArgumentException('A stream must either be a resource or a string.');
    }
  }

  /**
   * @param string $log
   */
  public function write(string $log): void {
    if ($this->getStream() === NULL) {
      return;
    }
    fwrite($this->getStream(), $this->guaranteeNewLine($log));
    fclose($this->getStream());
  }

  /**
   * @return resource|null
   */
  public function getStream() {
    return $this->stream;
  }

  /**
   * @param string $stream
   *
   * @return resource
   */
  private function createStreamResource(string $stream) {
    if (!str_starts_with($stream, 'php://')) {
      throw new InvalidArgumentException('Only PHP IO streams are supported.');
    }

    return fopen($stream, 'wb');
  }

  /**
   * @param string $message
   *
   * @return string
   */
  private function guaranteeNewLine(string $message): string {
    if (strpos($message, PHP_EOL) !== FALSE) {
      return $message;
    }

    return $message . PHP_EOL;
  }

}
