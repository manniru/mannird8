<?php

namespace Drupal\mannird8\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

class RegListForm extends FormBase {

  public function getFormId() {
    return 'reglist_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $keyword = $_SESSION['keyword'];




    $form['container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'accommodation',
        ],
      ]
    ];

    $form['container']['keyword'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search Word'),
      '#size' => 30,
    ];
    $form['container']['actions']['search'] = ['#type' => 'submit', '#value' => $this->t('Search'),];
    $form['container']['actions']['reset'] = ['#type' => 'submit', '#value' => $this->t('Reset'),];


    $header = ['photo', 'id', 'name', 'age', 'gender', 'view', 'print','edit', 'delete'];

    $query = \Drupal::database()->select('_students', 'tb');
    $query->fields('tb');
    if ($keyword) {
      $query->condition('tb.name', '%'.$keyword.'%', 'LIKE');
    }
    $pager = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(20);
    $results = $pager->execute()->fetchAll();

    $rows = array();
    foreach ($results as $r) {
      $view = Link::fromTextAndUrl('View', new Url('mannird8.regview', ['id' => $r->id], ['attributes' => ['class' => ['button']]]));
      $print = Link::fromTextAndUrl('Print', new Url('mannird8.regpdf', ['id' => $r->id], ['attributes' => ['class' => ['button']]]));
      $edit = Link::fromTextAndUrl('Edit', new Url('mannird8.regview', ['id' => $r->id], ['attributes' => ['class' => ['button']]]));
      $delete = Link::fromTextAndUrl('Delete', new Url('mannird8.regview', ['id' => $r->id], ['attributes' => ['class' => ['button']]]));


      // PHOTO START
      if(isset($r->photo)) {

        $file = File::load($r->photo);

        if($file) {
          $file_uri = $file->getFileUri();
          $file_name = $file->getFilename();
          $image_path = file_url_transform_relative(file_create_url($file_uri));
          $photo = ['data' => [
            '#theme' => 'image_style',
            '#style_name' => 'thumbnail',
            '#uri' => $file_uri,
            // optional parameters
            '#width' => 100,
            '#height' => 100,
        ] ];

          // \Drupal::logger('mannirtrs_file')->notice(print_r($image_path, TRUE));
        }
      }

      // PHOTO END

      $rows[$r->id] = [
        'photo' => $photo,
        'id' => $r->id,
        'name' => $r->name,
        'age' => $r->age,
        'gender' => $r->gender,
        'view' => $view,
        'print' => $print,
        'edit' => $edit,
        'delete' => $delete,
      ];
    }

    $form['table'] = [
      '#type' => 'table',
      '#caption' => $this->t('Registration list'),
      '#header' => $header,
      '#rows' => $rows,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $form['actions']['export'] = ['#type' => 'submit', '#value' => $this->t('Export'),];
    $form['actions']['deleteall'] = ['#type' => 'submit', '#value' => $this->t('Delete All'),];


    if ($keyword) {
      // $this->messenger()->addMessage($keyword);
      $form['container']['keyword']['#default_value'] = $keyword;
    }

    $form['pager'] = ['#type' => 'pager'];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $title = $form_state->getValue('title');
    if (strlen($title) < 5) {
      // $form_state->setErrorByName('title', $this->t('The title must be at least 5 characters long.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $op = $form_state->getValue('op');
    $keyword = $form_state->getValue('keyword');
    $this->messenger()->addMessage($keyword);

    if ($op == 'Search') {
      $_SESSION['keyword'] = $keyword;
    }
    if ($op == 'Reset') {
      unset($_SESSION['keyword']);
    }

    if ($op == 'Delete All') {
      \Drupal::database()->truncate('_students')->execute();
    }


  }

}
