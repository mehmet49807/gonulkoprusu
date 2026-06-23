import { useEffect, useMemo, useRef, useState } from "react";
import { AI_MODELS, sendChatMessage } from "./aiService.js";

const STORAGE_KEY = "gk_admin_ai_chat_v1";

function loadStoredChat() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return [];
    const parsed = JSON.parse(raw);
    return Array.isArray(parsed) ? parsed : [];
  } catch {
    return [];
  }
}

function saveStoredChat(messages) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(messages));
}

export default function AdminAiChat() {
  const [messages, setMessages] = useState(loadStoredChat);
  const [input, setInput] = useState("");
  const [model, setModel] = useState(AI_MODELS[0].id);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const bottomRef = useRef(null);
  const abortRef = useRef(null);

  const activeModel = useMemo(
    () => AI_MODELS.find((item) => item.id === model) || AI_MODELS[0],
    [model]
  );

  useEffect(() => {
    saveStoredChat(messages);
  }, [messages]);

  useEffect(() => {
    bottomRef.current?.scrollIntoView({ behavior: "smooth" });
  }, [messages, loading]);

  useEffect(() => {
    return () => abortRef.current?.abort();
  }, []);

  async function handleSubmit(event) {
    event.preventDefault();
    const text = input.trim();
    if (!text || loading) return;

    const userMessage = { role: "user", content: text };
    const nextMessages = [...messages, userMessage];
    setMessages(nextMessages);
    setInput("");
    setError("");
    setLoading(true);

    const controller = new AbortController();
    abortRef.current = controller;

    try {
      const reply = await sendChatMessage({
        messages: nextMessages,
        model,
        signal: controller.signal,
      });
      setMessages((current) => [
        ...current,
        { role: "assistant", content: reply },
      ]);
    } catch (err) {
      if (err.name !== "AbortError") {
        setError(err.message || "Yapay zeka yanıtı alınamadı.");
      }
    } finally {
      setLoading(false);
      abortRef.current = null;
    }
  }

  function clearChat() {
    if (loading) abortRef.current?.abort();
    setMessages([]);
    setError("");
    setInput("");
    localStorage.removeItem(STORAGE_KEY);
  }

  return (
    <section className="card ai-chat">
      <div className="section-heading">
        <div>
          <h2>Yapay Zeka Asistanı</h2>
          <p className="muted">
            Ücretsiz Pollinations AI ile doğrudan sohbet edin. API anahtarı
            gerekmez; yanıtlar anında üretilir.
          </p>
        </div>
        <span className="badge badge-open">Ücretsiz</span>
      </div>

      <div className="ai-chat-toolbar">
        <label className="ai-model-picker">
          <span>Model</span>
          <select value={model} onChange={(e) => setModel(e.target.value)}>
            {AI_MODELS.map((item) => (
              <option key={item.id} value={item.id}>
                {item.label}
              </option>
            ))}
          </select>
        </label>
        <p className="muted ai-model-note">{activeModel.description}</p>
        <button
          type="button"
          className="secondary ai-clear-btn"
          onClick={clearChat}
          disabled={messages.length === 0 && !input}
        >
          Sohbeti temizle
        </button>
      </div>

      <div className="ai-chat-log" aria-live="polite">
        {messages.length === 0 && !loading && (
          <div className="ai-chat-empty">
            <strong>Merhaba!</strong>
            <p>
              Kod yazma, metin düzenleme, planlama veya sorun giderme için
              mesajınızı yazın.
            </p>
            <div className="ai-suggestions">
              {[
                "Yönetici paneline yeni bir menü nasıl eklerim?",
                "Kullanıcı şikayet e-postası taslağı yaz.",
                "React bileşeninde performans sorunlarını kontrol et.",
              ].map((suggestion) => (
                <button
                  key={suggestion}
                  type="button"
                  className="ai-suggestion"
                  onClick={() => setInput(suggestion)}
                >
                  {suggestion}
                </button>
              ))}
            </div>
          </div>
        )}

        {messages.map((message, index) => (
          <div
            key={`${message.role}-${index}`}
            className={`ai-message ai-message-${message.role}`}
          >
            <div className="ai-message-meta">
              {message.role === "user" ? "Siz" : "Yapay Zeka"}
            </div>
            <div className="ai-message-body">{message.content}</div>
          </div>
        ))}

        {loading && (
          <div className="ai-message ai-message-assistant">
            <div className="ai-message-meta">Yapay Zeka</div>
            <div className="ai-message-body ai-typing">Yanıt hazırlanıyor…</div>
          </div>
        )}

        <div ref={bottomRef} />
      </div>

      <form className="ai-chat-form" onSubmit={handleSubmit}>
        <label htmlFor="ai-input">Mesajınız</label>
        <textarea
          id="ai-input"
          rows={4}
          value={input}
          onChange={(e) => setInput(e.target.value)}
          placeholder="Yapay zekaya bir soru sorun veya görev verin…"
          disabled={loading}
        />
        <div className="actions wrap">
          <button type="submit" disabled={loading || !input.trim()}>
            {loading ? "Gönderiliyor…" : "Gönder"}
          </button>
        </div>
      </form>

      {error && <p className="error">{error}</p>}
      <p className="hint">
        Bu sayfa <code>/ai</code> adresinde çalışır. Ücretsiz kullanımda kısa
        süreli istek sınırı olabilir; yoğun kullanımda birkaç saniye bekleyin.
      </p>
    </section>
  );
}
