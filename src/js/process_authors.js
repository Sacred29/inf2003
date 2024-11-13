
$(document).ready(function () {
     // Add Author AJAX
     $('#authorForm').submit(function (event) {
          event.preventDefault(); // Prevent the default form submission
          let author = $('#author').val(); // Get the author name
          let authors = $('#authorsArray').val();
          $.ajax({
               url: 'add_author_process.php', // Backend script for adding the author
               method: 'POST',
               data: {
                    author: author,
                    authors: authors
               }, // Send the form data
               success: function (response) {
                    var jsonResponse = JSON.parse(response);
                    if (jsonResponse["status"] === 'success') {
                         // Add the new author to the list
                         $('#authorsList').append('<li>' + jsonResponse["author"] +
                              '<button class="deleteAuthorBtn" data-author="' + jsonResponse["author"] + '">Delete</button></li>');
                         $('#author').val(''); // Clear the input field
                         $('#authorsArray').val(jsonResponse["authors"])
                    } else if (jsonResponse["status"] === 'exists') {
                         $('#author').val(''); // Clear the input field
                    } else {
                         alert('Error adding author: ' + response.message);
                    }
               },
               error: function (xhr, status, error) {
                    alert('AJAX Error: ' + error);
               }
          });
     });
     var authors = $('#authorsArray').val();
     // Delete Author AJAX
     $(document).on('click', '.deleteAuthorBtn', function (event) {
          event.preventDefault();
          let author = $(this).data('author'); // Get the author name from the button
          let authors = $('#authorsArray').val();
          $.ajax({
               url: 'delete_author_process.php', // Backend script for deleting the author
               method: 'POST',
               data: {
                    author: author,
                    authors: authors
               },
               success: function (response) {
                    var jsonResponse = JSON.parse(response);
                    if (jsonResponse["status"] === 'success') {
                         // Remove the author from the list
                         $('button[data-author="' + jsonResponse["author"] + '"]').closest('li').remove();
                         $('#authorsArray').val(jsonResponse["authors"])
                    } else {
                         alert('Error deleting author: ' + response.message);
                    }
               },
               error: function (xhr, status, error) {
                    alert('AJAX Error: ' + error);
               }
          });
     });
})