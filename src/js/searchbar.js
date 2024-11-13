// Handle form submission
document.querySelector('.search-bar form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    let searchTerm = document.getElementById('search').value.trim();
    let searchType = document.querySelector('input[name="searchType"]:checked').value;

    if (searchTerm) {
        // Use Fetch API to send the data to a PHP script that handles setting the session
        fetch('set_session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'search=' + encodeURIComponent(searchTerm) + '&searchType=' + encodeURIComponent(searchType) + '&page=1'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error submitting search');
            }
            return response.text();
        })
        .then(data => {
            // Redirect after setting the session
            window.location.href = 'index.php?search=' + encodeURIComponent(searchTerm) + '&searchType=' + encodeURIComponent(searchType);
        })
        .catch(error => {
            console.error('Error in search submission:', error);
            alert('There was a problem submitting your search. Please try again.');
        });
    }
});
