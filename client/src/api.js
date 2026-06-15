async function request(path, options = {}) {
  const res = await fetch(`/api${path}`, {
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
};
