// Point this at your running FastAPI backend
const API_BASE_URL = "http://localhost:8000";

function getToken() {
  return localStorage.getItem("dormhub_token");
}

function setToken(token) {
  localStorage.setItem("dormhub_token", token);
}

function clearToken() {
  localStorage.removeItem("dormhub_token");
}

// Generic request helper — this is done, reuse it for every endpoint below.
async function apiRequest(path, { method = "GET", body, auth = false } = {}) {
  const headers = { "Content-Type": "application/json" };
  if (auth) {
    const token = getToken();
    if (token) headers["Authorization"] = `Bearer ${token}`;
  }

  const response = await fetch(`${API_BASE_URL}${path}`, {
    method,
    headers,
    body: body ? JSON.stringify(body) : undefined,
  });

  if (!response.ok) {
    const errorBody = await response.json().catch(() => ({}));
    throw new Error(errorBody.detail || `Request failed: ${response.status}`);
  }

  if (response.status === 204) return null;
  return response.json();
}

// ---- Auth ----
// TODO: implement once backend /auth/register and /auth/login are done.
async function register(fullName, email, password) {
  throw new Error("TODO: call POST /auth/register via apiRequest()");
}

async function login(email, password) {
  // Note: FastAPI's OAuth2PasswordRequestForm expects form-encoded data
  // (not JSON), with the email in a "username" field. This one can't use
  // apiRequest() as-is — build the form request directly. See the backend
  // route in app/routes/auth.py for the expected shape.
  throw new Error("TODO: build the form-encoded POST /auth/login request");
}

async function getCurrentUser() {
  return apiRequest("/auth/me", { auth: true });
}

// ---- Households ----
// TODO: implement once backend /households routes are done.
async function createHousehold(name) {
  throw new Error("TODO: call POST /households via apiRequest()");
}

async function getMyHouseholds() {
  throw new Error("TODO: call GET /households/me via apiRequest()");
}

// ---- Invites ----
// TODO: implement once backend /invites routes are done.
async function createInvite(householdId) {
  throw new Error("TODO: call POST /invites/households/{householdId} via apiRequest()");
}

async function previewInvite(code) {
  throw new Error("TODO: call GET /invites/{code} via apiRequest()");
}

async function acceptInvite(code) {
  throw new Error("TODO: call POST /invites/{code}/accept via apiRequest()");
}

window.DormhubAPI = {
  register,
  login,
  logout: clearToken,
  isLoggedIn: () => !!getToken(),
  getCurrentUser,
  createHousehold,
  getMyHouseholds,
  createInvite,
  previewInvite,
  acceptInvite,
};
