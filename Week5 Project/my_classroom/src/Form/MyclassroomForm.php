<?php

namespace Drupal\my_classroom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Messenger;
use Drupal\Core\Link;

class MyclassroomForm extends FormBase
{
  public function getFormid()
  {
    return 'myclass_form';
  }
  public function buildform(array $form, FormStateInterface $form_state)
  {
    $form['btn1'] = [

        '#type' => 'submit',
        '#value' => $this->t('Students'),
        '#submit' => [[$this, 'btn1']],
      ]; 
      $form['btn2'] = [

        '#type' => 'submit',
        '#value' => $this->t('Subjects'),
        '#submit' => [[$this, 'btn2']],
      ];
      return $form;
  }
  
  public function btn1(array &$form, FormStateInterface $form_state)
  {
    // $this->messenger()->addMessage('Btn1..... clicked');
    
     $form_state->setRedirect('my_classroom.my_classroom_controller_student_listing');
  }
    
  public function btn2(array &$form, FormStateInterface $form_state)
  {
    // $this->messenger()->addMessage('Btn2..... clicked');
    $form_state->setRedirect('my_classroom.my_classroom_controller_subject_listing');

    
  }
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // $this->messenger()->addMessage('submit 1 clicked');
  }

}