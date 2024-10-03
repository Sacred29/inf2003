<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $authorToDelete = $_POST['author'];
     $authors = explode("/", $_POST['authors']);

     // Add genre to your database or array     
     if (($key = array_search($authorToDelete, $authors)) !== false) {
          unset($authors[$key]);
     }

     $response = ['status' => "success", 'author' => $authorToDelete, 'authors' => implode('/', $authors)];
     // Return a JSON response with the added genre
     echo json_encode($response);
}
