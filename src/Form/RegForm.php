<?php

namespace Drupal\mannird8\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class RegForm extends FormBase {

  public function getFormId() {
    return 'mannird8_registration_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $faker = \Faker\Factory::create();

    $this->messenger()->addMessage($faker->name);

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => true,
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
      'file_validate_extensions' => array('jpg', 'png', 'gif'),
      'file_validate_size' => [1000000],
    ];

    $path = 'http://mannir.net/mannir.jpg';

    // $form['f1']['photo1'] = [ '#markup' => "<img src='$path' width='100' height='100' alt='Photo'/>" ];

      $form['photo'] = [
        '#type' => 'managed_file',
        '#name' => 'photo',
        '#title' => t('Passport Photo'),
        '#size' => 20,
        '#description' => t('JPG format only'),
        '#upload_validators' => $validators,
        '#upload_location' => 'public://photos/',
        // '#default_value' => isset($reg->photo) ? [$reg->photo] : '',
        // '#default_value' => array($reg->photo),
        // '#default_value' => array($reg->photo),
        // '#required' => TRUE,
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

    $form['actions']['demo'] = [
      '#type' => 'submit',
      '#value' => $this->t('Demo'),
    ];

    $form['actions']['generate'] = [
      '#type' => 'submit',
      '#value' => $this->t('Generate'),
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
    $faker = \Faker\Factory::create();

    $op = $form_state->getValue('op');

    if ($op == 'Submit') {
      $name = $form_state->getValue('name');
      $age = $form_state->getValue('age');
      $gender = $form_state->getValue('gender');
      $dob = 2020 - $age;
      $this->messenger()->addMessage("Welcome $name, Your Age is $age, Year of Birth $dob");

      // SRTART PHOTO
      $fid = $form_state->getValue(['photo', 0]);
      $fields['photo'] = $fid;

      $photo = $fid;

      if(isset($fid)) {
        $fields['photo'] = $fid;

        // if (!$form_state->getErrors() && !empty($fid)) {
          $rrr = $uid;
          try {
            $file = File::load($fid);
            $file->setFilename($rrr);
            $file->save();

            // $host = \Drupal::request()->getHost();
            // \Drupal::logger('mannirigr_photo')->notice('<pre>' . print_r($host, TRUE) . '</pre>');

            try {
              $storage = new StorageClient(['projectId' => 'kanoecommerce']);
              $bucket = $storage->bucket('kanoecommerce.appspot.com');
              $image_path = file_url_transform_relative(file_create_url($file->getFileUri()));
              $image_path = ltrim($image_path, '/');
              $file2 = fopen($image_path, 'r');
              $object = $bucket->upload($file2, ['name' => "$rrr.jpg", 'predefinedAcl' => 'publicRead']);

            } catch (\Throwable $e) {
              \Drupal::logger('mannirigr_error')->notice('<pre>' . print_r($e, TRUE) . '</pre>');
            }


            // $file = File::load($fid);
            // exit($fid);
            $new_filename = $rrr.".jpg";
            // exit($new_filename);

            if (isset($new_filename)) {
              $stream_wrapper = \Drupal::service('file_system')->uriScheme($file->getFileUri());
              $new_filename_uri = "{$stream_wrapper}://photos/{$new_filename}";
              file_move($file, $new_filename_uri);
            }

            // exit('Moded');



          }
          catch (\Throwable $e) {
            // watchdog_exception('mannirtrs_photo', $e);
            // \Drupal::logger('mannirtrs_photo')->notice('<pre>' . print_r($e, TRUE) . '</pre>');

          }
        }

        // END PHOTO

    // print('<pre>' . print_r($fields, TRUE) . '</pre>'); exit();


      $fields = [
        'name' => $name,
        'age' => $age,
        'gender' => $gender,
        'photo' => $photo,
      ];
      $query = \Drupal::database()->insert('_students');
      $query->fields($fields);
      $query->execute();

    }

    if ($op == 'Demo') {

      $student = [
        'name' => $faker->name,
        'age' => $faker->numberBetween($min = 18, $max=30),
        'gender' => $faker->randomElement(['Male', 'Female']),
      ];

      $query = \Drupal::database()->insert('_students');
      $query->fields($student);
      $query->execute();

      //print('<pre>' . print_r($student, TRUE) . '</pre>'); exit();


      $_SESSION['student'] = (object) $student;


      \Drupal::messenger()->addMessage($student, TRUE);

    }

    if ($op == 'Generate') {
      $values = [];

      // $random_names = ['Auwal', 'Sani', 'Salisu', 'Rabiu', 'Khamisu', 'Sadisu', 'Sabiu', 'Tasiu'];

      for ($i=1; $i <= 1000; $i++) {
        $values[] = [
          'name' => $faker->name,
          'age' => $faker->numberBetween($min = 18, $max=30),
          'gender' => $faker->randomElement(['Male', 'Female']),
        ];
      }

      //  print('<pre>' . print_r($values, TRUE) . '</pre>'); exit();

    // $this->database->truncate('_employee')->execute();

    $query = \Drupal::database()->insert('_students')->fields(['name', 'age', 'gender']);
    foreach ($values as $record) {
        $query->values($record);
    }


    $query->execute();

      //print('<pre>' . print_r($student, TRUE) . '</pre>'); exit();


      $_SESSION['student'] = (object) $student;


      \Drupal::messenger()->addMessage($student, TRUE);

    }



  }

}
