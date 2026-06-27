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

const GITHUB_REPOSITORY_URL = "https://github.com/mehmet49807/gonulkoprusu";

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
      <div className="auth-shell auth-shell-admin">
        <div className="auth-card auth-card-admin">
          <h1>Yönetici Girişi</h1>
          <p className="muted auth-subtitle">
            Yalnızca yetkili yönetici hesapları giriş yapabilir.
          </p>
          <form className="admin-login-form" onSubmit={submit}>
            <label>Kullanıcı adı veya e-posta</label>
            <input
              autoFocus
              value={identifier}
              onChange={(e) => setIdentifier(e.target.value)}
              placeholder="admin"
              required
            />
            <label>Şifre</label>
            <input
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder="••••••••"
              required
            />
            <button type="submit" className="admin-gradient-btn">
              Panele Giriş
            </button>
          </form>
          {error && <p className="error">{error}</p>}
          <a className="back-link" href="https://gonulkoprusu.com">
            ← Ana siteye dön
          </a>
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
            <img src={profileImage(item)} alt={item.name} className="profile-thumb" />
            <div className="admin-entity-main">
              <div className="row-head">
                <strong>{item.name}</strong>
                <span className={`badge ${item.role === "admin" ? "badge-open" : "badge-resolved"}`}>
                  {item.role === "admin" ? "Yönetici" : "Kullanıcı"}
                </span>
              </div>
              <p className="muted">
                @{item.username} · {item.email || "-"}
              </p>
              <p className="muted">
                Şikayet sayısı: {item.complaint_count} · Premium:{" "}
                <strong>{PREMIUM_LABELS[item.premium_plan] || "Standart"}</strong>
              </p>
            </div>
            {item.role !== "admin" && (
              <div className="admin-entity-actions">
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
      <h2>Premium Takip</h2>
      <p className="muted">
        Premium üyeleri ve üyelik bitiş tarihlerini bu alandan izleyin.
      </p>
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

function AdminDashboard({ user }) {
  const [activeMenu, setActiveMenu] = useState("users");
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

  async function resolve(id, status) {
    const resolution = drafts[id];
    if (!resolution) return;
    await api.resolveComplaint(id, { adminId: user.id, status, resolution });
    setDrafts((d) => ({ ...d, [id]: "" }));
    loadComplaints();
  }

  return (
    <div className="admin-stack">
      <section className="admin-menu card">
        <button
          type="button"
          className={activeMenu === "users" ? "active" : ""}
          onClick={() => setActiveMenu("users")}
        >
          <span className="menu-icon" aria-hidden="true">
            👥
          </span>
          <span>Kullanıcı Yönetimi</span>
        </button>
        <button
          type="button"
          className={activeMenu === "premium" ? "active" : ""}
          onClick={() => setActiveMenu("premium")}
        >
          <span className="menu-icon" aria-hidden="true">
            ⭐
          </span>
          <span>Premium Takip</span>
        </button>
        <button
          type="button"
          className={activeMenu === "messages" ? "active" : ""}
          onClick={() => setActiveMenu("messages")}
        >
          <span className="menu-icon" aria-hidden="true">
            💬
          </span>
          <span>Mesaj Denetimi</span>
        </button>
        <button
          type="button"
          className={activeMenu === "complaints" ? "active" : ""}
          onClick={() => setActiveMenu("complaints")}
        >
          <span className="menu-icon" aria-hidden="true">
            📋
          </span>
          <span>Şikayetler</span>
        </button>
        <button
          type="button"
          className={activeMenu === "github" ? "active" : ""}
          onClick={() => setActiveMenu("github")}
        >
          <span className="menu-icon" aria-hidden="true">
            GH
          </span>
          <span>GitHub</span>
        </button>
      </section>

      {activeMenu === "users" ? (
        <AdminUserManagement adminUser={user} />
      ) : activeMenu === "premium" ? (
        <AdminPremiumTracking adminUser={user} />
      ) : activeMenu === "messages" ? (
        <AdminMessageModeration adminUser={user} />
      ) : activeMenu === "github" ? (
        <section className="card admin-page github-panel">
          <div className="section-heading">
            <div>
              <h2>GitHub Bağlantısı</h2>
              <p className="muted">
                Proje kodlarını, deploy ayarlarını ve README dosyasını GitHub
                reposunda takip edin.
              </p>
            </div>
          </div>
          <a
            className="github-link-card"
            href={GITHUB_REPOSITORY_URL}
            target="_blank"
            rel="noreferrer"
          >
            <span>
              <strong>Gönül Köprüsü GitHub Reposu</strong>
              <small>{GITHUB_REPOSITORY_URL}</small>
            </span>
            <span aria-hidden="true">↗</span>
          </a>
        </section>
      ) : (
        <section className="card">
          <h2>Yönetici Paneli — Tüm Şikayetler</h2>
          {complaints.length === 0 && <p className="muted">Şikayet yok.</p>}
          {complaints.map((c) => {
            const s = STATUS_LABELS[c.status] || STATUS_LABELS.open;
            return (
              <div key={c.id} className="row">
                <div className="row-head">
                  <strong>{c.subject}</strong>
                  <span className={`badge ${s.cls}`}>{s.text}</span>
                </div>
                <p className="muted">
                  {c.user_name}: {c.description}
                </p>
                {c.status === "open" ? (
                  <div className="resolve-box">
                    <textarea
                      rows={2}
                      placeholder="Sonuç açıklaması (kullanıcıya bildirim olarak gider)"
                      value={drafts[c.id] || ""}
                      onChange={(e) =>
                        setDrafts((d) => ({ ...d, [c.id]: e.target.value }))
                      }
                    />
                    <div className="actions">
                      <button onClick={() => resolve(c.id, "resolved")}>
                        Çözüldü olarak bildir
                      </button>
                      <button
                        className="secondary"
                        onClick={() => resolve(c.id, "rejected")}
                      >
                        Reddet
                      </button>
                    </div>
                  </div>
                ) : (
                  <p className="resolution">
                    <strong>Sonuç:</strong> {c.resolution}
                  </p>
                )}
              </div>
            );
          })}
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
