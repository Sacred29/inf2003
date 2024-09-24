// Function to open overlay with dynamic data from DOM
function openOverlay(bookElement) {
    const overlay = document.getElementById('overlay');
    const overlayImg = document.getElementById('overlay-img');
    const overlayTitle = document.getElementById('overlay-title');
    const overlayISBN = document.getElementById('overlay-isbn');
    const overlayDescription = document.getElementById('overlay-description');
    
    // Get the necessary book details from the clicked element's child nodes or attributes
    const imgSrc = bookElement.querySelector('img') ? bookElement.querySelector('img').src : 'default.jpg'; // Fallback image if missing
    const title = bookElement.querySelector('.bookTitle').textContent;
    const isbn = bookElement.querySelector('.ISBN').textContent;
    const publisher = bookElement.querySelector('.publisher').textContent;
    const quantity = bookElement.querySelector('.quantity').textContent;
    const language = bookElement.querySelector('.language').textContent;
    const publishDate = bookElement.querySelector('.publishDate').textContent;
    const pageCount = bookElement.querySelector('.pageCount').textContent;
    const overlayBorrowButton = document.getElementById('overlay-borrow-button');
    const author = bookElement.querySelector('.bookTitle').dataset.author;

    // Set the overlay content dynamically
    overlayImg.src = imgSrc;
    overlayTitle.textContent = title;
    overlayISBN.innerHTML = `<bold>ISBN</bold>: ${isbn}`;
    overlayDescription.innerHTML = `Author: ${author}<br>Publisher: ${publisher} <br>Quantity: ${quantity} <br>Language: ${language} <br>Published Date: ${publishDate} <br>Page Count: ${pageCount}<br>`;
    
    // Grey out borrow button
    if (quantity < 1) {
        overlayBorrowButton.disabled = true;
        overlayBorrowButton.textContent = "Out of Stock"; 
    } else {
        overlayBorrowButton.disabled = false; 
        overlayBorrowButton.textContent = "Borrow"; 
    }
    overlay.style.display = 'flex';
}

// Function to borrow 
// Amend here
function borrow() {
    console.log("borrow!!!!!!")
    let form = document.getElementById("borrowForm");
    //set value
    document.getElementById("form-isbn").value = document.getElementById("overlay-isbn").innerText.split(" ")[1];
    let date = new Date();
    let expirydate = new Date()
    expirydate.setDate(date.getDate() + 7);
    let formattedBorrow = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDate();
    let formattedExpiry = expirydate.getFullYear() + "-" + expirydate.getMonth() + "-" + expirydate.getDate();
    document.getElementById("form-borrowdate").value = formattedBorrow;
    document.getElementById("form-expirydate").value = formattedExpiry;
    form.submit();
}

// Function to close overlay
function closeOverlay() {
    const overlay = document.getElementById('overlay');
    overlay.style.display = 'none';
}

// Add click event listeners to all book elements
document.querySelectorAll('.book').forEach(book => {
    book.addEventListener('click', function() {
        openOverlay(this);
    });
});

// Add click event listener to close button
document.getElementById('close-btn').addEventListener('click', closeOverlay);
