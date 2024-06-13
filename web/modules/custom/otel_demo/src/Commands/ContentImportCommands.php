<?php

namespace Drupal\otel_demo\Commands;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\file\FileInterface;
use Drush\Commands\DrushCommands;

class ContentImportCommands extends DrushCommands
{

  use StringTranslationTrait;

  /**
   * Import content.
   *
   * @param string $filePath
   * @param string $contentType
   * @param string $importType
   *
   * @throws InvalidPluginDefinitionException
   * @throws PluginNotFoundException
   * @throws EntityStorageException
   *
   * @command otel-demo:import-content
   * @aliases odi
   *
   * @usage otel-demo:import-content content_import.csv article 1
   * @usage odi content_import.csv article 1
   */
  public function importContent(string $filePath, string $contentType, string $importType): void {
    $this->output()->writeln('Importing content...');
    $file = $this->getFile($filePath);

    $batch = [
      'title' => $this->t('Importing Content'),
      'operations' => [
        [
          'import_node', [$file, $contentType, $importType],
        ],
      ],
      'init_message' => $this->t('Importing...please wait'),
      'progress_message' => $this->t('Processed @current out of @total.'),
      'finished' => 'import_success',
    ];
    batch_set($batch);
    drush_backend_batch_process();
  }

  /**
   * @param string $filePath
   * @return EntityInterface|FileInterface
   * @throws EntityStorageException
   * @throws InvalidPluginDefinitionException
   * @throws PluginNotFoundException
   */
  public function getFile(string $filePath): EntityInterface|FileInterface {
    /** @var FileSystemInterface $fileSystem */
    $fileSystem = \Drupal::service('file_system');

    $uri = $fileSystem->copy(
      DRUPAL_ROOT . '/' . $filePath,
      'public://content_import.csv',
      FileSystemInterface::EXISTS_REPLACE
    );
    $file = \Drupal::entityTypeManager()->getStorage('file')->create([
      'uri' => $uri,
    ]);
    $file->setPermanent();
    $file->save();

    return $file;
  }

}
