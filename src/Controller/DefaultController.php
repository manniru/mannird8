<?php

namespace Drupal\mannird8\Controller;

require 'vendor/setasign/fpdf/fpdf.php';


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

// require '/Users/mannir/Sites/d8/vendor/phpoffice/phpspreadsheet/IOFactory.php';
// require '/Users/mannir/Sites/d8/vendor/phpoffice/phpspreadsheet/Spreadsheet.php';
// require '/Users/mannir/Sites/d8/vendor/phpoffice/phpspreadsheet/Writer/Xlsx.php';

// use PhpOffice\PhpSpreadsheet\IOFactory;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    // $pdf = new \FPDF('P','mm',array(210,297));
    $pdf = new \FPDF('L','mm','A5');

    $pdf->AddPage();

    $pdf->Image('modules/mannir/mannird8/assets/bg.png',0,0, 210, 135);


    $pdf->SetFont('Arial','B',16);
    $pdf->Image('http://buk.edu.ng/sites/all/themes/responsive_green/logo.png',30,20, 100);

    // $pdf->Cell(0,10,'BAYERO UNIVERSITY KANO', 0, 0, 'C');
    $pdf->Ln(20);
    $pdf->SetFont('Arial','B',12);

    $pdf->Cell(0,15,'ONLINE REGISTRATION FORM', 0, 0, 'C');
    
    $pdf->Ln();
    $pdf->Image('modules/mannir/mannird8/assets/mannir.jpg',70,35, 20, 20);


    $pdf->Ln();
    $w = 50;
    $w2 = 80;
    $pdf->Cell($w,10,'Registration Number:', 1, 0);
    $pdf->Cell($w2,10,$student->id, 1, 0);
    $pdf->Ln();
    $pdf->Cell($w,10,'Student Name', 1, 0);
    $pdf->Cell($w2,10,$student->name, 1, 0);
    $pdf->Ln();
    $pdf->Cell($w,10,'Student Age', 1, 0);
    $pdf->Cell($w2,10,$student->age, 1, 0);
    $pdf->Ln();
    $pdf->Cell($w,10,'Date Register', 1, 0);
    $pdf->Cell($w2,10, $student->datetime, 1, 0);
    $pdf->Ln();
    $pdf->Cell($w,10,'Print Date', 1, 0);
    $pdf->Cell($w2,10, date('d/m/Y h:m:i'), 1, 0);
    $pdf->Ln();
    $pdf->Cell($w,10,'Millisecon', 1, 0);
    $pdf->Cell($w2,10, round(microtime(true) * 1000), 1, 0);



    $pdf->Output();
    exit();

    return [
      '#type' => 'markup',
      '#markup' => "RegPDF Welcome $student->name <br /> Your Age: $student->age <br /> Gender: $student->gender"
    ];
  }

  /*
  public function excel() {

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Hello World !');

    $writer = new Xlsx($spreadsheet);
    $writer->save('hello world.xlsx');
    exit();

    return ['#markup' => 'Excel here'];
  }

  public function excel2() {
    $response = new Response();
    $response->headers->set('Pragma', 'no-cache');
    $response->headers->set('Expires', '0');
    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->headers->set('Content-Disposition', 'attachment; filename=spreadsheet.xlsx');

    // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    // header('Content-Disposition: attachment; filename=spreadsheet.xlsx'); /*-- $filename is  xsl filename ---*/
    // header('Cache-Control: max-age=0');

/*
    
    // Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    
    // Set workbook properties
    $spreadsheet->getProperties()->setCreator('Rob Gravelle')
                ->setLastModifiedBy('Rob Gravelle')
                ->setTitle('A Simple Excel Spreadsheet')
                ->setSubject('PhpSpreadsheet')
                ->setDescription('A Simple Excel Spreadsheet generated using PhpSpreadsheet.')
                ->setKeywords('Microsoft office 2013 php PhpSpreadsheet')
                ->setCategory('Test file');

    //Set active sheet index to the first sheet, 
    //and add some data
    $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A1', 'This')
                ->setCellValue('B2', 'is')
                ->setCellValue('C1', 'a')
                ->setCellValue('D2', 'test.');

    // Set worksheet title
    $spreadsheet->getActiveSheet()->setTitle('Simple');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $spreadsheet->setActiveSheetIndex(0);
 
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

    $path = \Drupal::service('file_system')->realpath(file_default_scheme() . "://");
    $filename = "test1";
    $writer->save($path."/"."/".$filename.".xlsx");

    $content = file_get_contents($path.'/'.$filename.'.xlsx');

    $response = new Response();
    $response->headers->set('Content-Type', 'application/vnd.ms-excel');
    $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename.'.xlsx');
    $response->headers->set('Cache-Control', 'max-age=0');
    $response->setContent($content);
    return $response;

  }
  */



  public function idcard($id = null)
  {
    $student = \Drupal::database()->query("select * from _students where id = $id")->fetchObject();

    $pdf = new \FPDF('L','mm',array(53.98, 85.60));

    $pdf->AddPage();
    $pdf->SetMargins(2, 2, 2);

    $pdf->Image('modules/mannir/mannird8/assets/idcard.jpg',0,0, 85.60, 53.98);

    $pdf->Ln(-10);
    $pdf->SetFont('Arial','B',12);
    // $pdf->Image('http://buk.edu.ng/sites/all/themes/responsive_green/logo.png',30,20, 100);

    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0,10,'BAYERO UNIVERSITY KANO', 0, 0, 'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial','B',8);
    $pdf->Ln(7);
    $pdf->Cell(0,5,'STUDENT IDCARD', 0, 0, 'C');
  

    $pdf->Ln();
    $w = 30;
    $w2 = 30;
    $h = 3;
    $pdf->Cell($w,$h,'Registration Number:', 1, 0);
    $pdf->Cell($w2,$h,$student->id, 1, 0);
    $pdf->Ln();
    $pdf->Cell($w,$h,'Student Name', 1, 0);
    $pdf->Cell($w2,$h,$student->name, 1, 0);
    $pdf->Ln();
    $pdf->Cell($w,$h,'Student Age', 1, 0);
    $pdf->Cell($w2,$h,$student->age, 1, 0);
    $pdf->Ln();
    $pdf->Cell($w,$h,'Date Register', 1, 0);
    $pdf->Cell($w2,$h, $student->datetime, 1, 0);
    $pdf->Ln();
    $pdf->Cell($w,$h,'Print Date', 1, 0);
    $pdf->Cell($w2,$h, date('d/m/Y h:m:i'), 1, 0);
    $pdf->Ln();
    $pdf->Cell($w,$h,'Millisecon', 1, 0);
    $pdf->Cell($w2,$h, round(microtime(true) * 1000), 1, 0);

    $pdf->Image('modules/mannir/mannird8/assets/audu.jpg',70,35, 20, 20);

    $pdf->SetTextColor(0, 0, 0);
    // $pdf->Ln(-60);

  $pdf->MultiCell(50, 80, "SAMPLE TEXT");
    $pdf->Output();
    exit();

    return [
      '#type' => 'markup',
      '#markup' => "RegPDF Welcome $student->name <br /> Your Age: $student->age <br /> Gender: $student->gender"
    ];
  }
}
