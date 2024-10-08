import pandas as pd


# Load your authors CSV into a dataframe
authors_df = pd.read_csv('csv_files/authors.csv')

# Create a new dataframe by splitting authors on '/' and stacking them into new rows
split_authors_df = authors_df['Author'].str.split('/', expand=True).stack().reset_index(level=1, drop=True)

# Create a new dataframe with unique authors and reset their index to create new author IDs
unique_authors_df = pd.DataFrame(split_authors_df, columns=['Author']).drop_duplicates().reset_index(drop=True)
unique_authors_df['author_id'] = unique_authors_df.index + 1  # Assign new author IDs starting from 1

# Save the cleaned authors data to a new CSV
unique_authors_df.to_csv('csv_files/cleaned_authors.csv', index=False)
