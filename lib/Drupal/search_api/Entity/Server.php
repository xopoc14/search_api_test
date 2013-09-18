<?php
/**
 * @file
 * Contains \Drupal\search_api\Entity\Server.
 */

namespace Drupal\search_api\Entity;

/*
 * Include required classes and interfaces.
 */
use Drupal;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\Annotation\EntityType;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\search_api\Server\ServerInterface;
use Drupal\search_api\Exception\SearchApiException;

/**
 * Class representing a search server.
 *
 * @EntityType(
 *   id = "search_api_server",
 *   label = @Translation("Search server"),
 *   controllers = {
 *     "storage" = "Drupal\Core\Config\Entity\ConfigStorageController",
 *     "access" = "Drupal\search_api\Controller\ServerAccessController",
 *     "form" = {
 *       "default" = "Drupal\search_api\Controller\ServerFormController",
 *       "edit" = "Drupal\search_api\Controller\ServerFormController",
 *       "delete" = "Drupal\search_api\Form\ServerDeleteConfirmForm",
 *       "enable" = "Drupal\search_api\Form\ServerEnableConfirmForm",
 *       "disable" = "Drupal\search_api\Form\ServerDisableConfirmForm"
 *     },
 *     "list" = "Drupal\search_api\Controller\ServerListController"
 *   },
 *   config_prefix = "search_api.server",
 *   entity_keys = {
 *     "id" = "machine_name",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "status" = "status"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/search/search_api/servers/{search_api_server}",
 *     "edit-form" = "/admin/config/search/search_api/servers/{search_api_server}/edit",
 *   }
 * )
 */
class Server extends ConfigEntityBase implements ServerInterface {

  /**
   * The machine name of the server.
   *
   * @var string
   */
  public $machine_name;

  /**
   * The displayed name for a server.
   *
   * @var string
   */
  public $name;

  /**
   * The server UUID.
   *
   * @var string
   */
  public $uuid;

  /**
   * The displayed description for a server.
   *
   * @var string
   */
  public $description = '';

  /**
   * The ID of the service plugin.
   *
   * @var string
   */
  public $servicePluginId;

  /**
   * The service plugin configuration.
   *
   * @var array
   */
  public $servicePluginConfig = array();

  /**
   * The service plugin instance.
   *
   * @var \Drupal\search_api\Service\ServiceInterface
   */
  private $servicePluginInstance = NULL;

  /**
   * Clone a Server object.
   */
  public function __clone() {
    // Prevent the service plugin instance from being cloned.
    $this->servicePluginInstance = NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->machine_name;
  }

  /**
   * {@inheritdoc}
   */
  public function uri() {
    return array(
     'path' => 'admin/config/search/search_api/servers/' . $this->id(),
      'options' => array(
        'entity_type' => $this->entityType,
        'entity' => $this,
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function hasValidService() {
    // Get the service plugin definition.
    $service_plugin_definition = Drupal::service('search_api.service.plugin.manager')->getDefinition($this->servicePluginId);
    // Determine whether the service is valid.
    return !empty($service_plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function getService() {
    // Check if the service plugin instance needs to be resolved.
    if (!$this->servicePluginInstance && $this->hasValidService()) {
      // Get the ID of the service plugin.
      $service_plugin_id = $this->servicePluginId;
      // Get the service plugin manager.
      $service_plugin_manager = Drupal::service('search_api.service.plugin.manager');
      // Create a service plugin instance.
      $this->servicePluginInstance = $service_plugin_manager->createInstance($service_plugin_id, $this->servicePluginConfig);
      // Check if the service plugin instance failed to resolve.
      if (!$this->servicePluginInstance) {
        // Raise SearchApiException: invalid service plugin.
        throw new SearchApiException(format_string('Search server with machine name @name specifies an illegal service plugin @plugin', array('@name' => $this->id(), '@plugin' => $service_plugin_id)));
      }
    }
    return $this->servicePluginInstance;
  }

}