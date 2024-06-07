<?php

namespace Drupal\otel_demo\Logger;

use DateTimeZone;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Logger\RfcLoggerTrait;
use Drupal\Core\Logger\RfcLogLevel;
use Drupal\otel_demo\Logger\Formatter\DrushFormatter;
use Drupal\otel_demo\ResourceWriter\StreamWriterFactory;
use Drupal\otel_demo\Service\BatchService;
use Drupal\otel_demo\Service\OpenTelemetryUtilityService;
use Elastic\Monolog\Formatter\ElasticCommonSchemaFormatter;
use Monolog\DateTimeImmutable;
use Monolog\Level;
use Monolog\LogRecord;
use Psr\Log\LoggerInterface;
use function array_slice;
use function date_default_timezone_get;
use function is_array;
use function is_object;
use function str_replace;

class Stdout implements LoggerInterface {

  use RfcLoggerTrait;

  private const SEVERITY_LEVELS = [
    RfcLogLevel::EMERGENCY => 'Emergency',
    RfcLogLevel::ALERT => 'Alert',
    RfcLogLevel::CRITICAL => 'Critical',
    RfcLogLevel::ERROR => 'Error',
    RfcLogLevel::WARNING => 'Warning',
    RfcLogLevel::NOTICE => 'Notice',
    RfcLogLevel::INFO => 'Info',
    RfcLogLevel::DEBUG => 'Debug',
  ];

  private const SEVERITY_LEVELS_NUMBER = [
    RfcLogLevel::EMERGENCY => 80,
    RfcLogLevel::ALERT => 70,
    RfcLogLevel::CRITICAL => 60,
    RfcLogLevel::ERROR => 50,
    RfcLogLevel::WARNING => 40,
    RfcLogLevel::NOTICE => 30,
    RfcLogLevel::INFO => 20,
    RfcLogLevel::DEBUG => 10,
  ];

  private const MONOLOG_SEVERITY_LEVELS_NUMBER = [
    80 => 600,
    70 => 550,
    60 => 500,
    50 => 400,
    40 => 300,
    30 => 250,
    20 => 200,
    10 => 100,
  ];


  private BatchService $batchService;

  private StreamWriterFactory $streamWriterFactory;

  protected DateTimeZone $timezone;

  private array $context;

  public function __construct(
    BatchService $batchService,
    StreamWriterFactory $streamWriterFactory
  ) {
    $this->batchService = $batchService;
    $this->streamWriterFactory = $streamWriterFactory;
    $this->timezone = new DateTimeZone(date_default_timezone_get() ?: 'UTC');
  }

  /**
   * {@inheritdoc}
   * @throws \Exception
   */
  public function log($level, $message, array $context = []): void {
    $stream = 'php://stdout';
    if ($level <= RfcLogLevel::WARNING) {
      $stream = 'php://stderr';
    }

    $context['level'] = self::SEVERITY_LEVELS_NUMBER[$level];

    $writer = $this->streamWriterFactory->get($stream);
    if ($writer === NULL) {
      return;
    }

    $severity = self::SEVERITY_LEVELS[$level];

    $this->setContext($context);

    $this->trimBacktrace();

    $span = OpenTelemetryUtilityService::getCurrentOtelSpan();
    $message = $this->formatMessage($message, $context);
    $record = [
      'message' => $this->removeNewLines($message),
      'context' => $this->getContext(),
      'level' => self::SEVERITY_LEVELS_NUMBER[$level],
      'level_name' => $severity,
      'channel' => 'MSP Drupal logger',
      'datetime' => new DateTimeImmutable(TRUE, $this->timezone),
      'extra' => [
        'TraceId' => $span?->getContext()->getTraceId(),
        'SpanId' => $span?->getContext()->getSpanId(),
        '@mt' => $this->removeNewLines($message),
        '@l' => $severity,
      ],
    ];

    $log = $this->getFormatted($record);

    $writer->write($log);
  }

  /**
   * @param string $message
   *
   * @return string
   */
  private function removeNewLines(string $message): string {
    return str_replace(["\r\n", "\r", "\n"], ' ', $message);
  }

  /**
   * @param $record
   *
   * @return string
   */
  private function getFormatted($record): string {
    $formatter = new ElasticCommonSchemaFormatter();

    if (!$record instanceof LogRecord) {
      $record = self::createLogRecord($record);
    }
    if ($record instanceof LogRecord && $this->isBatchActive()) {
      $formatter = new DrushFormatter();
    }

    return trim($this->removeNewLines($formatter->format($record)));
  }

  private function formatMessage($message, array $context = []): string {
    return (string) new FormattableMarkup($message, $context);
  }

  public static function createLogRecord(array $record, array $context = []): LogRecord {
    return new LogRecord(
      $record['datetime'],
      $record['channel'],
      Level::fromValue(self::MONOLOG_SEVERITY_LEVELS_NUMBER[$record['level']] ?? Level::Warning),
      $record['message'],
      $context,
      $record['extra']
    );
  }

  /**
   * Strip backtrace string.
   *
   * Slice backtrace to max of 8 items deep.
   *
   * Strip everything in backtrace args that is object or array.
   *
   * @return void
   */
  private function trimBacktrace(): void {
    $context = $this->getContext();

    unset($context['@backtrace_string']);

    if (isset($context['backtrace']) === FALSE) {
      return;
    }

    $context['backtrace'] = array_slice($context['backtrace'], 0, 6);

    foreach ($context['backtrace'] as $traceIndex => $traceItem) {
      if (isset($traceItem['args']) === FALSE) {
        continue;
      }

      foreach ($traceItem['args'] as $argIndex => $arg) {
        if (is_object($arg) || is_array($arg)) {
          unset($context['backtrace'][$traceIndex]['args'][$argIndex]);
        }
      }
    }

    $this->setContext($context);
  }

  /**
   * @return array
   */
  public function getContext(): array {
    return $this->context;
  }

  /**
   * @param array $context
   */
  public function setContext(array $context): void {
    $this->context = $context;
  }

  /**
   * @return bool
   */
  private function isBatchActive(): bool {
    $batch = $this->batchService->get();
    return isset($batch['running']) && $batch['running'] === TRUE;
  }

}
