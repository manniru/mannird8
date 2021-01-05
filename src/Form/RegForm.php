<?php

namespace Drupal\mannird8\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class RegForm extends FormBase {

  public function getFormId() {
    return 'mannird8_registration_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
    ];

    $form['age'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Age'),
    ];

    $form['dob'] = [
      '#type' => 'date',
      '#title' => 'Date of Birth',
      '#default_value' => '1990-01-01',
    ];

    $validators = [
      'file_validate_extensions' => array('jpg'),
      'file_validate_size' => [500000],
    ];

    $path = 'http://mannir.net/mannir.jpg';

    $form['f1']['photo1'] = [ '#markup' => "<img src='$path' width='100' height='100' alt='Photo'/>" ];

      $form['f1']['photo'] = [
        '#type' => 'managed_file',
        '#name' => 'photo',
        '#title' => t('Passport Photo'),
        '#size' => 20,
        '#description' => t('JPG format only'),
        '#upload_validators' => $validators,
        '#upload_location' => 'public://photos/',
        '#default_value' => isset($reg->photo) ? [$reg->photo] : '',
        // '#default_value' => array($reg->photo),
        // '#default_value' => array($reg->photo),
        '#required' => TRUE,
        // '#default_value' => $this->get('photo'),

        // '#upload_location' => 'public://photos/',
        // '#upload_validators'=>  array('file_validate_name' => array()),
      ];


    $form['gender'] = [
      '#type' => 'select',
      '#title' => 'Gender',
      '#options' => ['-Select-' => '', 'Male' => 'Male', 'Female' => 'Female'],
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
    $name = $form_state->getValue('name');
    $age = $form_state->getValue('age');
    $gender = $form_state->getValue('gender');
    $dob = 2020 - $age;
    $this->messenger()->addMessage("Welcome $name, Your Age is $age, Year of Birth $dob");

    $fields = [
      'name' => $name,
      'age' => $age,
      'gender' => $gender,
    ];
    $query = \Drupal::database()->insert('_students');
    $query->fields($fields);
    $query->execute();


  }

}
