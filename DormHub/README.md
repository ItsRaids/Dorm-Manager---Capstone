# Dormhub — Sprint 1 Scaffold: Foundation & Household Setup

This is a starting framework for Sprint 1, not a finished app. Project
structure, routing, DB connection, and models are wired up; the actual
logic inside most endpoints is left as `TODO` for the team to build.

**Sprint 1 scope**
- User registration/login
- MySQL database setup
- Household creation/joining
- Invite system
- Main dashboard

## Stack
- Backend: Python (FastAPI + SQLAlchemy)
- Database: MySQL
- Frontend: Vanilla JavaScript / HTML / CSS

## Project Structure
```
dormhub/
├── backend/
│   ├── app/
│   │   ├── models/       # SQLAlchemy models (mostly complete — extend as needed)
│   │   ├── routes/       # API endpoints (stubbed — TODO logic)
│   │   ├── schemas/      # Pydantic request/response shapes (stubbed)
│   │   ├── database.py   # DB connection/session setup (done)
│   │   ├── auth.py       # password hashing + JWT helpers (stubbed)
│   │   └── main.py       # FastAPI app entrypoint (done)
│   ├── requirements.txt
│   └── .env.example
├── frontend/
│   ├── index.html         # dashboard shell
│   ├── login.html         # login/register shell
│   ├── household.html     # create/join household shell
│   └── src/
│       ├── api.js         # fetch helpers (stubbed)
│       ├── app.js         # dashboard page logic (stubbed)
│       ├── auth.js        # login/register page logic (stubbed)
│       ├── household.js   # household create/join/invite logic (stubbed)
│       └── styles.css
├── database/
│   └── schema.sql         # core tables (drafted — review as a team)
└── .gitignore
```

## Who owns what (suggested split)
| Area | Files | Depends on |
|---|---|---|
| DB schema | `database/schema.sql` | — |
| Auth (register/login) | `backend/app/routes/auth.py`, `backend/app/auth.py`, `frontend/login.html`, `frontend/src/auth.js` | DB schema |
| Households (create/join) | `backend/app/routes/households.py`, `frontend/household.html`, `frontend/src/household.js` | Auth |
| Invite system | `backend/app/routes/invites.py`, invite UI inside `household.js` | Households |
| Dashboard | `backend/app/routes/dashboard.py`, `frontend/index.html`, `frontend/src/app.js` | Auth + Households |

These can mostly be worked on in parallel once the DB schema is agreed on,
since the models and route files already exist as stubs — each person can
fill in their file without stepping on others.

## Getting Started

### 1. Database
```bash
mysql -u root -p -e "CREATE DATABASE dormhub"
mysql -u root -p dormhub < database/schema.sql
```
Review `schema.sql` as a team first — it's a draft of what Sprint 1 needs,
not final.

### 2. Backend
```bash
cd backend
python -m venv venv
source venv/bin/activate   # Windows: venv\Scripts\activate
pip install -r requirements.txt
cp .env.example .env       # fill in DB credentials + a secret key
uvicorn app.main:app --reload
```
Docs at `http://localhost:8000/docs`. Endpoints currently return
`501 Not Implemented` until their `TODO`s are filled in.

### 3. Frontend
No build step needed:
```bash
cd frontend
python -m http.server 5500
```
Visit `http://localhost:5500`. Confirm `API_BASE_URL` in
`frontend/src/api.js` matches your backend.

## How the stubs work
- **Backend routes** have the function signature, request/response models,
  and a docstring describing what the endpoint should do, but the body
  just raises `HTTPException(501)`. Replace the body with real logic.
- **Frontend JS** has the function signatures and DOM wiring in place;
  the actual `fetch` calls / logic are marked `// TODO`.
- Everything that's foundational and unlikely to need debate (DB
  connection, password hashing setup, JWT helpers, app wiring/routing)
  is left complete so the team isn't blocked on plumbing.

## Definition of done for Sprint 1
- [ ] Schema reviewed and applied to a shared dev DB
- [ ] A user can register and log in, and stay logged in across a refresh
- [ ] A logged-in user with no household sees a "create or join" prompt
- [ ] A user can create a household and becomes its first member
- [ ] A household member can generate an invite (link or code)
- [ ] A different user can accept that invite and join the household
- [ ] Logged-in members with a household land on a dashboard showing
      household name + member list
