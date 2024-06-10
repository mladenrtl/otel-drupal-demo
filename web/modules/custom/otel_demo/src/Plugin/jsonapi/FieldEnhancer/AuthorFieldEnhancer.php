<?php

namespace Drupal\otel_demo\Plugin\jsonapi\FieldEnhancer;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\TypedData\Exception\MissingDataException;
use Drupal\jsonapi\JsonApiResource\ResourceObject;
use Drupal\jsonapi_extras\Plugin\ResourceFieldEnhancerBase;
use Drupal\user\UserInterface;
use GuzzleHttp\Exception\GuzzleException;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\API\Trace\SpanInterface;
use Shaper\Util\Context;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Perform additional manipulations to JSON fields.
 *
 * @ResourceFieldEnhancer(
 *   id = "author",
 *   label = @Translation("Author Field"),
 *   description = @Translation("Render Author Field from User API")
 * )
 */
class AuthorFieldEnhancer extends ResourceFieldEnhancerBase implements ContainerFactoryPluginInterface {

  /**
   * The serialization json.
   *
   * @var Drupal\Component\serialization\Json
   */
  protected $encoder;

  /**
   * Constructs a new JSONFieldEnhancer.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Component\Serialization\Json $encoder
   *   The serialization json.
   */
  public function __construct(array $configuration, string $plugin_id, $plugin_definition, Json $encoder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->encoder = $encoder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('serialization.json'));
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

  /**
   * {@inheritdoc}
   * @throws MissingDataException
   * @throws GuzzleException
   */
  public function doUndoTransform($data, Context $context) {

    /** @var ResourceObject $resourceObject */
    $resourceObject = $context->offsetGet('resource_object');

    if ($resourceObject->getResourceType()->getTypeName() !== 'node--article') {
      return NULL;
    }

    $uidField = $resourceObject->getField('uid');
    $referencedEntity = $uidField->referencedEntities();

    if (count($referencedEntity) === 0) {
      return NULL;
    }

    /** @var UserInterface $user */
    $user = array_shift($referencedEntity);
    $email = $user->getEmail();

    $data = $this->getUserDetailsByEmail($email);

    return $this->encoder->decode($data);
  }

  /**
   * {@inheritdoc}
   */
  protected function doTransform($data, Context $context) {
    return $this->encoder->encode($data);
  }

  /**
   * {@inheritdoc}
   */
  public function getOutputJsonSchema() {
    return [
      'oneOf' => [
        ['type' => 'object'],
        ['type' => 'array'],
        ['type' => 'string'],
        ['type' => 'null'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getSettingsForm(array $resource_field_info) {
    return [];
  }

  /**
   * @throws GuzzleException
   * @throws \Exception
   */
  private function getUserDetailsByEmail(?string $email): string {
    $httpClient = \Drupal::httpClient();

    $currentSpan = $this->getCurrentOtelSpan();

    $ctx = $currentSpan->storeInContext(\OpenTelemetry\Context\Context::getCurrent());
    $carrier = [];
    TraceContextPropagator::getInstance()->inject($carrier, null, $ctx);

    $options = [];

    foreach ($carrier as $key => $value) {
      $options['headers'][$key] = $value;
    }

    \Drupal::logger('otel_demo')->info('Fetching user details for email: ' . $email);

    throw new \Exception('User API is not available');

//    $userDetails = $httpClient->get("http://users.api:8080/users/{$email}", $options);
//
//    return $userDetails->getBody()->getContents();
  }

  private static function getCurrentOtelSpan(): ?SpanInterface {
    $scope = \OpenTelemetry\Context\Context::storage()->scope();
    if ($scope === NULL) {
      return NULL;
    }
    $span = Span::fromContext($scope->context());
    return $span;
  }

}
