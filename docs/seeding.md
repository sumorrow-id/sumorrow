# Database Seeding

When setting up the database for the first time or wanting to reset the data, you will often need to seed the baseline data (Provinces, Mountains, Basecamps, and Mountain Images).

## Where is the data?

The seed data is stored in a JSON file to make it straightforward to track changes within version control and make it simple for anyone on the team to pull and sync.

Location: `database/data/mountains_seeder.json`

## How it works

The main seeder `MountainSeeder` handles everything.
1. It reads the local `database/data/mountains_seeder.json` file.
2. It parses the data and creates the **Provinces** based on the provinces mapping in the JSON.
3. It creates or updates the **Mountains** based on their names.
4. It iterates and creates relations like **Mountain Images** and **Basecamps**.

## Running the Seeder

To construct the tables and insert the seed data automatically, run the `migrate:fresh` command with the `--seed` flag.

```bash
php artisan migrate:fresh --seed
```

If you just want to run the seeder without dropping tables (assuming the schema is already updated):

```bash
php artisan db:seed
```

## Adding new data

If you need to add new mountains or modify existing ones, update the `database/data/mountains_seeder.json` directly. Afterwards, running `php artisan db:seed` will simply update the records in the database via the `updateOrCreate` functionality without crashing due to duplicates.
