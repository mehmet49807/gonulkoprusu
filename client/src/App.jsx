import { useCallback, useEffect, useState } from "react";
import { api } from "./api.js";

// "public" -> gonulkoprusu.com (kullanıcı sitesi)
// "admin"  -> admin.gonulkoprusu.com (yönetici paneli)
const APP_TARGET = import.meta.env.VITE_APP_TARGET === "admin" ? "admin" : "public";

const STATUS_LABELS = {
  open: { text: "Beklemede", cls: "badge-open" },
  resolved: { text: "Çözüldü", cls: "badge-resolved" },
  rejected: { text: "Reddedildi", cls: "badge-rejected" },
};

const PREMIUM_LABELS = {
  none: "Standart",
  pro: "Pro",
  gold: "Gold",
  platinum: "Platinum",
};

const MESSAGE_STATUS_LABELS = {
  pending: "Beklemede",
  approved: "Onaylandı",
  hidden: "Gizlendi",
};

const ADMIN_MENU_ITEMS = [
  { key: "users", label: "Kullanıcı Yönetimi", icon: "👥" },
  { key: "premium", label: "Premium Takip", icon: "⭐" },
  { key: "messages", label: "Mesaj Denetimi", icon: "💬" },
  { key: "complaints", label: "Şikayetler", icon: "📋" },
  { key: "ai", label: "AI Denetleme", icon: "🤖" },
  { key: "seo", label: "SEO Ayarları", icon: "🔍" },
];

const VIOLATION_TYPE_LABELS = {
  iban: "IBAN Numarası",
  phone: "Telefon Numarası",
  social_media: "Sosyal Medya",
  social_handle: "Sosyal Medya Hesabı",
};

const GENDER_LABELS = {
  male: "Erkek",
  female: "Kadın",
  unspecified: "Belirtilmemiş",
};

function formatDate(value) {
  if (!value) return "-";
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return value;
  return date.toLocaleString("tr-TR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
}

function profileImage(user) {
  return (
    user.avatar_url ||
    `https://api.dicebear.com/7.x/avataaars/svg?seed=${encodeURIComponent(
      user.username || user.name || "user"
    )}`
  );
}

function Login({ onLogin }) {
  const [identifier, setIdentifier] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const isAdmin = APP_TARGET === "admin";

  const STATIC_ADMIN_CREDENTIALS = [
    { login: "admin", password: "mehmet49800" },
    { login: "mehmet49800@gmail.com", password: "mehmet49800" },
  ];

  function staticAdminUser() {
    if (!isAdmin) return null;
    const login = identifier.trim().toLowerCase();
    const pass = password.trim();
    const matched = STATIC_ADMIN_CREDENTIALS.some(
      (cred) => cred.login === login && cred.password === pass
    );
    if (!matched) return null;
    return { id: 1, username: "admin", name: "Yönetici", role: "admin" };
  }

  async function submit(e) {
    e.preventDefault();
    setError("");
    const login = identifier.trim();
    if (!login) {
      setError("Kullanıcı adı veya e-posta zorunludur.");
      return;
    }
    if (isAdmin && !password.trim()) {
      setError("Şifre zorunludur.");
      return;
    }
    const fallbackUser = staticAdminUser();
    if (fallbackUser) {
      onLogin(fallbackUser);
      return;
    }
    try {
      const user = await api.login(login);
      if (isAdmin && user?.role !== "admin") {
        setError("Yönetici girişi başarısız. Bilgilerinizi kontrol edin.");
        return;
      }
      onLogin(user);
    } catch (err) {
      setError(
        isAdmin
          ? "Yönetici girişi başarısız. Bilgilerinizi kontrol edin."
          : err.message
      );
    }
  }

  if (isAdmin) {
    return (
      <div className="admin-login-body">
        <div className="admin-login-shell">
          <aside className="admin-login-visual">
            <div className="admin-login-visual-glow" aria-hidden="true" />
            <div className="admin-login-visual-glow admin-login-visual-glow--2" aria-hidden="true" />
            <a href="https://www.gonulkoprusu.com" className="admin-login-brand">
              <div className="admin-login-brand-icon">GK</div>
              <span>
                <strong>Gönül Köprüsü</strong>
                <small>Yönetim Paneli</small>
              </span>
            </a>
            <h1 className="admin-login-headline">
              Güvenli<br />yönetim alanı
            </h1>
            <p className="admin-login-desc">
              Platform moderasyonu, kullanıcı yönetimi ve duyurular için yetkili giriş noktası.
            </p>
            <ul className="admin-login-features">
              <li>
                <span className="admin-feature-icon" aria-hidden="true">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </span>
                KVKK uyumlu veri yönetimi
              </li>
              <li>
                <span className="admin-feature-icon" aria-hidden="true">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </span>
                Kullanıcı & şikayet denetimi
              </li>
              <li>
                <span className="admin-feature-icon" aria-hidden="true">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                </span>
                Toplu duyuru sistemi
              </li>
              <li>
                <span className="admin-feature-icon" aria-hidden="true">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                </span>
                Premium üyelik takibi
              </li>
            </ul>
            <div className="admin-login-visual-footer">
              <span>&copy; {new Date().getFullYear()} Gönül Köprüsü</span>
            </div>
          </aside>

          <section className="admin-login-panel">
            <div className="admin-login-card">
              <div className="admin-login-card-badge">Yetkili Erişim</div>
              <h2 className="admin-login-title">Yönetici Girişi</h2>
              <p className="admin-login-subtitle">
                Yalnızca yetkili yönetici hesapları giriş yapabilir.
              </p>

              {error && (
                <div className="admin-login-error">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                  {error}
                </div>
              )}

              <form className="admin-login-form" onSubmit={submit}>
                <div className="admin-form-group">
                  <label htmlFor="admin-login">Kullanıcı adı veya e-posta</label>
                  <div className="admin-input-wrap">
                    <svg className="admin-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <input
                      id="admin-login"
                      autoFocus
                      value={identifier}
                      onChange={(e) => setIdentifier(e.target.value)}
                      placeholder="admin"
                      required
                    />
                  </div>
                </div>
                <div className="admin-form-group">
                  <label htmlFor="admin-password">Şifre</label>
                  <div className="admin-input-wrap">
                    <svg className="admin-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input
                      id="admin-password"
                      type="password"
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                      placeholder="••••••••"
                      required
                    />
                  </div>
                </div>
                <button type="submit" className="admin-gradient-btn">
                  <span>Panele Giriş</span>
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
              </form>

              <a className="admin-login-back" href="https://www.gonulkoprusu.com">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Ana siteye dön
              </a>
            </div>
          </section>
        </div>
      </div>
    );
  }

  return (
    <div className="auth-shell">
      <div className="auth-card">
        <h1>Gönül Köprüsü</h1>
        <p className="muted">
          Topluluk yardımlaşma ve şikayet platformu
        </p>
        <form onSubmit={submit}>
          <label>Kullanıcı adı</label>
          <input
            autoFocus
            value={identifier}
            onChange={(e) => setIdentifier(e.target.value)}
            placeholder="ayse, mehmet veya admin"
          />
          <button type="submit">Giriş yap</button>
        </form>
        {error && <p className="error">{error}</p>}
        <p className="hint">
          Demo kullanıcıları: <code>ayse</code>, <code>mehmet</code> (kullanıcı)
        </p>
      </div>
    </div>
  );
}

function NotificationBell({ user }) {
  const [items, setItems] = useState([]);
  const [open, setOpen] = useState(false);

  const load = useCallback(async () => {
    setItems(await api.getNotifications(user.id));
  }, [user.id]);

  useEffect(() => {
    load();
    const t = setInterval(load, 4000);
    return () => clearInterval(t);
  }, [load]);

  const unread = items.filter((n) => !n.is_read).length;

  async function markRead(id) {
    await api.markRead(id, user.id);
    load();
  }

  return (
    <div className="bell">
      <button className="bell-btn" onClick={() => setOpen((o) => !o)}>
        🔔
        {unread > 0 && <span className="bell-count">{unread}</span>}
      </button>
      {open && (
        <div className="bell-panel">
          <h3>Bildirimler</h3>
          {items.length === 0 && <p className="muted">Henüz bildirim yok.</p>}
          {items.map((n) => (
            <div
              key={n.id}
              className={`notif ${n.is_read ? "" : "notif-unread"}`}
            >
              <strong>{n.title}</strong>
              <p>{n.message}</p>
              {!n.is_read && (
                <button className="link" onClick={() => markRead(n.id)}>
                  Okundu işaretle
                </button>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

function UserDashboard({ user }) {
  const [subject, setSubject] = useState("");
  const [description, setDescription] = useState("");
  const [complaints, setComplaints] = useState([]);
  const [msg, setMsg] = useState("");

  const load = useCallback(async () => {
    setComplaints(await api.getComplaints(user.id));
  }, [user.id]);

  useEffect(() => {
    load();
    const t = setInterval(load, 4000);
    return () => clearInterval(t);
  }, [load]);

  async function submit(e) {
    e.preventDefault();
    setMsg("");
    await api.createComplaint({ userId: user.id, subject, description });
    setSubject("");
    setDescription("");
    setMsg("Şikayetiniz alındı. Yönetici sonucu bildirim olarak gelecektir.");
    load();
  }

  return (
    <div className="grid">
      <section className="card">
        <h2>Yeni Şikayet Oluştur</h2>
        <form onSubmit={submit}>
          <label>Konu</label>
          <input
            value={subject}
            onChange={(e) => setSubject(e.target.value)}
            required
          />
          <label>Açıklama</label>
          <textarea
            rows={4}
            value={description}
            onChange={(e) => setDescription(e.target.value)}
            required
          />
          <button type="submit">Gönder</button>
        </form>
        {msg && <p className="success">{msg}</p>}
      </section>

      <section className="card">
        <h2>Şikayetlerim</h2>
        {complaints.length === 0 && (
          <p className="muted">Henüz şikayet oluşturmadınız.</p>
        )}
        {complaints.map((c) => {
          const s = STATUS_LABELS[c.status] || STATUS_LABELS.open;
          return (
            <div key={c.id} className="row">
              <div className="row-head">
                <strong>{c.subject}</strong>
                <span className={`badge ${s.cls}`}>{s.text}</span>
              </div>
              <p className="muted">{c.description}</p>
              {c.resolution && (
                <p className="resolution">
                  <strong>Yönetici sonucu:</strong> {c.resolution}
                </p>
              )}
            </div>
          );
        })}
      </section>
    </div>
  );
}

function AdminUserManagement({ adminUser }) {
  const [users, setUsers] = useState([]);
  const [premiumDrafts, setPremiumDrafts] = useState({});
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");

  const loadUsers = useCallback(async () => {
    setLoading(true);
    setError("");
    try {
      const rows = await api.getAdminUsers(adminUser.id);
      setUsers(rows);
      setPremiumDrafts(
        Object.fromEntries(rows.map((row) => [row.id, row.premium_plan || "none"]))
      );
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, [adminUser.id]);

  useEffect(() => {
    loadUsers();
  }, [loadUsers]);

  async function applyPremium(userId) {
    const plan = premiumDrafts[userId] || "none";
    await api.setUserPremium(userId, { adminId: adminUser.id, plan });
    setMessage(
      plan === "none"
        ? "Premium üyelik kaldırıldı."
        : `${PREMIUM_LABELS[plan]} üyelik atandı.`
    );
    loadUsers();
  }

  async function removeUser(userItem) {
    const ok = window.confirm(
      `${userItem.name} kullanıcısı silinsin mi? Bu işlem geri alınamaz.`
    );
    if (!ok) return;
    await api.deleteUser(userItem.id, adminUser.id);
    setMessage("Kullanıcı silindi.");
    loadUsers();
  }

  return (
    <section className="card admin-page">
      <div className="section-heading">
        <div>
          <h2>Kullanıcı Yönetimi</h2>
          <p className="muted">
            Profilleri görüntüleyin, premium atayın ve gerekli kullanıcı ayarlarını
            yönetin.
          </p>
        </div>
      </div>
      {message && <p className="success">{message}</p>}
      {error && <p className="error">{error}</p>}
      {loading && <p className="muted">Kullanıcılar yükleniyor…</p>}
      <div className="admin-list">
        {users.map((item) => (
          <article key={item.id} className="admin-entity-card">
            <div className="profile-thumb-wrap">
              <img src={profileImage(item)} alt={item.name} className="profile-thumb" />
              {item.verified === 1 && (
                <span className="verified-badge" title="Doğrulanmış Hesap">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="#3b82f6"><path d="M12 2l3.09 1.41L18.18 2l1.82 3.09L23 6.18l-1.41 3.09L23 12.36l-2.82 1.09L18.18 17l-3.09 1.41L12 22l-3.09-1.59L5.82 22l-1.82-3.55L1 17.36l1.41-3.09L1 11.18 3.82 10.09 5.82 7l3.09-1.59z"/><path d="M10 15.5l-3.5-3.5 1.41-1.41L10 12.67l5.59-5.59L17 8.5z" fill="#fff"/></svg>
                </span>
              )}
            </div>
            <div className="admin-entity-main">
              <div className="row-head">
                <strong>
                  {item.name}
                  {item.verified === 1 && <span className="verified-text"> (Mavi Tik)</span>}
                </strong>
                <span className={`badge ${item.role === "admin" ? "badge-open" : "badge-resolved"}`}>
                  {item.role === "admin" ? "Yönetici" : "Kullanıcı"}
                </span>
              </div>
              <p className="muted">
                @{item.username} · {item.email || "-"} · {GENDER_LABELS[item.gender] || "Belirtilmemiş"}
              </p>
              <p className="muted">
                Şikayet: {item.complaint_count} · Uyarı: {item.warning_count || 0} · Premium:{" "}
                <strong>{PREMIUM_LABELS[item.premium_plan] || "Standart"}</strong>
              </p>
            </div>
            {item.role !== "admin" && (
              <div className="admin-entity-actions">
                <div className="action-row">
                  <label className="action-label">Cinsiyet:</label>
                  <select
                    value={item.gender || "unspecified"}
                    onChange={async (e) => {
                      await api.setUserGender(item.id, {
                        adminId: adminUser.id,
                        gender: e.target.value,
                      });
                      loadUsers();
                    }}
                  >
                    <option value="unspecified">Belirtilmemiş</option>
                    <option value="female">Kadın</option>
                    <option value="male">Erkek</option>
                  </select>
                </div>
                <div className="action-row">
                  <label className="action-label">Premium:</label>
                  <select
                    value={premiumDrafts[item.id] || "none"}
                    onChange={(e) =>
                      setPremiumDrafts((prev) => ({
                        ...prev,
                        [item.id]: e.target.value,
                      }))
                    }
                  >
                    <option value="none">Standart</option>
                    <option value="pro">Pro</option>
                    <option value="gold">Gold</option>
                    <option value="platinum">Platinum</option>
                  </select>
                </div>
                <button type="button" onClick={() => applyPremium(item.id)}>
                  Premium uygula
                </button>
                <button
                  type="button"
                  className="secondary danger"
                  onClick={() => removeUser(item)}
                >
                  Kullanıcıyı sil
                </button>
              </div>
            )}
          </article>
        ))}
      </div>
    </section>
  );
}

function AdminPremiumTracking({ adminUser }) {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  const loadPremium = useCallback(async () => {
    setLoading(true);
    setError("");
    try {
      setUsers(await api.getPremiumUsers(adminUser.id));
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, [adminUser.id]);

  useEffect(() => {
    loadPremium();
  }, [loadPremium]);

  return (
    <section className="card admin-page">
      <div className="section-heading">
        <div>
          <h2>Premium Takip</h2>
          <p className="muted">
            Premium üyeleri ve üyelik bitiş tarihlerini bu alandan izleyin.
          </p>
        </div>
      </div>
      {error && <p className="error">{error}</p>}
      {loading && <p className="muted">Premium kullanıcılar yükleniyor…</p>}
      {users.length === 0 && !loading && (
        <p className="muted">Aktif premium kullanıcı yok.</p>
      )}
      <div className="admin-list">
        {users.map((item) => (
          <article key={item.id} className="admin-entity-card">
            <img src={profileImage(item)} alt={item.name} className="profile-thumb" />
            <div className="admin-entity-main">
              <div className="row-head">
                <strong>{item.name}</strong>
                <span className="badge badge-resolved">
                  {PREMIUM_LABELS[item.premium_plan] || "Premium"}
                </span>
              </div>
              <p className="muted">
                @{item.username} · {item.email || "-"}
              </p>
              <p className="muted">
                Bitiş tarihi: <strong>{formatDate(item.premium_until)}</strong>
              </p>
            </div>
          </article>
        ))}
      </div>
    </section>
  );
}

function AdminMessageModeration({ adminUser }) {
  const [statusFilter, setStatusFilter] = useState("all");
  const [messages, setMessages] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  const loadMessages = useCallback(async () => {
    setLoading(true);
    setError("");
    try {
      setMessages(await api.getAdminMessages(adminUser.id, statusFilter));
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, [adminUser.id, statusFilter]);

  useEffect(() => {
    loadMessages();
  }, [loadMessages]);

  async function moderate(id, action) {
    await api.moderateMessage(id, { adminId: adminUser.id, action });
    loadMessages();
  }

  return (
    <section className="card admin-page">
      <div className="section-heading">
        <div>
          <h2>Mesaj Denetimi</h2>
          <p className="muted">Kullanıcı mesajlarını onaylayın veya gizleyin.</p>
        </div>
        <select value={statusFilter} onChange={(e) => setStatusFilter(e.target.value)}>
          <option value="all">Tümü</option>
          <option value="pending">Beklemede</option>
          <option value="approved">Onaylı</option>
          <option value="hidden">Gizli</option>
        </select>
      </div>
      {error && <p className="error">{error}</p>}
      {loading && <p className="muted">Mesajlar yükleniyor…</p>}
      <div className="admin-list">
        {messages.map((item) => (
          <article key={item.id} className="admin-entity-card">
            <img
              src={profileImage(item)}
              alt={item.user_name}
              className="profile-thumb"
            />
            <div className="admin-entity-main">
              <div className="row-head">
                <strong>{item.user_name}</strong>
                <span className="badge badge-open">
                  {MESSAGE_STATUS_LABELS[item.status] || item.status}
                </span>
              </div>
              <p className="muted">
                @{item.username} · {formatDate(item.created_at)}
              </p>
              <p>{item.content}</p>
            </div>
            <div className="admin-entity-actions">
              <button type="button" onClick={() => moderate(item.id, "approve")}>
                Onayla
              </button>
              <button
                type="button"
                className="secondary"
                onClick={() => moderate(item.id, "pending")}
              >
                Beklemeye al
              </button>
              <button
                type="button"
                className="secondary danger"
                onClick={() => moderate(item.id, "hide")}
              >
                Gizle
              </button>
            </div>
          </article>
        ))}
      </div>
    </section>
  );
}

function AdminSEO() {
  const [settings, setSettings] = useState(() => {
    const saved = localStorage.getItem("gk_seo_settings");
    return saved
      ? JSON.parse(saved)
      : {
          siteTitle: "Gönül Köprüsü - Topluluk Yardımlaşma Platformu",
          siteDescription:
            "Gönül Köprüsü, topluluk yardımlaşma ve şikayet yönetim platformudur. Kullanıcılar sorunlarını bildirir, yöneticiler hızlıca çözer.",
          keywords:
            "gönül köprüsü, yardımlaşma, topluluk, şikayet, platform, destek",
          ogImage: "",
          googleAnalyticsId: "",
          googleVerification: "",
          robotsTxt:
            "User-agent: *\nAllow: /\nSitemap: https://www.gonulkoprusu.com/sitemap.xml",
        };
  });
  const [message, setMessage] = useState("");

  function update(key, value) {
    setSettings((prev) => ({ ...prev, [key]: value }));
  }

  function save() {
    localStorage.setItem("gk_seo_settings", JSON.stringify(settings));
    setMessage("SEO ayarları kaydedildi.");
    setTimeout(() => setMessage(""), 3000);
  }

  return (
    <section className="card admin-page">
      <div className="section-heading">
        <div>
          <h2>SEO & Google Ayarları</h2>
          <p className="muted">
            Arama motoru optimizasyonu ve Google entegrasyon ayarlarını yönetin.
          </p>
        </div>
      </div>
      {message && <p className="success">{message}</p>}

      <div className="seo-grid">
        <div className="seo-card">
          <div className="seo-card-header">
            <span className="seo-card-icon">📝</span>
            <h3>Meta Etiketleri</h3>
          </div>
          <label>Site Başlığı</label>
          <input
            value={settings.siteTitle}
            onChange={(e) => update("siteTitle", e.target.value)}
          />
          <label>Site Açıklaması</label>
          <textarea
            rows={3}
            value={settings.siteDescription}
            onChange={(e) => update("siteDescription", e.target.value)}
          />
          <label>Anahtar Kelimeler</label>
          <input
            value={settings.keywords}
            onChange={(e) => update("keywords", e.target.value)}
            placeholder="kelime1, kelime2, kelime3"
          />
        </div>

        <div className="seo-card">
          <div className="seo-card-header">
            <span className="seo-card-icon">🔗</span>
            <h3>Open Graph / Sosyal Medya</h3>
          </div>
          <label>OG Görsel URL</label>
          <input
            value={settings.ogImage}
            onChange={(e) => update("ogImage", e.target.value)}
            placeholder="https://www.gonulkoprusu.com/og-image.jpg"
          />
          <p className="muted">
            Sosyal medyada paylaşıldığında görünecek önizleme görseli.
          </p>
        </div>

        <div className="seo-card">
          <div className="seo-card-header">
            <span className="seo-card-icon">📊</span>
            <h3>Google Analytics</h3>
          </div>
          <label>Measurement ID</label>
          <input
            value={settings.googleAnalyticsId}
            onChange={(e) => update("googleAnalyticsId", e.target.value)}
            placeholder="G-XXXXXXXXXX"
          />
          <p className="muted">
            Google Analytics 4 ölçüm kimliğinizi girin.
          </p>
        </div>

        <div className="seo-card">
          <div className="seo-card-header">
            <span className="seo-card-icon">✅</span>
            <h3>Google Search Console</h3>
          </div>
          <label>Doğrulama Kodu</label>
          <input
            value={settings.googleVerification}
            onChange={(e) => update("googleVerification", e.target.value)}
            placeholder="google-site-verification=..."
          />
          <p className="muted">
            Search Console site doğrulama meta etiketindeki content değeri.
          </p>
        </div>

        <div className="seo-card seo-card-wide">
          <div className="seo-card-header">
            <span className="seo-card-icon">🤖</span>
            <h3>Robots.txt</h3>
          </div>
          <textarea
            className="code-editor"
            rows={6}
            value={settings.robotsTxt}
            onChange={(e) => update("robotsTxt", e.target.value)}
          />
        </div>
      </div>

      <button
        type="button"
        className="admin-gradient-btn seo-save-btn"
        onClick={save}
      >
        <span>Ayarları Kaydet</span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
      </button>
    </section>
  );
}

function AdminAI({ adminUser }) {
  const [tab, setTab] = useState("dashboard");
  const [report, setReport] = useState(null);
  const [logs, setLogs] = useState([]);
  const [warnings, setWarnings] = useState([]);
  const [scanText, setScanText] = useState("");
  const [scanResult, setScanResult] = useState(null);
  const [scanning, setScanning] = useState(false);
  const [scanAllResult, setScanAllResult] = useState(null);

  const loadReport = useCallback(async () => {
    try {
      setReport(await api.getAiReport(adminUser.id));
    } catch {
      setReport(null);
    }
  }, [adminUser.id]);

  const loadLogs = useCallback(async () => {
    try {
      setLogs(await api.getAiLogs(adminUser.id));
    } catch {
      setLogs([]);
    }
  }, [adminUser.id]);

  const loadWarnings = useCallback(async () => {
    try {
      setWarnings(await api.getAiWarnings(adminUser.id));
    } catch {
      setWarnings([]);
    }
  }, [adminUser.id]);

  useEffect(() => {
    loadReport();
    loadLogs();
    loadWarnings();
  }, [loadReport, loadLogs, loadWarnings]);

  async function handleScan() {
    if (!scanText.trim()) return;
    setScanning(true);
    setScanResult(null);
    try {
      const result = await api.aiScan({ adminId: adminUser.id, text: scanText });
      setScanResult(result);
    } catch (err) {
      setScanResult({ error: err.message });
    } finally {
      setScanning(false);
    }
  }

  async function handleScanAll() {
    setScanAllResult(null);
    try {
      const result = await api.aiScanAll({ adminId: adminUser.id });
      setScanAllResult(result);
      loadReport();
      loadLogs();
      loadWarnings();
    } catch (err) {
      setScanAllResult({ error: err.message });
    }
  }

  return (
    <section className="card admin-page">
      <div className="section-heading">
        <div>
          <h2>AI Denetleme Sistemi</h2>
          <p className="muted">
            Yapay zeka destekli otomatik içerik denetleme, uyarı ve raporlama sistemi.
          </p>
        </div>
      </div>

      <div className="ai-tabs">
        {[
          { key: "dashboard", label: "Rapor" },
          { key: "scan", label: "Manuel Tarama" },
          { key: "logs", label: "Denetleme Logları" },
          { key: "warnings", label: "Uyarılar" },
          { key: "rules", label: "Kurallar" },
        ].map((t) => (
          <button
            key={t.key}
            type="button"
            className={`ai-tab${tab === t.key ? " active" : ""}`}
            onClick={() => setTab(t.key)}
          >
            {t.label}
          </button>
        ))}
      </div>

      {tab === "dashboard" && (
        <div className="ai-dashboard">
          {report ? (
            <>
              <div className="ai-stats-grid">
                <div className="ai-stat-card">
                  <div className="ai-stat-number">{report.totalScanned}</div>
                  <div className="ai-stat-label">Toplam Taranan</div>
                </div>
                <div className="ai-stat-card ai-stat-danger">
                  <div className="ai-stat-number">{report.totalViolations}</div>
                  <div className="ai-stat-label">Toplam Ihlal</div>
                </div>
                <div className="ai-stat-card ai-stat-warning">
                  <div className="ai-stat-number">{report.totalWarnings}</div>
                  <div className="ai-stat-label">Toplam Uyarı</div>
                </div>
                <div className="ai-stat-card">
                  <div className="ai-stat-number">
                    {report.totalScanned > 0
                      ? ((report.totalViolations / report.totalScanned) * 100).toFixed(1)
                      : 0}%
                  </div>
                  <div className="ai-stat-label">Ihlal Oranı</div>
                </div>
              </div>

              {report.violationsByType.length > 0 && (
                <div className="ai-section">
                  <h3>Ihlal Tipleri</h3>
                  <div className="admin-list">
                    {report.violationsByType.map((v) => (
                      <div key={v.violation_type} className="ai-type-row">
                        <span className="ai-type-label">
                          {VIOLATION_TYPE_LABELS[v.violation_type] || v.violation_type}
                        </span>
                        <span className="badge badge-open">{v.count}</span>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              {report.topOffenders.length > 0 && (
                <div className="ai-section">
                  <h3>En Fazla Uyarı Alan Kullanıcılar</h3>
                  <div className="admin-list">
                    {report.topOffenders.map((u) => (
                      <div key={u.id} className="ai-type-row">
                        <span>{u.name} (@{u.username})</span>
                        <span className="badge badge-rejected">{u.warning_count} uyarı</span>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              <div className="ai-section">
                <h3>Son Denetleme Kayıtları</h3>
                {report.recentLogs.length === 0 && (
                  <p className="muted">Henüz denetleme kaydı yok.</p>
                )}
                <div className="admin-list">
                  {report.recentLogs.map((log) => (
                    <article key={log.id} className="admin-entity-card ai-log-card">
                      <div className="admin-entity-main">
                        <div className="row-head">
                          <strong>{log.user_name}</strong>
                          <span className="badge badge-rejected">
                            {VIOLATION_TYPE_LABELS[log.violation_type] || log.violation_type}
                          </span>
                        </div>
                        <p className="muted">
                          @{log.username} · {log.content_type} · {formatDate(log.created_at)}
                        </p>
                        <p className="ai-log-content">{log.content_text}</p>
                      </div>
                    </article>
                  ))}
                </div>
              </div>

              <button
                type="button"
                className="admin-gradient-btn"
                onClick={handleScanAll}
                style={{ marginTop: 16, maxWidth: 300 }}
              >
                <span>Tüm Mesajları Tara</span>
              </button>
              {scanAllResult && !scanAllResult.error && (
                <p className="success" style={{ marginTop: 8 }}>
                  {scanAllResult.scanned} mesaj tarandı, {scanAllResult.flagged} ihlal tespit edildi.
                </p>
              )}
              {scanAllResult?.error && (
                <p className="error" style={{ marginTop: 8 }}>{scanAllResult.error}</p>
              )}
            </>
          ) : (
            <p className="muted">Rapor yükleniyor...</p>
          )}
        </div>
      )}

      {tab === "scan" && (
        <div className="ai-scan-section">
          <h3>Manuel Içerik Tarama</h3>
          <p className="muted">
            Herhangi bir metni AI denetleme motoruyla test edin.
          </p>
          <textarea
            rows={4}
            value={scanText}
            onChange={(e) => setScanText(e.target.value)}
            placeholder="Test edilecek metni buraya yazın... (IBAN, telefon numarası, sosyal medya linki vb.)"
          />
          <button
            type="button"
            className="admin-gradient-btn"
            onClick={handleScan}
            disabled={scanning}
            style={{ marginTop: 8, maxWidth: 200 }}
          >
            <span>{scanning ? "Taranıyor..." : "Tara"}</span>
          </button>
          {scanResult && (
            <div className="ai-scan-result" style={{ marginTop: 12 }}>
              {scanResult.error ? (
                <p className="error">{scanResult.error}</p>
              ) : scanResult.clean ? (
                <div className="ai-result-clean">
                  <span className="ai-result-icon">&#10003;</span>
                  <span>Temiz - Ihlal tespit edilmedi</span>
                </div>
              ) : (
                <div className="ai-result-violations">
                  <span className="ai-result-icon danger">&#10007;</span>
                  <span>Ihlaller tespit edildi:</span>
                  <ul>
                    {scanResult.violations.map((v, i) => (
                      <li key={i}>
                        <strong>{VIOLATION_TYPE_LABELS[v.type] || v.type}:</strong> {v.detail}
                      </li>
                    ))}
                  </ul>
                </div>
              )}
            </div>
          )}
        </div>
      )}

      {tab === "logs" && (
        <div className="ai-section">
          <h3>Denetleme Logları</h3>
          {logs.length === 0 && <p className="muted">Henüz denetleme logu yok.</p>}
          <div className="admin-list">
            {logs.map((log) => (
              <article key={log.id} className="admin-entity-card ai-log-card">
                <div className="admin-entity-main">
                  <div className="row-head">
                    <strong>{log.user_name} (@{log.username})</strong>
                    <span className="badge badge-rejected">
                      {VIOLATION_TYPE_LABELS[log.violation_type] || log.violation_type}
                    </span>
                  </div>
                  <p className="muted">
                    Tip: {log.content_type} · Işlem: {log.action_taken} · {formatDate(log.created_at)}
                  </p>
                  <p className="ai-log-content">{log.content_text}</p>
                  <p className="muted">{log.violation_detail}</p>
                </div>
              </article>
            ))}
          </div>
        </div>
      )}

      {tab === "warnings" && (
        <div className="ai-section">
          <h3>Kullanıcı Uyarıları</h3>
          {warnings.length === 0 && <p className="muted">Henüz uyarı yok.</p>}
          <div className="admin-list">
            {warnings.map((w) => (
              <article key={w.id} className="admin-entity-card">
                <div className="admin-entity-main">
                  <div className="row-head">
                    <strong>{w.user_name} (@{w.username})</strong>
                    <span className="badge badge-open">Uyarı #{w.warning_count}</span>
                  </div>
                  <p className="muted">{formatDate(w.created_at)}</p>
                  <p>{w.reason}</p>
                </div>
              </article>
            ))}
          </div>
        </div>
      )}

      {tab === "rules" && <AdminRules />}
    </section>
  );
}

function AdminRules() {
  const [rules, setRules] = useState(null);

  useEffect(() => {
    api.getRules().then(setRules).catch(() => setRules(null));
  }, []);

  if (!rules) return <p className="muted">Kurallar yükleniyor...</p>;

  return (
    <div className="ai-rules">
      <h3>{rules.title}</h3>
      <p className="muted">Son güncelleme: {rules.lastUpdated}</p>
      {rules.sections.map((s, i) => (
        <div key={i} className="ai-rule-section">
          <h4>{s.title}</h4>
          <p>{s.content}</p>
        </div>
      ))}
    </div>
  );
}

function AdminDashboard({ user }) {
  const [activeMenu, setActiveMenu] = useState("users");
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [complaints, setComplaints] = useState([]);
  const [drafts, setDrafts] = useState({});

  const loadComplaints = useCallback(async () => {
    try {
      setComplaints(await api.getComplaints(user.id));
    } catch {
      setComplaints([]);
    }
  }, [user.id]);

  useEffect(() => {
    loadComplaints();
    const t = setInterval(loadComplaints, 4000);
    return () => clearInterval(t);
  }, [loadComplaints]);

  function navigate(key) {
    setActiveMenu(key);
    setMobileMenuOpen(false);
  }

  async function resolve(id, status) {
    const resolution = drafts[id];
    if (!resolution) return;
    await api.resolveComplaint(id, { adminId: user.id, status, resolution });
    setDrafts((d) => ({ ...d, [id]: "" }));
    loadComplaints();
  }

  return (
    <div className="admin-stack">
      {/* Mobile hamburger toggle */}
      <button
        type="button"
        className="mobile-hamburger"
        onClick={() => setMobileMenuOpen((o) => !o)}
        aria-label="Menüyü aç"
      >
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>

      {/* Mobile overlay */}
      {mobileMenuOpen && (
        <div
          className="mobile-menu-overlay"
          onClick={() => setMobileMenuOpen(false)}
        />
      )}

      {/* Mobile slide-out drawer (right side) */}
      <aside className={`mobile-menu-drawer${mobileMenuOpen ? " open" : ""}`}>
        <div className="mobile-menu-header">
          <span className="mobile-menu-title">Menü</span>
          <button
            type="button"
            className="mobile-menu-close"
            onClick={() => setMobileMenuOpen(false)}
            aria-label="Menüyü kapat"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        {ADMIN_MENU_ITEMS.map((item) => (
          <button
            key={item.key}
            type="button"
            className={`mobile-menu-item${activeMenu === item.key ? " active" : ""}`}
            onClick={() => navigate(item.key)}
          >
            <span className="menu-icon" aria-hidden="true">{item.icon}</span>
            <span>{item.label}</span>
          </button>
        ))}
      </aside>

      {/* Desktop horizontal menu */}
      <section className="admin-menu card">
        {ADMIN_MENU_ITEMS.map((item) => (
          <button
            key={item.key}
            type="button"
            className={activeMenu === item.key ? "active" : ""}
            onClick={() => setActiveMenu(item.key)}
          >
            <span className="menu-icon" aria-hidden="true">{item.icon}</span>
            <span>{item.label}</span>
          </button>
        ))}
      </section>

      {activeMenu === "users" ? (
        <AdminUserManagement adminUser={user} />
      ) : activeMenu === "premium" ? (
        <AdminPremiumTracking adminUser={user} />
      ) : activeMenu === "messages" ? (
        <AdminMessageModeration adminUser={user} />
      ) : activeMenu === "ai" ? (
        <AdminAI adminUser={user} />
      ) : activeMenu === "seo" ? (
        <AdminSEO />
      ) : (
        <section className="card admin-page">
          <div className="section-heading">
            <div>
              <h2>Şikayetler</h2>
              <p className="muted">
                Kullanıcı şikayetlerini inceleyin, çözümleyin veya reddedin.
              </p>
            </div>
          </div>
          {complaints.length === 0 && <p className="muted">Şikayet yok.</p>}
          <div className="admin-list">
            {complaints.map((c) => {
              const s = STATUS_LABELS[c.status] || STATUS_LABELS.open;
              return (
                <article key={c.id} className="admin-entity-card">
                  <img
                    src={profileImage({ username: c.user_name })}
                    alt={c.user_name}
                    className="profile-thumb"
                  />
                  <div className="admin-entity-main">
                    <div className="row-head">
                      <strong>{c.subject}</strong>
                      <span className={`badge ${s.cls}`}>{s.text}</span>
                    </div>
                    <p className="muted">
                      {c.user_name} · {formatDate(c.created_at)}
                    </p>
                    <p>{c.description}</p>
                    {c.resolution && (
                      <p className="resolution">
                        <strong>Sonuç:</strong> {c.resolution}
                      </p>
                    )}
                  </div>
                  {c.status === "open" && (
                    <div className="admin-entity-actions">
                      <textarea
                        rows={2}
                        placeholder="Sonuç açıklaması…"
                        value={drafts[c.id] || ""}
                        onChange={(e) =>
                          setDrafts((d) => ({ ...d, [c.id]: e.target.value }))
                        }
                      />
                      <button
                        type="button"
                        onClick={() => resolve(c.id, "resolved")}
                      >
                        Çözüldü
                      </button>
                      <button
                        type="button"
                        className="secondary danger"
                        onClick={() => resolve(c.id, "rejected")}
                      >
                        Reddet
                      </button>
                    </div>
                  )}
                </article>
              );
            })}
          </div>
        </section>
      )}
    </div>
  );
}

export default function App() {
  const [user, setUser] = useState(() => {
    const raw = localStorage.getItem("gk_user");
    if (!raw) return null;
    const savedUser = JSON.parse(raw);
    if (APP_TARGET === "admin" && savedUser?.role !== "admin") {
      localStorage.removeItem("gk_user");
      return null;
    }
    return savedUser;
  });

  function login(u) {
    localStorage.setItem("gk_user", JSON.stringify(u));
    setUser(u);
  }
  function logout() {
    localStorage.removeItem("gk_user");
    setUser(null);
  }

  if (!user) return <Login onLogin={login} />;

  const isAdminApp = APP_TARGET === "admin";

  let body;
  if (isAdminApp) {
    body =
      user.role === "admin" ? (
        <AdminDashboard user={user} />
      ) : (
        <section className="card">
          <h2>Yetkisiz erişim</h2>
          <p className="muted">
            Bu panel yalnızca yöneticilere açıktır. Kullanıcı işlemleri için
            ana siteyi kullanın.
          </p>
        </section>
      );
  } else {
    body =
      user.role === "admin" ? (
        <section className="card">
          <h2>Yönetici hesabı</h2>
          <p className="muted">
            Yönetici paneli için <code>admin.gonulkoprusu.com</code> adresini
            kullanın.
          </p>
        </section>
      ) : (
        <UserDashboard user={user} />
      );
  }

  return (
    <div className="app">
      <header className="topbar">
        <div className="brand">
          Gönül Köprüsü{isAdminApp ? " — Yönetici" : ""}
        </div>
        <div className="topbar-right">
          {!isAdminApp && user.role !== "admin" && (
            <NotificationBell user={user} />
          )}
          <span className="who">
            {user.name} {user.role === "admin" ? "(Yönetici)" : ""}
          </span>
          <button className="link" onClick={logout}>
            Çıkış
          </button>
        </div>
      </header>
      <main>{body}</main>
    </div>
  );
}
