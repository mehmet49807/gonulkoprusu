import express from "express";
import cors from "cors";
import { db } from "./db.js";

const app = express();
app.use(cors());
app.use(express.json());

const PORT = process.env.PORT || 4000;

// ── AI Content Moderation Engine ──

const IBAN_REGEX = /\b[A-Z]{2}\d{2}[\s]?[\dA-Z]{4}[\s]?[\dA-Z]{4}[\s]?[\dA-Z]{4}[\s]?[\dA-Z]{4}[\s]?[\dA-Z]{0,6}\b/i;
const TR_IBAN_REGEX = /\bTR\s?\d{2}\s?\d{4}\s?\d{4}\s?\d{4}\s?\d{4}\s?\d{4}\s?\d{2}\b/i;
const PHONE_REGEX = /(?:\+90|0)\s?[-.]?\s?\(?\d{3}\)?\s?[-.]?\s?\d{3}\s?[-.]?\s?\d{2}\s?[-.]?\s?\d{2}/;
const PHONE_GENERIC_REGEX = /\b\d{10,11}\b/;
const SOCIAL_MEDIA_REGEX = /(?:instagram|facebook|twitter|tiktok|snapchat|telegram|whatsapp|discord|youtube|twitch)[\s.:/@]*[\w.]+/i;
const SOCIAL_URL_REGEX = /(?:https?:\/\/)?(?:www\.)?(?:instagram\.com|facebook\.com|twitter\.com|x\.com|tiktok\.com|snapchat\.com|t\.me|wa\.me|discord\.gg|youtube\.com|youtu\.be|twitch\.tv)\/[\w./-]+/i;
const AT_HANDLE_REGEX = /@[\w.]{3,30}/;

function aiScanContent(text) {
  const violations = [];

  if (IBAN_REGEX.test(text) || TR_IBAN_REGEX.test(text)) {
    violations.push({
      type: "iban",
      detail: "IBAN numarası tespit edildi",
    });
  }

  if (PHONE_REGEX.test(text) || PHONE_GENERIC_REGEX.test(text)) {
    violations.push({
      type: "phone",
      detail: "Telefon numarası tespit edildi",
    });
  }

  if (SOCIAL_MEDIA_REGEX.test(text) || SOCIAL_URL_REGEX.test(text)) {
    violations.push({
      type: "social_media",
      detail: "Sosyal medya paylaşımı tespit edildi",
    });
  }

  if (AT_HANDLE_REGEX.test(text)) {
    const match = text.match(AT_HANDLE_REGEX);
    if (match && !match[0].includes("@gonulkoprusu")) {
      violations.push({
        type: "social_handle",
        detail: "Sosyal medya kullanıcı adı tespit edildi",
      });
    }
  }

  return violations;
}

function aiModerateAndLog(userId, contentType, contentId, contentText) {
  const violations = aiScanContent(contentText);
  if (violations.length === 0) return { clean: true, violations: [] };

  const logInsert = db.prepare(
    `INSERT INTO ai_moderation_logs (user_id, content_type, content_id, content_text, violation_type, violation_detail, action_taken)
     VALUES (?, ?, ?, ?, ?, ?, 'hidden')`
  );

  const warningInsert = db.prepare(
    `INSERT INTO ai_warnings (user_id, reason, warning_count)
     VALUES (?, ?, 1)`
  );

  const notifInsert = db.prepare(
    `INSERT INTO notifications (user_id, complaint_id, title, message)
     VALUES (?, NULL, ?, ?)`
  );

  const tx = db.transaction(() => {
    for (const v of violations) {
      logInsert.run(userId, contentType, contentId, contentText, v.type, v.detail);
    }

    const totalWarnings = (
      db.prepare("SELECT COUNT(*) AS c FROM ai_warnings WHERE user_id = ?").get(userId)
    ).c;

    const violationSummary = violations.map((v) => v.detail).join(", ");
    warningInsert.run(userId, violationSummary);

    notifInsert.run(
      userId,
      "AI Denetleme Uyarısı",
      `İçeriğiniz topluluk kurallarına aykırı bulundu: ${violationSummary}. ` +
      `Toplam uyarı sayınız: ${totalWarnings + 1}. ` +
      "Lütfen topluluk kurallarına uygun davranınız."
    );

    if (contentType === "message" && contentId) {
      db.prepare("UPDATE messages SET status = 'hidden' WHERE id = ?").run(contentId);
    }
  });
  tx();

  return { clean: false, violations };
}

function getUserById(id) {
  return db
    .prepare(
      `SELECT id, username, name, role, email, avatar_url, premium_plan, premium_until, gender, verified
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
      `SELECT id, username, name, role, email, avatar_url, premium_plan, premium_until, gender, verified
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
         u.gender,
         u.verified,
         COUNT(c.id) AS complaint_count,
         (SELECT COUNT(*) FROM ai_warnings w WHERE w.user_id = u.id) AS warning_count
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

// Yeni şikayet oluştur (kullanıcı) — AI moderation uygulanır
app.post("/api/complaints", (req, res) => {
  const { userId, subject, description } = req.body || {};
  const user = getUserById(Number(userId));
  if (!user) return res.status(401).json({ error: "Geçersiz kullanıcı" });
  if (!subject || !description)
    return res.status(400).json({ error: "Konu ve açıklama gerekli" });

  const fullText = `${subject} ${description}`;
  const aiResult = aiModerateAndLog(user.id, "complaint", null, fullText);
  if (!aiResult.clean) {
    return res.status(403).json({
      error: "İçeriğiniz topluluk kurallarına aykırı bulundu ve engellendi.",
      violations: aiResult.violations,
    });
  }

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

// ── AI Moderation Admin Endpoints ──

// AI content scan (test / manual scan)
app.post("/api/admin/ai/scan", (req, res) => {
  const admin = requireAdmin(res, req.body?.adminId);
  if (!admin) return;
  const text = String(req.body?.text || "");
  if (!text.trim()) return res.status(400).json({ error: "Metin gerekli" });
  const violations = aiScanContent(text);
  res.json({ clean: violations.length === 0, violations });
});

// AI moderation logs
app.get("/api/admin/ai/logs", (req, res) => {
  const admin = requireAdmin(res, req.query.adminId);
  if (!admin) return;
  const rows = db
    .prepare(
      `SELECT l.*, u.name AS user_name, u.username
       FROM ai_moderation_logs l
       JOIN users u ON u.id = l.user_id
       ORDER BY l.created_at DESC
       LIMIT 200`
    )
    .all();
  res.json(rows);
});

// AI warnings list
app.get("/api/admin/ai/warnings", (req, res) => {
  const admin = requireAdmin(res, req.query.adminId);
  if (!admin) return;
  const rows = db
    .prepare(
      `SELECT w.*, u.name AS user_name, u.username
       FROM ai_warnings w
       JOIN users u ON u.id = w.user_id
       ORDER BY w.created_at DESC
       LIMIT 200`
    )
    .all();
  res.json(rows);
});

// AI report: summary stats
app.get("/api/admin/ai/report", (req, res) => {
  const admin = requireAdmin(res, req.query.adminId);
  if (!admin) return;

  const totalScanned = db
    .prepare("SELECT COUNT(*) AS c FROM messages")
    .get().c +
    db.prepare("SELECT COUNT(*) AS c FROM complaints").get().c;
  const totalViolations = db
    .prepare("SELECT COUNT(*) AS c FROM ai_moderation_logs")
    .get().c;
  const totalWarnings = db
    .prepare("SELECT COUNT(*) AS c FROM ai_warnings")
    .get().c;
  const byType = db
    .prepare(
      `SELECT violation_type, COUNT(*) AS count
       FROM ai_moderation_logs
       GROUP BY violation_type
       ORDER BY count DESC`
    )
    .all();
  const topOffenders = db
    .prepare(
      `SELECT u.id, u.username, u.name, COUNT(w.id) AS warning_count
       FROM ai_warnings w
       JOIN users u ON u.id = w.user_id
       GROUP BY u.id
       ORDER BY warning_count DESC
       LIMIT 10`
    )
    .all();
  const recentLogs = db
    .prepare(
      `SELECT l.*, u.name AS user_name, u.username
       FROM ai_moderation_logs l
       JOIN users u ON u.id = l.user_id
       ORDER BY l.created_at DESC
       LIMIT 20`
    )
    .all();

  res.json({
    totalScanned,
    totalViolations,
    totalWarnings,
    violationsByType: byType,
    topOffenders,
    recentLogs,
  });
});

// Scan all existing messages (batch AI review)
app.post("/api/admin/ai/scan-all", (req, res) => {
  const admin = requireAdmin(res, req.body?.adminId);
  if (!admin) return;

  const messages = db.prepare("SELECT * FROM messages WHERE status != 'hidden'").all();
  let flagged = 0;
  for (const msg of messages) {
    const result = aiModerateAndLog(msg.user_id, "message", msg.id, msg.content);
    if (!result.clean) flagged++;
  }
  res.json({ scanned: messages.length, flagged });
});

// Update user gender
app.post("/api/admin/users/:id/gender", (req, res) => {
  const admin = requireAdmin(res, req.body?.adminId);
  if (!admin) return;
  const targetId = Number(req.params.id);
  const target = getUserById(targetId);
  if (!target) return res.status(404).json({ error: "Kullanıcı bulunamadı" });

  const gender = String(req.body?.gender || "unspecified").toLowerCase();
  const allowed = new Set(["male", "female", "unspecified"]);
  if (!allowed.has(gender)) {
    return res.status(400).json({ error: "Geçersiz cinsiyet değeri" });
  }

  const verified = gender === "female" ? 1 : target.verified;
  db.prepare("UPDATE users SET gender = ?, verified = ? WHERE id = ?").run(
    gender,
    verified,
    targetId
  );

  res.json(getUserById(targetId));
});

// Toggle verified status
app.post("/api/admin/users/:id/verify", (req, res) => {
  const admin = requireAdmin(res, req.body?.adminId);
  if (!admin) return;
  const targetId = Number(req.params.id);
  const target = getUserById(targetId);
  if (!target) return res.status(404).json({ error: "Kullanıcı bulunamadı" });

  const verified = req.body?.verified ? 1 : 0;
  db.prepare("UPDATE users SET verified = ? WHERE id = ?").run(verified, targetId);
  res.json(getUserById(targetId));
});

// Privacy & usage rules
app.get("/api/rules", (_req, res) => {
  res.json({
    title: "Gönül Köprüsü Topluluk Kuralları ve Gizlilik Politikası",
    lastUpdated: "2026-06-24",
    sections: [
      {
        title: "1. Genel Kurallar",
        content:
          "Gönül Köprüsü platformunda tüm kullanıcılar saygılı ve yapıcı bir dil kullanmalıdır. Nefret söylemi, hakaret ve tehdit içeren paylaşımlar yasaktır.",
      },
      {
        title: "2. AI Denetleme Sistemi",
        content:
          "Platformumuzda yapay zeka destekli otomatik içerik denetleme sistemi aktiftir. Bu sistem; mesajlarda, gönderilerde ve hikayelerde IBAN numaraları, telefon numaraları, sosyal medya hesap bilgileri ve bağlantılarını otomatik olarak tespit eder ve engeller. Kural ihlali yapan kullanıcılara otomatik uyarı bildirimi gönderilir.",
      },
      {
        title: "3. Kişisel Bilgi Paylaşım Yasağı",
        content:
          "Mesajlarda, gönderilerde ve hikayelerde IBAN numarası, banka hesap bilgisi, telefon numarası, sosyal medya kullanıcı adları veya bağlantıları paylaşmak yasaktır. Bu tür içerikler AI denetleme sistemi tarafından otomatik olarak tespit edilir ve engellenir.",
      },
      {
        title: "4. Uyarı ve Yaptırım Sistemi",
        content:
          "Kural ihlali tespit edildiğinde kullanıcıya otomatik uyarı bildirimi gönderilir. Tekrarlayan ihlallerde hesap geçici veya kalıcı olarak askıya alınabilir. Uyarı geçmişi yönetici panelinden takip edilir.",
      },
      {
        title: "5. Doğrulanmış Hesaplar (Mavi Tik)",
        content:
          "Kadın kullanıcılara güvenlik amacıyla otomatik olarak mavi tik (doğrulanmış hesap) verilir. Bu özellik platform güvenliğini artırmak için tasarlanmıştır.",
      },
      {
        title: "6. Gizlilik Politikası",
        content:
          "Kullanıcı verileri yalnızca platform hizmetleri kapsamında kullanılır. AI denetleme sistemi içerik analizini otomatik olarak gerçekleştirir; bu süreçte kişisel veriler üçüncü taraflarla paylaşılmaz. Denetleme kayıtları yalnızca yöneticiler tarafından görüntülenebilir.",
      },
      {
        title: "7. Veri Güvenliği",
        content:
          "Tüm kullanıcı verileri güvenli ortamlarda saklanır. AI denetleme logları ve uyarı geçmişi yalnızca yetkili yöneticiler tarafından erişilebilir durumdadır.",
      },
    ],
  });
});

app.listen(PORT, () => {
  console.log(`[server] Gönül Köprüsü API http://localhost:${PORT}`);
});
