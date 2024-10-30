import pandas as pd

# Load your genres CSV into a dataframe
genres_df = pd.read_csv('csv_files/genres.csv')


split_genres_df = genres_df['genres'].str.split(';', expand=True).stack().reset_index(level=1, drop=True)


split_genres_df = split_genres_df.str.strip()

unique_genres_df = pd.DataFrame(split_genres_df, columns=['genres']).drop_duplicates().reset_index(drop=True)
unique_genres_df['genre_id'] = unique_genres_df.index + 1  # Assign new genre IDs starting from 1

unique_genres_df.to_csv('csv_files/cleaned_genres.csv', index=False)

