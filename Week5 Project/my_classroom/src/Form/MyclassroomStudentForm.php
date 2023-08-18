<?php

namespace Drupal\my_classroom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Messenger;
use Drupal\Core\Link;

class MyclassroomStudentForm extends FormBase{

  public function getFormId()
  {
    return 'myclassroom_student_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $conn1 = Database::getconnection();
    $record = [];
    if (isset($_GET['id'])) {
      $query = $conn1->select('student_table', 'm')->condition('sid', $_GET['id'])->fields('m');
      $record = $query->execute()->fetchAssoc();
    }
    $form['stu_name'] = ['#type' => 'textfield','#attributes'=>array('placeholder'=>t('Student Name')), '#required' => TRUE, '#default_value' => (isset($record['stu_name']) && $_GET['id']) ? $record['stu_name'] : ''];

    $form['sub_id'] = ['#type' => 'textfield','#attributes'=>array('placeholder'=>t('Subject Id')), '#required' => TRUE, '#default_value' => (isset($record['sub_id']) && $_GET['id']) ? $record['sub_id'] : '',
    ];

    $form['email'] = ['#type' => 'textfield','#attributes'=>array('placeholder'=>t('Email')), '#required' => TRUE, '#default_value' => (isset($record['email']) && $_GET['id']) ? $record['email'] : ''];

    $form['phone'] = ['#type' => 'textfield','#attributes'=>array('placeholder'=>t('Phone Number')), '#required' => TRUE, '#default_value' => (isset($record['phone']) && $_GET['id']) ? $record['phone'] : ''];

    $form['action'] = ['#type' => 'action'];

    $form['action']['submit'] = ['#type' => 'submit', '#value' => t('Save')];

    $form['action']['reset'] = ['#type' => 'button', '#value' => t('Reset'), '#attributes' => ['onclick' => '
  this.form.reset(); return false;',]];

    $link = Url::fromUserInput('/my_classroom/');

    $form['action']['cancel'] = ['#markup' => Link::fromTextAndUrl(t('Back to page'), $link, ['
  attributes' => ['class' => 'button']])->toString()];
    return $form;
  }


  public function custom_validate_sub_id_exists(array &$form, FormStateInterface $form_state) {
    $sub_id = $form_state->getValue('sub_id');

    $query = Database::getConnection()->select('subject_table', 's');
    $query->fields('s', ['sub_id'])
        ->condition('sub_id', $sub_id)
        ->range(0, 1); 
    $result = $query->execute()->fetchAssoc();

    if (!$result) {
        $form_state->setErrorByName('sub_id', t('The entered sub_id is not present in the subject_table.'));
    }
}


  public function validateForm(array &$form, FormStateInterface $form_state) {
    $name = $form_state->getValue('stu_name');

    if (preg_match('/[^A-Z[ ]a-z]/', $name)) {
      $form_state->setErrorByName('stu_name', $this->t('Name must be in Characters Only'));
    }

    $sub_id = $form_state->getValue('sub_id');
    $query = Database::getConnection()->select('subject_table', 's');
    $query->fields('s', ['sub_id'])
        ->condition('sub_id', $sub_id)
        ->range(0, 1); // Only need to check if it exists, so limit the query to one result.

    $result = $query->execute()->fetchAssoc();
    if (!$result) {
      $form_state->setErrorByName('sub_id', t('The entered sub_id is not present in the subject_table.'));
  }
    parent::validateForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $field = $form_state->getValues();
    $name = $field['stu_name'];
    $sub_id = $field['sub_id'];
    $email = $field['email'];
    $phone = $field['phone'];


    if (isset($_GET['id'])){
      $field = [
        'stu_name' => $name,
        'sub_id' => $sub_id,
        'email' => $email,
        'phone' => $phone,
      ];
            $database = \Drupal::database();
      $database->update('student_table')
        ->fields($field)
        ->condition('sid', $_GET['id'])
        ->execute();
      $this->messenger()->addMessage('Successfully updated records');
    } 
    else {
      $field = [
        'stu_name' => $name,
        'sub_id' => $sub_id,
        'email' => $email,
        'phone' => $phone,
      ];
       $query = \Drupal::database();
      $query->insert('student_table')->fields($field)->execute();
      $this->messenger()->addMessage('Successfully saved records');
    }
    $this->messenger()->addMessage('Successfully saved records');
    $form_state->setRedirect('my_classroom.myclassroom_form');
}

}