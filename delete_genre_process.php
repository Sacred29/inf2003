<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $genreToDelete = $_POST['genre'];
     $genres = explode("/", $_POST['genres']);

     // Add genre to your database or array     
     if (($key = array_search($genreToDelete, $genres)) !== false) {
          unset($genres[$key]);
     }

     $response = ['status' => "success", 'genre' => $genreToDelete, 'genres' => implode('/', $genres)];
     // Return a JSON response with the added genre
     echo json_encode($response);
}
