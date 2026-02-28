import pg from 'pg';
import dotenv from 'dotenv';

dotenv.config();

const { Pool } = pg;
const pool = new Pool({
    connectionString: process.env.DATABASE_URL,
});

async function migrate() {
    const client = await pool.connect();
    try {
        console.log('Starting migration v4...');

        // Create authors table
        await client.query(`
      CREATE TABLE IF NOT EXISTS authors (
        id SERIAL PRIMARY KEY,
        name TEXT NOT NULL,
        description TEXT,
        content TEXT,
        image_url TEXT,
        created_at TIMESTAMP DEFAULT NOW(),
        updated_at TIMESTAMP DEFAULT NOW()
      );
    `);

        // Insert a default record if empty
        const { rowCount } = await client.query('SELECT id FROM authors LIMIT 1');
        if (rowCount === 0) {
            await client.query(`
        INSERT INTO authors (name, description, content)
        VALUES ($1, $2, $3)
      `, ['Ивета Костадинова', 'Пътешественик и разказвач на истории.', 'Добре дошли в моето кътче на дивата природа.']);
        }

        console.log('Migration v4 completed successfully.');
    } catch (err) {
        console.error('Migration v4 failed:', err);
    } finally {
        client.release();
        await pool.end();
    }
}

migrate();
