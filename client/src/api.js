// Deploy ortamlarında API farklı bir adreste olabilir; build sırasında
// VITE_API_BASE ile ayarlanır. Geliştirmede boş bırakılır (Vite proxy /api -> :4000).
const API_BASE = (import.meta.env.VITE_API_BASE || "").replace(/\/$/, "");

async function request(path, options = {}) {
  const res = await fetch(`${API_BASE}/api${path}`, {
    headers: { "Content-Type": "application/json" },
    ...options,
  });
  const data = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error(data.error || "İstek başarısız");
  return data;
}

export const api = {
  login: (username) =>
    request("/login", { method: "POST", body: JSON.stringify({ username }) }),
  getComplaints: (userId) => request(`/complaints?userId=${userId}`),
  createComplaint: (payload) =>
    request("/complaints", { method: "POST", body: JSON.stringify(payload) }),
  resolveComplaint: (id, payload) =>
    request(`/complaints/${id}/resolve`, {
      method: "POST",
      body: JSON.stringify(payload),
    }),
  getNotifications: (userId) => request(`/notifications?userId=${userId}`),
  markRead: (id, userId) =>
    request(`/notifications/${id}/read`, {
      method: "POST",
      body: JSON.stringify({ userId }),
    }),
  getAdminUsers: (adminId) => request(`/admin/users?adminId=${adminId}`),
  setUserPremium: (id, payload) =>
    request(`/admin/users/${id}/premium`, {
      method: "POST",
      body: JSON.stringify(payload),
    }),
  deleteUser: (id, adminId) =>
    request(`/admin/users/${id}?adminId=${adminId}`, { method: "DELETE" }),
  getPremiumUsers: (adminId) => request(`/admin/premium-users?adminId=${adminId}`),
  getAdminMessages: (adminId, status = "all") =>
    request(`/admin/messages?adminId=${adminId}&status=${status}`),
  moderateMessage: (id, payload) =>
    request(`/admin/messages/${id}/moderate`, {
      method: "POST",
      body: JSON.stringify(payload),
    }),
  // AI moderation
  aiScan: (payload) =>
    request("/admin/ai/scan", { method: "POST", body: JSON.stringify(payload) }),
  aiScanAll: (payload) =>
    request("/admin/ai/scan-all", { method: "POST", body: JSON.stringify(payload) }),
  getAiLogs: (adminId) => request(`/admin/ai/logs?adminId=${adminId}`),
  getAiWarnings: (adminId) => request(`/admin/ai/warnings?adminId=${adminId}`),
  getAiReport: (adminId) => request(`/admin/ai/report?adminId=${adminId}`),
  // User gender / verified
  setUserGender: (id, payload) =>
    request(`/admin/users/${id}/gender`, { method: "POST", body: JSON.stringify(payload) }),
  setUserVerified: (id, payload) =>
    request(`/admin/users/${id}/verify`, { method: "POST", body: JSON.stringify(payload) }),
  // Rules
  getRules: () => request("/rules"),
};
