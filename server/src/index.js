import express from "express";
import cors from "cors";
import { db } from "./db.js";

const app = express();
app.use(cors());
app.use(express.json());

const PORT = process.env.PORT || 4000;

function getUserById(id) {
  return db
    .prepare(
      `SELECT id, username, name, role, email, avatar_url, premium_plan, premium_until
       FROM users WHERE id = ?`
    )
    .get(id);
}

function requireAdmin(res, adminId) {
  const admin = getUserById(Number(adminId));
  if (!admin || admin.role !== "admin") {
    res.status(403).json({ error: "Yönetici yetkisi gerekli" });
    return null;
  }
  return admin;
}

app.get("/api/health", (_req, res) => {
  res.json({ ok: true });
});

// Basit giriş: kullanıcı adı veya e-posta ile (demo amaçlı, parola yok)
app.post("/api/login", (req, res) => {
  const { username } = req.body || {};
  const login = String(username || "").trim().toLowerCase();
  if (!login) return res.status(400).json({ error: "username gerekli" });
  const user = db
    .prepare(
      `SELECT id, username, name, role, email, avatar_url, premium_plan, premium_until
       FROM users
       WHERE lower(username) = ? OR lower(email) = ?`
    )
    .get(login, login);
  if (!user) return res.status(404).json({ error: "Kullanıcı bulunamadı" });
  res.json(user);
});

// Admin - kullanıcı yönetimi listesi
app.get("/api/admin/users", (req, res) => {
  const admin = requireAdmin(res, req.query.adminId);
  if (!admin) return;

  const rows = db
    .prepare(
      `SELECT
         u.id,
         u.username,
         u.name,
         u.role,
         u.email,
         u.avatar_url,
         u.premium_plan,
         u.premium_until,
         COUNT(c.id) AS complaint_count
       FROM users u
       LEFT JOIN complaints c ON c.user_id = u.id
       GROUP BY u.id
       ORDER BY CASE WHEN u.role = 'admin' THEN 0 ELSE 1 END, u.name ASC`
    )
    .all();

  res.json(rows);
});

// Admin - premium atama/güncelleme
app.post("/api/admin/users/:id/premium", (req, res) => {
  const admin = requireAdmin(res, req.body?.adminId);
  if (!admin) return;

  const targetId = Number(req.params.id);
  const target = getUserById(targetId);
  if (!target) return res.status(404).json({ error: "Kullanıcı bulunamadı" });
  if (target.role === "admin") {
    return res.status(400).json({ error: "Yönetici hesabına premium atanamaz" });
  }

  const allowedPlans = new Set(["none", "pro", "gold", "platinum"]);
  const plan = String(req.body?.plan || "none").toLowerCase();
  if (!allowedPlans.has(plan)) {
    return res.status(400).json({ error: "Geçersiz premium planı" });
  }

  const until = req.body?.until ? String(req.body.until) : null;
  const premiumUntil = plan === "none" ? null : until || "2026-12-31";

  db.prepare(
    "UPDATE users SET premium_plan = ?, premium_until = ? WHERE id = ?"
  ).run(plan, premiumUntil, targetId);

  res.json(getUserById(targetId));
});

// Admin - kullanıcı silme
app.delete("/api/admin/users/:id", (req, res) => {
  const admin = requireAdmin(res, req.query.adminId);
  if (!admin) return;

  const targetId = Number(req.params.id);
  const target = getUserById(targetId);
  if (!target) return res.status(404).json({ error: "Kullanıcı bulunamadı" });
  if (target.role === "admin") {
    return res.status(400).json({ error: "Yönetici hesabı silinemez" });
  }

  const tx = db.transaction(() => {
    const complaintIds = db
      .prepare("SELECT id FROM complaints WHERE user_id = ?")
      .all(targetId)
      .map((row) => row.id);

    if (complaintIds.length > 0) {
      const placeholders = complaintIds.map(() => "?").join(",");
      db.prepare(
        `DELETE FROM notifications WHERE complaint_id IN (${placeholders})`
      ).run(...complaintIds);
    }

    db.prepare("DELETE FROM notifications WHERE user_id = ?").run(targetId);
    db.prepare("DELETE FROM complaints WHERE user_id = ?").run(targetId);
    db.prepare("DELETE FROM messages WHERE user_id = ?").run(targetId);
    db.prepare("DELETE FROM users WHERE id = ?").run(targetId);
  });
  tx();

  res.json({ ok: true });
});

// Admin - premium listesi
app.get("/api/admin/premium-users", (req, res) => {
  const admin = requireAdmin(res, req.query.adminId);
  if (!admin) return;
  const rows = db
    .prepare(
      `SELECT id, username, name, email, avatar_url, premium_plan, premium_until
       FROM users
       WHERE premium_plan != 'none'
       ORDER BY premium_until ASC, name ASC`
    )
    .all();
  res.json(rows);
});

// Admin - mesaj denetimi listesi
app.get("/api/admin/messages", (req, res) => {
  const admin = requireAdmin(res, req.query.adminId);
  if (!admin) return;

  const status = String(req.query.status || "all").toLowerCase();
  const rows =
    status === "all"
      ? db
          .prepare(
            `SELECT m.*, u.name AS user_name, u.username, u.avatar_url
             FROM messages m
             JOIN users u ON u.id = m.user_id
             ORDER BY m.created_at DESC`
          )
          .all()
      : db
          .prepare(
            `SELECT m.*, u.name AS user_name, u.username, u.avatar_url
             FROM messages m
             JOIN users u ON u.id = m.user_id
             WHERE m.status = ?
             ORDER BY m.created_at DESC`
          )
          .all(status);

  res.json(rows);
});

// Admin - mesaj onay/gizleme
app.post("/api/admin/messages/:id/moderate", (req, res) => {
  const admin = requireAdmin(res, req.body?.adminId);
  if (!admin) return;

  const messageId = Number(req.params.id);
  const message = db.prepare("SELECT * FROM messages WHERE id = ?").get(messageId);
  if (!message) return res.status(404).json({ error: "Mesaj bulunamadı" });

  const action = String(req.body?.action || "").toLowerCase();
  const mapping = { approve: "approved", hide: "hidden", pending: "pending" };
  const nextStatus = mapping[action];
  if (!nextStatus) return res.status(400).json({ error: "Geçersiz işlem" });

  db.prepare(
    `UPDATE messages
     SET status = ?, reviewed_at = datetime('now'), reviewed_by = ?
     WHERE id = ?`
  ).run(nextStatus, admin.id, messageId);

  const updated = db
    .prepare(
      `SELECT m.*, u.name AS user_name, u.username, u.avatar_url
       FROM messages m
       JOIN users u ON u.id = m.user_id
       WHERE m.id = ?`
    )
    .get(messageId);

  res.json(updated);
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
