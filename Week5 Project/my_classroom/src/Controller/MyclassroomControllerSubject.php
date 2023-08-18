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

 

 

class MyclassroomControllerSubject extends ControllerBase{

 

public function Listing()

    {

 

      // Define table headers for the listing table

      $header_table1 = [

        'sub_id' => t('SUB ID'),

        'sub_name' => t('SUB Name'),
        'dep_name' => t('Dep Name'),


        'opt' => t('Edit Operation'),

        'opt1' => t('Delete Operation'),

      ];

      $row = [];

     

      // Get a database connection

      $conn = Database::getConnection();

     

      // Query the 'my_crud' table for records

      $query = $conn->select('subject_table', 'm');

      $query->fields('m', ['sub_id', 'sub_name','dep_name']);

      $result = $query->execute()->fetchAll();

 

      // Loop through the results and build table rows with edit and delete links

      foreach ($result as $value) {

        $delete = Url::fromUserInput('/my_classroom/Subjectform/delete/' . $value->sub_id);

        $edit = Url::fromUserInput('/my_classroom/subject_form/data?id=' . $value->sub_id);

 

        $row[] = [

          'sub_id' => $value->sub_id,

          'sub_name' => $value->sub_name,
          'dep_name' => $value->dep_name,


          'opt' => Link::fromTextAndUrl('Edit', $edit)->toString(),

          'opt1' => Link::fromTextAndUrl('Delete', $delete)->toString(),

        ];

      }

     

      // Create a link for adding a new user

      $add = Url::fromUserInput('/my_classroom/subject_form/data');

      $text = "Add Subject";

 

      // Build the table with records, headers, and an add link

      $data['table'] = [

        '#type' => 'table',

        '#header' => $header_table1,

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