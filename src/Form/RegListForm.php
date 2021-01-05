<?php

namespace Drupal\mannird8\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

class RegListForm extends FormBase {

  public function getFormId() {
    return 'reglist_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $header = ['id', 'name', 'age', 'gender', 'view', 'print','edit', 'delete'];

    $query = \Drupal::database()->select('_students', 'tb');
    $query->fields('tb');
    $pager = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(10);
    $results = $pager->execute()->fetchAll();

    $rows = array();
    foreach ($results as $r) {
      $view = Link::fromTextAndUrl('View', new Url('mannird8.regview', ['id' => $r->id], ['attributes' => ['class' => ['button']]]));
      $print = Link::fromTextAndUrl('Print', new Url('mannird8.regpdf', ['id' => $r->id], ['attributes' => ['class' => ['button']]]));
      $edit = Link::fromTextAndUrl('Edit', new Url('mannird8.regview', ['id' => $r->id], ['attributes' => ['class' => ['button']]]));
      $delete = Link::fromTextAndUrl('Delete', new Url('mannird8.regview', ['id' => $r->id], ['attributes' => ['class' => ['button']]]));

      $rows[$r->id] = [
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

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $title = $form_state->getValue('title');
    if (strlen($title) < 5) {
      // $form_state->setErrorByName('title', $this->t('The title must be at least 5 characters long.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    //
  }

}
