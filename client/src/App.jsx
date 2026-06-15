import { useCallback, useEffect, useMemo, useState } from "react";
import { api } from "./api.js";

// "public" -> gonulkoprusu.com (kullanıcı sitesi)
// "admin"  -> admin.gonulkoprusu.com (yönetici paneli)
const APP_TARGET = import.meta.env.VITE_APP_TARGET === "admin" ? "admin" : "public";

const STATUS_LABELS = {
  open: { text: "Beklemede", cls: "badge-open" },
  resolved: { text: "Çözüldü", cls: "badge-resolved" },
  rejected: { text: "Reddedildi", cls: "badge-rejected" },
};

const AI_TOOLS = [
  {
    id: "agent",
    name: "Agent / Composer",
    summary: "Kod yazdırma ve çok dosyalı değişiklik planı",
    instruction:
      "Aşağıdaki hedefe göre üretim kalitesinde kod değişikliği öner. Gerekirse dosya dosya uygulanacak net adımlar ver.",
  },
  {
    id: "ask",
    name: "Ask",
    summary: "Kod açıklama ve hızlı soru-cevap",
    instruction:
      "Aşağıdaki kodu sade Türkçe ile açıkla. Riskli veya kafa karıştıran bölümleri ayrıca belirt.",
  },
  {
    id: "plan",
    name: "Plan",
    summary: "Uygulama planı ve mimari kararlar",
    instruction:
      "Bu hedef için uygulanabilir teknik plan hazırla. Etkilenecek dosyaları, sıralamayı ve riskleri belirt.",
  },
  {
    id: "debug",
    name: "Debug",
    summary: "Hata nedeni bulma ve çözüm adımları",
    instruction:
      "Aşağıdaki kod/hedef için olası hata kaynaklarını analiz et. Kanıt, kök neden ve düzeltme adımlarını yaz.",
  },
  {
    id: "bugbot",
    name: "Bugbot",
    summary: "Kod inceleme, regresyon ve eksik test bulma",
    instruction:
      "Kod incelemesi yap. Önce gerçek bug, regresyon, güvenlik ve test eksiklerini önem sırasıyla listele.",
  },
  {
    id: "security",
    name: "Security Review",
    summary: "Güvenlik açığı ve gizli bilgi kontrolü",
    instruction:
      "Güvenlik incelemesi yap. Yetki, gizli bilgi, veri sızıntısı, XSS/CSRF ve dağıtım risklerini kontrol et.",
  },
  {
    id: "tests",
    name: "Test Generator",
    summary: "Manuel ve otomatik test senaryoları",
    instruction:
      "Bu değişiklik için test planı oluştur. Kritik kullanıcı akışları, edge case'ler ve komutları ekle.",
  },
  {
    id: "pr",
    name: "PR Writer",
    summary: "Pull request özeti ve kontrol listesi",
    instruction:
      "Bu değişiklik için kısa PR açıklaması yaz. Summary, Testing ve risk notlarını Markdown olarak hazırla.",
  },
];

const DEFAULT_CODE_SNIPPET = `// Buraya düzenlemek istediğiniz kodu yapıştırın.
// Araç seçin, hedefinizi yazın ve Cursor/AI prompt'unu kopyalayın.

function example() {
  return "Gönül Köprüsü";
}
`;

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

function AdminAiTools() {
  const [activeToolId, setActiveToolId] = useState(AI_TOOLS[0].id);
  const [fileName, setFileName] = useState("client/src/App.jsx");
  const [language, setLanguage] = useState("jsx");
  const [goal, setGoal] = useState(
    "Yönetici panelinde kullanıcıların işini kolaylaştıracak değişikliği uygula."
  );
  const [code, setCode] = useState(DEFAULT_CODE_SNIPPET);
  const [message, setMessage] = useState("");

  const activeTool = useMemo(
    () => AI_TOOLS.find((tool) => tool.id === activeToolId) || AI_TOOLS[0],
    [activeToolId]
  );

  const generatedPrompt = useMemo(
    () =>
      [
        `Araç: ${activeTool.name}`,
        "",
        activeTool.instruction,
        "",
        `Hedef: ${goal}`,
        `Dosya: ${fileName || "belirtilmedi"}`,
        `Dil: ${language || "belirtilmedi"}`,
        "",
        "Kod:",
        "```" + (language || ""),
        code,
        "```",
      ].join("\n"),
    [activeTool, code, fileName, goal, language]
  );

  async function copyToClipboard(text, label) {
    try {
      await navigator.clipboard.writeText(text);
      setMessage(`${label} panoya kopyalandı.`);
    } catch {
      setMessage("Tarayıcı panoya kopyalamayı engelledi. Metni elle seçip kopyalayın.");
    }
  }

  function downloadCode() {
    const blob = new Blob([code], { type: "text/plain;charset=utf-8" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = fileName.split("/").pop() || "cursor-code.txt";
    link.click();
    URL.revokeObjectURL(url);
    setMessage("Kod dosyası indirildi.");
  }

  return (
    <section className="card ai-tools">
      <div className="section-heading">
        <div>
          <h2>Cursor AI Araçları</h2>
          <p className="muted">
            Kod yazma, inceleme, hata ayıklama ve PR hazırlama için hazır
            Cursor/AI promptları oluşturun.
          </p>
        </div>
        <span className="badge badge-open">Yönetici</span>
      </div>

      <div className="tool-grid">
        {AI_TOOLS.map((tool) => (
          <button
            key={tool.id}
            type="button"
            className={`tool-card ${activeTool.id === tool.id ? "active" : ""}`}
            onClick={() => setActiveToolId(tool.id)}
          >
            <strong>{tool.name}</strong>
            <span>{tool.summary}</span>
          </button>
        ))}
      </div>

      <div className="editor-layout">
        <div>
          <label>Hedef / Talimat</label>
          <textarea
            rows={4}
            value={goal}
            onChange={(e) => setGoal(e.target.value)}
          />

          <div className="field-row">
            <div>
              <label>Dosya adı</label>
              <input value={fileName} onChange={(e) => setFileName(e.target.value)} />
            </div>
            <div>
              <label>Dil</label>
              <select value={language} onChange={(e) => setLanguage(e.target.value)}>
                <option value="jsx">React / JSX</option>
                <option value="js">JavaScript</option>
                <option value="css">CSS</option>
                <option value="json">JSON</option>
                <option value="md">Markdown</option>
                <option value="sql">SQL</option>
                <option value="">Diğer</option>
              </select>
            </div>
          </div>

          <label>Kod editörü</label>
          <textarea
            className="code-editor"
            rows={14}
            spellCheck="false"
            value={code}
            onChange={(e) => setCode(e.target.value)}
          />
        </div>

        <div>
          <label>Oluşturulan Cursor/AI promptu</label>
          <textarea
            className="prompt-preview"
            rows={21}
            readOnly
            value={generatedPrompt}
          />
          <div className="actions wrap">
            <button
              type="button"
              onClick={() => copyToClipboard(generatedPrompt, "Prompt")}
            >
              Promptu kopyala
            </button>
            <button
              type="button"
              className="secondary"
              onClick={() => copyToClipboard(code, "Kod")}
            >
              Kodu kopyala
            </button>
            <button type="button" className="secondary" onClick={downloadCode}>
              Kodu indir
            </button>
          </div>
          {message && <p className="success">{message}</p>}
        </div>
      </div>
    </section>
  );
}

function AdminDashboard({ user }) {
  const [activeMenu, setActiveMenu] = useState("complaints");
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
    <div className="admin-stack">
      <section className="admin-menu card">
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
          className={activeMenu === "ai" ? "active" : ""}
          onClick={() => setActiveMenu("ai")}
        >
          <span className="menu-icon" aria-hidden="true">
            🤖
          </span>
          <span>Yapay Zeka Araçları</span>
        </button>
      </section>

      {activeMenu === "complaints" ? (
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
      ) : (
        <AdminAiTools />
      )}
    </div>
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
