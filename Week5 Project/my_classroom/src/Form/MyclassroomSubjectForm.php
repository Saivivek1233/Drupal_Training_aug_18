<?php

namespace Drupal\my_classroom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Messenger;
use Drupal\Core\Link;

class MyclassroomSubjectForm extends FormBase{

  public function getFormId()
  {
    return 'myclassroom_subject_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $conn1 = Database::getconnection();
    $record = [];
    if (isset($_GET['id'])) {
      $query = $conn1->select('subject_table', 'm')->condition('sub_id', $_GET['id'])->fields('m');
      $record = $query->execute()->fetchAssoc();
    }
    $form['sub_id'] = ['#type' => 'textfield','#attributes'=>array('placeholder'=>t('Subject Id')), '#required' => TRUE, '#default_value' => (isset($record['sub_id']) && $_GET['id']) ? $record['sub_id'] : ''];
    $form['sub_name'] = ['#type' => 'textfield','#attributes'=>array('placeholder'=>t('Subject Name')), '#required' => TRUE, '#default_value' => (isset($record['sub_name']) && $_GET['id']) ? $record['sub_name'] : ''];
    $form['dep_name'] = ['#type' => 'textfield','#attributes'=>array('placeholder'=>t('Department Name')), '#required' => TRUE, '#default_value' => (isset($record['dep_name']) && $_GET['id']) ? $record['dep_name'] : ''];

    $form['action'] = ['#type' => 'action'];

    $form['action']['submit'] = ['#type' => 'submit', '#value' => t('Save')];

    $form['action']['reset'] = ['#type' => 'button', '#value' => t('Reset'), '#attributes' => ['onclick' => '
  this.form.reset(); return false;',]];

    $link = Url::fromUserInput('/my_classroom/');

    $form['action']['cancel'] = ['#markup' => Link::fromTextAndUrl(t('Back to page'), $link, ['
  attributes' => ['class' => 'button']])->toString()];
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $query = Database::getConnection()->select('subject_table', 's');

    $name = $form_state->getValue('sub_name');

    if (preg_match('/[^A-Z[ ]a-z]/', $name)) {
      $form_state->setErrorByName('sub_name', $this->t('Name must be in Characters Only'));
    }

    $sub_id = $form_state->getValue('sub_id');

    $query->fields('s', ['sub_id'])
        ->condition('sub_id', $sub_id, '<>')
        ->range(0, 1); 
    $result = $query->execute()->fetchAssoc();

    if ($result) {
        $form_state->setErrorByName('sub_id', t('The entered sub_id is already present in the subject_table.'));
    }


    // $query = Database::getConnection()->select('subject_table', 's');
    $query->fields('s', ['sub_name'])
        ->condition('sub_name', $name, '<>')
        ->range(0, 1); 
    $result = $query->execute()->fetchAssoc();

    if ($result) {
        $form_state->setErrorByName('sub_name', t('The entered sub_name is alredy present in the subject_table.'));
    }

    parent::validateForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $field = $form_state->getValues();
    $sub_id = $field['sub_id'];
    $name = $field['sub_name'];
    $dep = $field['dep_name'];

    if (isset($_GET['id'])) {
      $field = ['sub_id' => $sub_id,'sub_name' => $name,'dep_name' => $dep];

      $database = \Drupal::database();
      $database->update('subject_table')
        ->fields($field)
        ->condition('sub_id', $_GET['id'])
        ->execute();
      $this->messenger()->addMessage('Successfully updated records');
    } 
    else {
      $field = ['sub_id' => $sub_id,'sub_name' => $name,'dep_name' => $dep];
      $query = \Drupal::database();
      $query->insert('subject_table')->fields($field)->execute();
      $this->messenger()->addMessage('Successfully saved records');
    }
    $this->messenger()->addMessage('Successfully saved records');
    $form_state->setRedirect('my_classroom.myclassroom_form');
}

}