<?php

 

/**

 * @file

 * Install, update and uninstall functions for the my_crud module.

 */

 

namespace Drupal\my_classroom\Controller;

 

use Drupal\Core\Controller\ControllerBase;

use Drupal\Core\Database\Database;

use Drupal\Core\Url;

use Drupal\Core\Messenger;

use Drupal\Core\Link;

 

 

class MyclassroomControllerStudent extends ControllerBase{

   

    // Controller function for listing records

  public function Listing()

  {

 

    // Define table headers for the listing table

    $header_table = [

      'sid' => t('Stu ID'),

      'stu_name' => t('Name'),
      'sub_id' => t('Subject_Id'),
      'email' => t('Email'),
      'phone' => t('Phone'),

      'opt' => t('Edit Operation'),

      'opt1' => t('Delete Operation'),

    ];

    $row = [];

   

    // Get a database connection

    $conn = Database::getConnection();

   

    // Query the 'my_crud' table for records

    $query = $conn->select('student_table', 'm');

    $query->fields('m', ['sid', 'stu_name','sub_id','email','phone']);

    $result = $query->execute()->fetchAll();

 

    // Loop through the results and build table rows with edit and delete links

    foreach ($result as $value) {

      $delete = Url::fromUserInput('/my_classroom/Studentform/delete/' . $value->sid);

      $edit = Url::fromUserInput('/my_classroom/student_form/data?id=' . $value->sid);

 

      $row[] = [

        'sid' => $value->sid,
        'stu_name' => $value->stu_name,
        'sub_id' => $value->sub_id,
        'email' => $value->email,
        'phone' => $value->phone,
        'opt' => Link::fromTextAndUrl('Edit', $edit)->toString(),
        'opt1' => Link::fromTextAndUrl('Delete', $delete)->toString(),

      ];

    }

   

    // Create a link for adding a new user

    $add = Url::fromUserInput('/my_classroom/student_form/data');

    $text = "Add User";

 

    // Build the table with records, headers, and an add link

    $data['table'] = [

      '#type' => 'table',

      '#header' => $header_table,

      '#rows' => $row,

      '#empty' => t('No record found'),

      '#caption' => Link::fromTextAndUrl($text, $add)->toString(),

    ];

 

    // Add a message indicating successful listing of records

    $this->messenger()->addMessage('Records Listed');

 

    // Return the data to be rendered

    return $data;

  }

}