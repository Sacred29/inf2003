document.addEventListener("DOMContentLoaded", function () {
    // Function to open overlay and populate it with book details
    function openOverlay(book) {
        document.getElementById("overlay-title").textContent = book.title;
        document.getElementById("overlay-isbn").textContent = `ISBN: ${book.isbn}`;
        document.getElementById("overlay-publisher").textContent = `Publisher: ${book.publisher}`;
        document.getElementById("overlay-language").textContent = `Language: ${book.language}`;
        document.getElementById("overlay-publishDate").textContent = `Published: ${book.publishDate}`;
        document.getElementById("overlay-pageCount").textContent = `Pages: ${book.pageCount}`;
        document.getElementById("overlay-genre").textContent = `Genres: ${book.genres.join(" / ")}`;
        document.getElementById("overlay-authors").textContent = `Authors: ${book.authors.join(", ")}`;
        document.getElementById("overlay-authors").textContent = `Quantity: ${book.quantity}`;

        // Show the overlay
        document.getElementById("overlay").style.display = "flex";
    }

    // Function to close overlay
    function closeOverlay() {
        document.getElementById("overlay").style.display = "none";
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
        let formattedBorrow = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
        let formattedExpiry = expirydate.getFullYear() + "-" + (expirydate.getMonth() + 1) + "-" + expirydate.getDate();
        document.getElementById("form-borrowdate").value = formattedBorrow;
        document.getElementById("form-expirydate").value = formattedExpiry;
        document.getElementById("form-quantity").value = globalQuantity;
        form.submit();
    }


    // Add click events to each book card
    document.querySelectorAll(".book").forEach(function (card) {
        card.addEventListener("click", function () {
            // Extract book details from the card's data attributes
            const book = {
                title: card.querySelector(".bookTitle").textContent,
                isbn: card.querySelector(".ISBN").textContent,
                publisher: card.querySelector(".publisher").textContent,
                quantity: card.querySelector(".quantity").textContent,
                language: card.querySelector(".language").textContent,
                publishDate: card.querySelector(".publishDate").textContent,
                pageCount: card.querySelector(".pageCount").textContent,
                genres: card.querySelector(".genre").textContent.split(" / "),
                authors: card.getAttribute("data-author").split(", ")
            };

            openOverlay(book);
        });
    });

    document.getElementById("close-btn").addEventListener("click", closeOverlay);
});
