import Database from "better-sqlite3";
import { fileURLToPath } from "node:url";
import { dirname, join } from "node:path";

const __dirname = dirname(fileURLToPath(import.meta.url));
const dbPath = process.env.DB_PATH || join(__dirname, "..", "data.sqlite");

export const db = new Database(dbPath);
db.pragma("journal_mode = WAL");

db.exec(`
  CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    name TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'user'
  );

  CREATE TABLE IF NOT EXISTS complaints (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    subject TEXT NOT NULL,
    description TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'open',
    resolution TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    resolved_at TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
  );

  CREATE TABLE IF NOT EXISTS notifications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    complaint_id INTEGER,
    title TEXT NOT NULL,
    message TEXT NOT NULL,
    is_read INTEGER NOT NULL DEFAULT 0,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (complaint_id) REFERENCES complaints(id)
  );

  CREATE TABLE IF NOT EXISTS messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    content TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'pending',
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    reviewed_at TEXT,
    reviewed_by INTEGER,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (reviewed_by) REFERENCES users(id)
  );
`);

function ensureUserColumns() {
  const columns = db.prepare("PRAGMA table_info(users)").all();
  const names = new Set(columns.map((col) => col.name));
  if (!names.has("email")) {
    db.exec("ALTER TABLE users ADD COLUMN email TEXT");
  }
  if (!names.has("avatar_url")) {
    db.exec("ALTER TABLE users ADD COLUMN avatar_url TEXT");
  }
  if (!names.has("premium_plan")) {
    db.exec("ALTER TABLE users ADD COLUMN premium_plan TEXT NOT NULL DEFAULT 'none'");
  }
  if (!names.has("premium_until")) {
    db.exec("ALTER TABLE users ADD COLUMN premium_until TEXT");
  }
}

function seedUsers() {
  const count = db.prepare("SELECT COUNT(*) AS c FROM users").get().c;
  if (count === 0) {
    const insert = db.prepare(
      `INSERT INTO users (username, name, role, email, avatar_url, premium_plan, premium_until)
       VALUES (?, ?, ?, ?, ?, ?, ?)`
    );
    insert.run(
      "admin",
      "Yönetici",
      "admin",
      "mehmet49800@gmail.com",
      "https://api.dicebear.com/7.x/bottts-neutral/svg?seed=admin",
      "none",
      null
    );
    insert.run(
      "ayse",
      "Ayşe Yılmaz",
      "user",
      "ayse@gonulkoprusu.com",
      "https://api.dicebear.com/7.x/avataaars/svg?seed=ayse",
      "pro",
      "2026-12-31"
    );
    insert.run(
      "mehmet",
      "Mehmet Demir",
      "user",
      "mehmet@gonulkoprusu.com",
      "https://api.dicebear.com/7.x/avataaars/svg?seed=mehmet",
      "none",
      null
    );
  }

  db.prepare(
    `UPDATE users
     SET email = COALESCE(email, username || '@gonulkoprusu.com'),
         avatar_url = COALESCE(avatar_url, 'https://api.dicebear.com/7.x/avataaars/svg?seed=' || username),
         premium_plan = COALESCE(premium_plan, 'none')
     WHERE role != 'admin'`
  ).run();

  db.prepare(
    `UPDATE users
     SET email = COALESCE(email, 'mehmet49800@gmail.com'),
         avatar_url = COALESCE(avatar_url, 'https://api.dicebear.com/7.x/bottts-neutral/svg?seed=admin'),
         premium_plan = COALESCE(premium_plan, 'none')
     WHERE role = 'admin'`
  ).run();
}

function seedMessages() {
  const count = db.prepare("SELECT COUNT(*) AS c FROM messages").get().c;
  if (count > 0) return;
  const rows = [
    [2, "Premium üyelik nasıl alabilirim?", "pending"],
    [3, "Uygulama içinde mesajlaşma çok yavaş çalışıyor.", "pending"],
    [2, "Topluluk kurallarına aykırı içerik bildiriyorum.", "approved"],
  ];
  const insert = db.prepare(
    "INSERT INTO messages (user_id, content, status) VALUES (?, ?, ?)"
  );
  for (const row of rows) {
    insert.run(...row);
  }
}

ensureUserColumns();
seedUsers();
seedMessages();
