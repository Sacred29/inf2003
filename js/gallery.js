// Book details (sample data)
// Remove after db integrated
const bookDetails = {
    1: {
        img: 'book1.jpg',
        title: 'Book Title 1',
        author: 'Author Name 1',
        description: 'This is a description for Book Title 1.'
    },
    2: {
        img: 'book2.jpg',
        title: 'Book Title 2',
        author: 'Author Name 2',
        description: 'This is a description for Book Title 2.'
    },
    3: {
        img: 'book3.jpg',
        title: 'Book Title 3',
        author: 'Author Name 3',
        description: 'This is a description for Book Title 3.'
    }
};

// Function to open overlay
function openOverlay(bookId) {
    const overlay = document.getElementById('overlay');
    const overlayImg = document.getElementById('overlay-img');
    const overlayTitle = document.getElementById('overlay-title');
    const overlayAuthor = document.getElementById('overlay-author');
    const overlayDescription = document.getElementById('overlay-description');
    
    // replace with sql
    const book = bookDetails[bookId];
    
    overlayImg.src = book.img;
    overlayTitle.textContent = book.title;
    overlayAuthor.textContent = `By ${book.author}`;
    overlayDescription.textContent = book.description;
    
    overlay.style.display = 'flex';
}

// Function to close overlay
function closeOverlay() {
    const overlay = document.getElementById('overlay');
    overlay.style.display = 'none';
}

// Add click event listeners to all book elements
document.querySelectorAll('.book').forEach(book => {
    book.addEventListener('click', function() {
        const bookId = this.getAttribute('data-book');
        openOverlay(bookId);
    });
});

// Add click event listener to close button
document.getElementById('close-btn').addEventListener('click', closeOverlay);
