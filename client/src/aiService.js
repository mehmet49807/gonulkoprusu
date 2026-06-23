const POLLINATIONS_CHAT_URL = "https://text.pollinations.ai/openai";
const REFERRER = "admin.gonulkoprusu.com";

export const AI_MODELS = [
  {
    id: "openai",
    label: "GPT-OSS (Ücretsiz)",
    description: "Hızlı ve dengeli genel amaçlı model",
  },
  {
    id: "mistral",
    label: "Mistral (Ücretsiz)",
    description: "Kısa ve net yanıtlar için",
  },
];

const SYSTEM_PROMPT =
  "Sen Gönül Köprüsü yönetici panelinin yapay zeka asistanısın. " +
  "Türkçe, net ve uygulanabilir yanıtlar ver. Kod, içerik, planlama ve " +
  "sorun giderme konularında yardımcı ol.";

function extractAssistantText(payload) {
  if (typeof payload === "string") return payload.trim();
  const content = payload?.choices?.[0]?.message?.content;
  if (typeof content === "string") return content.trim();
  if (Array.isArray(content)) {
    return content
      .map((part) => (typeof part === "string" ? part : part?.text || ""))
      .join("")
      .trim();
  }
  return "";
}

export async function sendChatMessage({ messages, model = "openai", signal }) {
  const conversation = [
    { role: "system", content: SYSTEM_PROMPT },
    ...messages.filter((message) => message.role !== "system"),
  ];

  const response = await fetch(POLLINATIONS_CHAT_URL, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Referer: `https://${REFERRER}/ai`,
    },
    body: JSON.stringify({
      model,
      messages: conversation,
      temperature: 0.7,
      max_tokens: 1200,
      stream: false,
    }),
    signal,
  });

  if (!response.ok) {
    const errorText = await response.text().catch(() => "");
    if (response.status === 429) {
      throw new Error(
        "Çok sık istek gönderildi. Lütfen birkaç saniye bekleyip tekrar deneyin."
      );
    }
    throw new Error(
      errorText || `Yapay zeka servisi yanıt vermedi (${response.status}).`
    );
  }

  const payload = await response.json();
  const text = extractAssistantText(payload);
  if (!text) {
    throw new Error("Yapay zekadan boş yanıt alındı. Lütfen tekrar deneyin.");
  }

  return text;
}
