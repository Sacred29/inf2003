import pandas as pd

import pandas as pd


df = pd.read_csv('csv_files/BooksData.csv')

# Extract unique authors and assign ID
authors_df = df[['Author']].drop_duplicates().reset_index(drop=True)
authors_df['author_id'] = authors_df.index + 1  

# Extract unique genres and assign ID
genres_df = df[['genres']].drop_duplicates().reset_index(drop=True)
genres_df['genre_id'] = genres_df.index + 1  #

authors_df.to_csv('csv_files/authors.csv', index=False)
genres_df.to_csv('csv_files/genres.csv', index=False)