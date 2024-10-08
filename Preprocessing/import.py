import mysql.connector
import csv
import random
from datetime import datetime
import logging


logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
# Connect to SQL DB
conn = mysql.connector.connect(
    host='35.212.179.183',
    user='inf2003-sqldev',
    password='toor',
    database='ELibDatabase'
)

cursor = conn.cursor()


with open('csv_files/Goodreads_books_with_genres.csv', newline='', encoding='utf-8', errors='ignore') as csvfile:
    reader = csv.reader(csvfile)
    row_count = 0


    row_count += 1

    for row in reader:
        try:
            # Extract data from each row
            book_title = row[1]
            authors = row[2].split('/')  
            isbn = row[5].strip()  # Convert ISBN from scientific notation
            language = row[6]
            page_count = int(row[7])
            publish_date = row[10]
            publisher = row[11]
            genres = row[12].split(';')  

            # Convert publish date to MySQL format
            publish_date_mysql = datetime.strptime(publish_date, '%m/%d/%Y').strftime('%Y-%m-%d')
            quantity = random.randint(1, 5)
            logging.info(f"Inserting book: {book_title} (ISBN: {isbn})")
            # Insert book entries into the book table
            cursor.execute("""
                INSERT INTO Booklist (ISBN, bookTitle, quantity, language, publisher, publishDate, pageCount)
                VALUES (%s, %s, %s, %s, %s, %s, %s)
                ON DUPLICATE KEY UPDATE bookTitle = VALUES(bookTitle)
            """, (isbn, book_title, quantity, language, publisher, publish_date_mysql, page_count))

            # Commit the book insert
            conn.commit()

            
            for author in authors:
                # Retrieve the author_id from the Author table
                cursor.execute("SELECT authorID FROM Authors WHERE authorName = %s", (author,))
                author_id = cursor.fetchone()

                # check if author is in the author table
                if author_id:
                    # Insert into the bookAuthor table to link the book and the author
                    cursor.execute("INSERT INTO bookAuthor (book_id, author_id) VALUES (%s, %s)", (isbn, author_id[0]))
                else:
                    print(f"Author '{author}' not found in the database!")


            for genre in genres:
                # Retrieve the genre_id from the Genre table
                cursor.execute("SELECT genreID FROM Genres WHERE genreName = %s", (genre,))
                genre_id = cursor.fetchone()

                # check if genre exists in the Genre table
                if genre_id:
                    # Insert into the bookGenre table to link the book and the genre
                    cursor.execute("INSERT INTO bookGenre (book_id, genre_id) VALUES (%s, %s)", (isbn, genre_id[0]))
                else:
                    print(f"Genre '{genre}' not found in the database!")

            conn.commit()
        except mysql.connector.Error as err:
            print(f"Error: {err}")
            conn.rollback() 

cursor.close()
conn.close()

print("successfully inserted!")
