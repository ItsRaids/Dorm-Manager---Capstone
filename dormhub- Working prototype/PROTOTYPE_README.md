# DormHub

**One home. One hub. Better roommate living.**

DormHub is a student capstone project for coordinating shared living spaces. It combines household setup, chore assignments, shared lists, events, communication, voting, and roommate suggestions in a dark-themed dashboard.

## Quick start: interactive prototype

1. Download or clone this repository.
2. Open `frontend/index.html` in a modern browser.
3. Sign in using the demo account: `dean@dormhub.demo` / `demo1234`.

Alternatively, serve the frontend locally:

```bash
cd frontend
python -m http.server 5500
```

Then visit `http://localhost:5500`.

The **only active login and home experience** is the redesigned single-page interface at `frontend/index.html`. Legacy `login.html`, `household.html`, and their unused frontend scripts were removed.

## Features

### Sprint 1: Foundation and household setup

- Register, sign in, sign out, update a profile and change a password
- Create and switch households, join using an invite code or link
- View household members and a household-scoped dashboard
- Navigation, member/owner roles, and owner-only house rules editing in the UI

### Sprint 2: Household management

- Create, edit, assign, complete, and delete chores
- Shared shopping list with purchased-item checkboxes
- Move-in/household items and who is bringing them
- Create, edit, and delete calendar events, including upcoming events on the dashboard
- Contact directory, house rules, and announcements

### Sprint 3: Communication and coordination

- Household group chat and direct messages, with message history and timestamps
- Poll creation with multiple options, one recorded vote per user, and live-in-page results
- Anonymous suggestion submission and a household suggestion board
- Chore wheel: choose eligible members and randomly distribute unfinished chores
- Add, edit, remove, and open shared Amazon wishlist links from the wishlist page and dashboard

## Project layout

```text
frontend/
  index.html                 Redesigned single-page app
  src/prototype.css          UI theme and responsive layout
  src/prototype.js           Browser prototype functionality
backend/
  app/                       Existing FastAPI scaffold (not connected to prototype)
  requirements.txt
  .env.example
database/
  schema.sql                 Original MySQL schema scaffold
README.md
```

## Architecture and current limitations

**This repository currently contains an interactive browser prototype plus the original, separate backend scaffold.** The prototype stores its data in browser `localStorage`. It is not connected to FastAPI, MySQL, or SQLAlchemy. Consequently:

- Different browsers and devices **do not share data**. Messages, votes, chores, and suggestions persist only in the same browser storage.
- Invite codes work only where the issuing browser's stored invite record is available.
- Demo roommates are placeholders rather than independent registered accounts.
- Passwords are stored as plaintext in localStorage for demonstration only. **Do not use real passwords or sensitive information.**
- The suggestion interface does not display author identity, but the prototype cannot promise robust anonymity.
- Poll results and chat history update when the app is used in the same browser; there is no real-time multi-device synchronization.
- Amazon wishlist links are stored and opened; no Amazon API integration is implemented.
- UI-only role checks are not a substitute for server-side authorization.

This is **not production-ready authentication or shared hosting**. For deployment, connect authenticated FastAPI endpoints to a MySQL database, hash passwords, enforce per-household permissions on the server, store invitations and messages centrally, and implement message refresh or WebSockets.

## Suggested backend work

1. Extend SQLAlchemy models and migrations for chores, lists, events, contacts, announcements, messages, polls, votes, suggestions, and wishlist links.
2. Build authenticated, household-scoped CRUD endpoints.
3. Replace localStorage operations with API calls and use secure server-side sessions or tokens.
4. Add cross-user message delivery and polling or WebSockets.
5. Test authorization, invite expiry, anonymous suggestion handling, and household data isolation.

## Technology

- Prototype: HTML, CSS, vanilla JavaScript, browser localStorage
- Backend scaffold: Python, FastAPI, SQLAlchemy
- Planned database: MySQL

## Capstone roadmap

- **Week 1:** Foundation and household setup
- **Week 2:** Household management
- **Week 3:** Messaging, polls, anonymous suggestions, chore wheel, and wishlist

## Contributors

DormHub is developed as a team capstone project. Contributions should be reviewed through GitHub pull requests and tested in the browser before merging.
