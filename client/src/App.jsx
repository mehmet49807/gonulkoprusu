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

function Login({ onLogin }) {
  const [username, setUsername] = useState("");
  const [error, setError] = useState("");

  async function submit(e) {
    e.preventDefault();
    setError("");
    try {
      const user = await api.login(username);
      onLogin(user);
    } catch (err) {
      setError(err.message);
    }
  }

  const isAdmin = APP_TARGET === "admin";
  return (
    <div className="auth-card">
      <h1>Gönül Köprüsü{isAdmin ? " — Yönetici" : ""}</h1>
      <p className="muted">
        {isAdmin
          ? "Yönetici paneli girişi"
          : "Topluluk yardımlaşma ve şikayet platformu"}
      </p>
      <form onSubmit={submit}>
        <label>Kullanıcı adı</label>
        <input
          autoFocus
          value={username}
          onChange={(e) => setUsername(e.target.value)}
          placeholder={isAdmin ? "admin" : "ayse, mehmet veya admin"}
        />
        <button type="submit">Giriş yap</button>
      </form>
      {error && <p className="error">{error}</p>}
      <p className="hint">
        {isAdmin ? (
          <>
            Yönetici hesabı ile giriş yapın (örn. <code>admin</code>).
          </>
        ) : (
          <>
            Demo kullanıcıları: <code>ayse</code>, <code>mehmet</code> (kullanıcı)
          </>
        )}
      </p>
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

function AdminDashboard({ user }) {
  const [complaints, setComplaints] = useState([]);
  const [drafts, setDrafts] = useState({});

  const load = useCallback(async () => {
    setComplaints(await api.getComplaints(user.id));
  }, [user.id]);

  useEffect(() => {
    load();
    const t = setInterval(load, 4000);
    return () => clearInterval(t);
  }, [load]);

  async function resolve(id, status) {
    const resolution = drafts[id];
    if (!resolution) return;
    await api.resolveComplaint(id, { adminId: user.id, status, resolution });
    setDrafts((d) => ({ ...d, [id]: "" }));
    load();
  }

  return (
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
  );
}

export default function App() {
  const [user, setUser] = useState(() => {
    const raw = localStorage.getItem("gk_user");
    return raw ? JSON.parse(raw) : null;
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
