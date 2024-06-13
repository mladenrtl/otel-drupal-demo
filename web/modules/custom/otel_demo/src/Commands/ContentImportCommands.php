<?php

namespace Drupal\otel_demo\Commands;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drush\Commands\DrushCommands;

class ContentImportCommands extends DrushCommands
{

  use StringTranslationTrait;

  /**
   * Import content.
   *
   * @command otel-demo:import-content
   * @aliases odi
   */
  public function importContent(): void
  {
    $this->output()->writeln('Importing content...');

    /** @var FileSystemInterface $fileSystem */
    $fileSystem = \Drupal::service('file_system');

    $uri = $fileSystem->copy(DRUPAL_ROOT . '/content-import/opentelemetry_demo_blog_posts.csv', 'public://opentelemetry_demo_blog_posts.csv', FileSystemInterface::EXISTS_REPLACE);
    $file = \Drupal::entityTypeManager()->getStorage('file')->create([
      'uri' => $uri,
    ]);
    $file->setPermanent();
    $file->save();


    $batch = [
      'title' => $this->t('Importing Content'),
      'operations' => [
        [
          'import_node', [$file, 'article', 1],
        ],
      ],
      'init_message' => $this->t('Importing...please wait'),
      'progress_message' => $this->t('Processed @current out of @total.'),
      'finished' => 'import_success',
    ];
    batch_set($batch);
    drush_backend_batch_process();
  }

}
