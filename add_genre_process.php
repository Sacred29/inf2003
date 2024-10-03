<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $newGenre = $_POST['genre'];
     $genres = explode("/", $_POST['genres']);

     // Add genre to your database or array
     if (in_array($newGenre, $genres) === false) {
          if ($genres[0] == '') {
               $genres[0] = $newGenre;
          } else {
               array_push($genres, $newGenre);
          }
          $response = ['status' => "success", 'genre' => $newGenre, 'genres' => implode('/', $genres)];
     } else {
          $response = ['status' => "exists"];
     }

     // Return a JSON response with the added genre
     echo json_encode($response);
}
