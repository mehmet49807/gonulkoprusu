import express from "express";
import cors from "cors";
import { db } from "./db.js";

const app = express();
app.use(cors());
app.use(express.json());

const PORT = process.env.PORT || 4000;

function getUserById(id) {
  return db.prepare("SELECT id, username, name, role FROM users WHERE id = ?").get(id);
}

app.get("/api/health", (_req, res) => {
  res.json({ ok: true });
});

// Basit giriş: kullanıcı adı ile (demo amaçlı, parola yok)
app.post("/api/login", (req, res) => {
  const { username } = req.body || {};
  if (!username) return res.status(400).json({ error: "username gerekli" });
  const user = db
    .prepare("SELECT id, username, name, role FROM users WHERE username = ?")
    .get(String(username).trim().toLowerCase());
  if (!user) return res.status(404).json({ error: "Kullanıcı bulunamadı" });
  res.json(user);
});

// Şikayetleri listele. Admin tümünü, kullanıcı sadece kendininkini görür.
app.get("/api/complaints", (req, res) => {
  const userId = Number(req.query.userId);
  const user = getUserById(userId);
  if (!user) return res.status(401).json({ error: "Geçersiz kullanıcı" });

  const rows =
    user.role === "admin"
      ? db
          .prepare(
            `SELECT c.*, u.name AS user_name FROM complaints c
             JOIN users u ON u.id = c.user_id
             ORDER BY c.created_at DESC`
          )
          .all()
      : db
          .prepare(
            `SELECT c.*, u.name AS user_name FROM complaints c
             JOIN users u ON u.id = c.user_id
             WHERE c.user_id = ? ORDER BY c.created_at DESC`
          )
          .all(userId);
  res.json(rows);
});

// Yeni şikayet oluştur (kullanıcı)
app.post("/api/complaints", (req, res) => {
  const { userId, subject, description } = req.body || {};
  const user = getUserById(Number(userId));
  if (!user) return res.status(401).json({ error: "Geçersiz kullanıcı" });
  if (!subject || !description)
    return res.status(400).json({ error: "Konu ve açıklama gerekli" });

  const info = db
    .prepare(
      "INSERT INTO complaints (user_id, subject, description) VALUES (?, ?, ?)"
    )
    .run(user.id, String(subject).trim(), String(description).trim());
  const complaint = db
    .prepare("SELECT * FROM complaints WHERE id = ?")
    .get(info.lastInsertRowid);
  res.status(201).json(complaint);
});

// Şikayeti sonuçlandır (yönetici) -> kullanıcıya bildirim gönderilir
app.post("/api/complaints/:id/resolve", (req, res) => {
  const adminId = Number(req.body?.adminId);
  const admin = getUserById(adminId);
  if (!admin || admin.role !== "admin")
    return res.status(403).json({ error: "Yönetici yetkisi gerekli" });

  const { status, resolution } = req.body || {};
  const newStatus = status === "rejected" ? "rejected" : "resolved";
  if (!resolution || !String(resolution).trim())
    return res.status(400).json({ error: "Sonuç açıklaması gerekli" });

  const complaint = db
    .prepare("SELECT * FROM complaints WHERE id = ?")
    .get(Number(req.params.id));
  if (!complaint) return res.status(404).json({ error: "Şikayet bulunamadı" });

  const tx = db.transaction(() => {
    db.prepare(
      `UPDATE complaints SET status = ?, resolution = ?, resolved_at = datetime('now') WHERE id = ?`
    ).run(newStatus, String(resolution).trim(), complaint.id);

    const statusLabel = newStatus === "resolved" ? "çözüldü" : "reddedildi";
    db.prepare(
      `INSERT INTO notifications (user_id, complaint_id, title, message)
       VALUES (?, ?, ?, ?)`
    ).run(
      complaint.user_id,
      complaint.id,
      `Şikayetiniz ${statusLabel}`,
      `"${complaint.subject}" başlıklı şikayetiniz ${statusLabel}. Yönetici sonucu: ${String(
        resolution
      ).trim()}`
    );
  });
  tx();

  const updated = db
    .prepare("SELECT * FROM complaints WHERE id = ?")
    .get(complaint.id);
  res.json(updated);
});

// Kullanıcının bildirimleri
app.get("/api/notifications", (req, res) => {
  const userId = Number(req.query.userId);
  const user = getUserById(userId);
  if (!user) return res.status(401).json({ error: "Geçersiz kullanıcı" });
  const rows = db
    .prepare(
      "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC"
    )
    .all(userId);
  res.json(rows);
});

// Bildirimi okundu işaretle
app.post("/api/notifications/:id/read", (req, res) => {
  const userId = Number(req.body?.userId);
  const notif = db
    .prepare("SELECT * FROM notifications WHERE id = ?")
    .get(Number(req.params.id));
  if (!notif || notif.user_id !== userId)
    return res.status(404).json({ error: "Bildirim bulunamadı" });
  db.prepare("UPDATE notifications SET is_read = 1 WHERE id = ?").run(notif.id);
  res.json({ ok: true });
});

app.listen(PORT, () => {
  console.log(`[server] Gönül Köprüsü API http://localhost:${PORT}`);
});
