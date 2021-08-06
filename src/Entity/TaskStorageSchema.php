<?php

namespace Drupal\search_api\Entity;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorageSchema;

/**
 * Defines a storage schema for task entities.
 */
class TaskStorageSchema extends SqlContentEntityStorageSchema {

  /**
   * {@inheritdoc}
   */
  protected function getEntitySchema(ContentEntityTypeInterface $entity_type, $reset = FALSE): array {
    $schema = parent::getEntitySchema($entity_type, $reset);

    if ($data_table = $this->storage->getBaseTable()) {
      $schema[$data_table]['unique keys'] += [
        'task__unique' => ['type', 'server_id', 'index_id', 'data'],
      ];
    }

    return $schema;
  }

}
