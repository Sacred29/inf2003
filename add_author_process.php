<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $newAuthor = $_POST['author'];
     $authors = explode("/", $_POST['authors']);


     // Add genre to your database or array
     if (in_array($newAuthor, $authors) === false) {
          if ($authors[0] == '') {
               $authors[0] = $newAuthor;
          } else {
               array_push($authors, $newAuthor);
          }
          $response = ['status' => "success", 'author' => $newAuthor, 'authors' => implode('/', $authors)];
     } else {
          $response = ['status' => "exists"];
     }
     // Add genre to your database or array

     // Return a JSON response with the added genre
     echo json_encode($response);
}
