<?php

namespace Drupal\mannird8\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Driver\mysql\Connection;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Entity\Element\EntityAutocomplete;

use Drupal\Component\Utility\Tags;
use Drupal\Component\Utility\Unicode;
//use Nekman\LuhnAlgorithm\LuhnAlgorithmFactory;

use Symfony\Component\HttpFoundation\Response;

class DefaultController extends ControllerBase {

  /**
   * Drupal\Core\Database\Driver\mysql\Connection definition.
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;
  protected $nodeStorage;


  /**
   * Constructs a new DefaultController object.
   */
  public function __construct(Connection $database, EntityTypeManagerInterface $entity_type_manager) {
    $this->database = $database;
    $this->nodeStroage = $entity_type_manager->getStorage('node');

    $module_handler = \Drupal::service('module_handler');
    $path = $module_handler->getModule('mannird8')->getPath();
    $this->path = $path;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('entity_type.manager')
    );
  }

  public function regview($id) {
    $student = \Drupal::database()->query("select * from _students where id = $id")->fetchObject();
    return [
      '#type' => 'markup',
      '#markup' => "Welcome $student->name <br /> Your Age: $student->age <br /> Gender: $student->gender"
    ];
  }
}
