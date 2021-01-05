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

  public function regpdf($id) {
    $student = \Drupal::database()->query("select * from _students where id = $id")->fetchObject();

    $pdf = new \FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Image('http://buk.edu.ng/sites/all/themes/responsive_green/logo.png',60,10, 100);

    // $pdf->Cell(0,10,'BAYERO UNIVERSITY KANO', 0, 0, 'C');
    $pdf->Ln(40);
    $pdf->SetFont('Arial','B',12);

    $pdf->Cell(0,10,'ONLINE REGISTRATION FORM', 0, 0, 'C');
    
    $pdf->Ln();
    $pdf->Image('modules/custom/mannir/mannird8/mannir.jpg',90,25, 20, 20);

    $pdf->Ln();
    $pdf->Cell(100,10,'Registration Number:', 1, 0);
    $pdf->Cell(90,10,$student->id, 1, 0);
    $pdf->Ln();
    $pdf->Cell(100,10,'Student Name', 1, 0);
    $pdf->Cell(90,10,$student->name, 1, 0);
    $pdf->Ln();
    $pdf->Cell(100,10,'Student Age', 1, 0);
    $pdf->Cell(90,10,$student->age, 1, 0);

    $pdf->Output();
    exit();

    return [
      '#type' => 'markup',
      '#markup' => "RegPDF Welcome $student->name <br /> Your Age: $student->age <br /> Gender: $student->gender"
    ];
  }
}
