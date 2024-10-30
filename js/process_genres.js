$(document).ready(function () {
     // Add Genre AJAX
     $('#genreForm').submit(function (event) {
          event.preventDefault(); // Prevent the default form submission
          let genre = $('#genre').val(); // Get the selected genre
          let genres = $('#genresArray').val();
          $.ajax({
               url: 'add_genre_process.php', // Backend script for adding the genre
               method: 'POST',
               data: {
                    genre: genre,
                    genres: genres
               }, // Send the form data
               success: function (response) {
                    var jsonResponse = JSON.parse(response);
                    if (jsonResponse["status"] === 'success') {
                         // Add the new genre to the list
                         $('#genresList').append('<li>' + jsonResponse["genre"] +
                              '<button class="deleteGenreBtn" data-genre="' + jsonResponse["genre"] + '">Delete</button></li>');
                         $('#genresArray').val(jsonResponse["genres"])
                    } else if (jsonResponse["status"] === 'exists') {

                    } else {
                         alert('Error adding genre: ' + response.message);
                    }
               },
               error: function (xhr, status, error) {
                    alert('AJAX Error: ' + error);
               }
          });
     });

     // Delete Genre AJAX
     $(document).on('click', '.deleteGenreBtn', function (event) {
          event.preventDefault();
          let genre = $(this).data('genre'); // Get the selected genre
          let genres = $('#genresArray').val();
          $.ajax({
               url: 'delete_genre_process.php', // Backend script for deleting the genre
               method: 'POST',
               data: {
                    genre: genre,
                    genres: genres
               },
               success: function (response) {
                    var jsonResponse = JSON.parse(response);
                    if (jsonResponse["status"] === 'success') {
                         // Remove the genre from the list
                         $('button[data-genre="' + jsonResponse["genre"] + '"]').closest('li').remove();
                         $('#genresArray').val(jsonResponse["genres"])
                    } else {
                         alert('Error deleting genre: ' + response.message);
                    }
               },
               error: function (xhr, status, error) {
                    alert('AJAX Error: ' + error);
               }
          });
     });
});