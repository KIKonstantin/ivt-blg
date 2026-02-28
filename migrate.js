import pg from 'pg';
import { hash } from 'bcryptjs';
import dotenv from 'dotenv';

dotenv.config();

const { Pool } = pg;
const pool = new Pool({
  connectionString: process.env.DATABASE_URL,
});

async function migrate() {
  const client = await pool.connect();
  try {
    console.log('Starting migration...');

    // 1. Users
    await client.query(`
      CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        email TEXT UNIQUE NOT NULL,
        password_hash TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'editor',
        created_at TIMESTAMP DEFAULT NOW()
      );
    `);
    console.log('  ✓ users');

    // 2. Sessions
    await client.query(`
      CREATE TABLE IF NOT EXISTS sessions (
        token UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        expires_at TIMESTAMP NOT NULL,
        created_at TIMESTAMP DEFAULT NOW()
      );
    `);
    console.log('  ✓ sessions');

    // 3. Posts
    await client.query(`
      CREATE TABLE IF NOT EXISTS posts (
        id SERIAL PRIMARY KEY,
        title TEXT NOT NULL,
        content TEXT NOT NULL,
        excerpt TEXT,
        image_url TEXT,
        has_gallery BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT NOW(),
        updated_at TIMESTAMP DEFAULT NOW()
      );
    `);
    console.log('  ✓ posts');

    // 4. Post galleries
    await client.query(`
      CREATE TABLE IF NOT EXISTS post_galleries (
        id SERIAL PRIMARY KEY,
        post_id INTEGER NOT NULL REFERENCES posts(id) ON DELETE CASCADE,
        image_url TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT NOW()
      );
    `);
    console.log('  ✓ post_galleries');

    // 5. Comments
    await client.query(`
      CREATE TABLE IF NOT EXISTS comments (
        id SERIAL PRIMARY KEY,
        post_id INTEGER NOT NULL REFERENCES posts(id) ON DELETE CASCADE,
        author_name TEXT NOT NULL,
        content TEXT NOT NULL,
        is_approved BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT NOW()
      );
    `);
    console.log('  ✓ comments');

    // 6. Authors
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
    console.log('  ✓ authors');

    // Seed default admin user if no users exist
    const { rowCount: userCount } = await client.query('SELECT id FROM users LIMIT 1');
    if (userCount === 0) {
      const adminEmail = 'admin@koreni.bg';
      const adminPassword = process.env.ADMIN_PASSWORD || '123123';
      const passwordHash = await hash(adminPassword, 10);
      await client.query(
        'INSERT INTO users (email, password_hash, role) VALUES ($1, $2, $3)',
        [adminEmail, passwordHash, 'admin']
      );
      console.log(`  ✓ admin user seeded (email: ${adminEmail})`);
    }

    // Seed default author if empty
    const { rowCount: authorCount } = await client.query('SELECT id FROM authors LIMIT 1');
    if (authorCount === 0) {
      await client.query(
        'INSERT INTO authors (name, description, content) VALUES ($1, $2, $3)',
        ['Ивета Костадинова', 'Пътешественик и разказвач на истории.', 'Добре дошли в моето кътче на дивата природа.']
      );
      console.log('  ✓ default author seeded');
    }

    console.log('Migration completed successfully.');
  } catch (err) {
    console.error('Migration failed:', err);
    process.exit(1);
  } finally {
    client.release();
    await pool.end();
  }
}

migrate();
