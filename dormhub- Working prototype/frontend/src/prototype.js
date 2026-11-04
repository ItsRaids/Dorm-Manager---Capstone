(() => {
  'use strict';

  /* =========================================================
   * DormHub prototype
   * Single-file app. All data lives in localStorage (no backend).
   * ========================================================= */

  /* ---------------------------------------------------------
   * 1. CONSTANTS & SMALL HELPERS
   * --------------------------------------------------------- */
  const KEY = 'dormhub_prototype_v2';

  // Shortcut for querySelector
  const $ = s => document.querySelector(s);

  // Escape user-provided text before putting it into HTML
  const esc = s =>
    String(s ?? '').replace(/[&<>"']/g, c => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;'
    }[c]));

  // Random short id
  const uid = () => Math.random().toString(36).slice(2, 11);

  // Today's date as YYYY-MM-DD
  const today = () => new Date().toISOString().slice(0, 10);

  // Only allow http(s) links; anything else becomes '#'
  const safeUrl = s => {
    try {
      const u = new URL(s);
      return ['https:', 'http:'].includes(u.protocol) ? u.href : '#';
    } catch {
      return '#';
    }
  };

  /* ---------------------------------------------------------
   * 2. SEED DATA (demo account + sample household)
   * --------------------------------------------------------- */
  const seed = () => ({
    users: [
      { id: 'demo', name: 'Dean', email: 'dean@dormhub.demo', password: 'demo1234' }
    ],
    current: 'demo',
    households: [
      {
        id: 'maple',
        name: 'Maple Hall · Room 204',
        owner: 'demo',
        rules: 'Quiet hours start at 10 PM. Keep common areas clean.'
      }
    ],
    members: [
      { user: 'demo', house: 'maple', role: 'Owner' },
      { user: 'maya', house: 'maple', role: 'Member' },
      { user: 'alex', house: 'maple', role: 'Member' },
      { user: 'sam',  house: 'maple', role: 'Member' }
    ],
    activeHouse: 'maple',
    invites: [],
    chores: [
      { id: 'c1', house: 'maple', title: 'Take out trash',     assignee: 'demo', due: today(), done: false },
      { id: 'c2', house: 'maple', title: 'Clean the kitchen',  assignee: 'maya', due: today(), done: false },
      { id: 'c3', house: 'maple', title: 'Vacuum common area', assignee: 'alex', due: today(), done: false }
    ],
    shopping: [
      { id: 's1', house: 'maple', title: 'Dish soap',   quantity: '1 bottle', done: false },
      { id: 's2', house: 'maple', title: 'Paper towels', quantity: '2 rolls',  done: false }
    ],
    items: [
      { id: 'i1', house: 'maple', title: 'Microwave',   assignee: 'maya', done: true },
      { id: 'i2', house: 'maple', title: 'Mini fridge', assignee: 'demo', done: false }
    ],
    events: [
      { id: 'e1', house: 'maple', title: 'Movie night',   date: today(), time: '19:00', description: 'Dorm living room' },
      { id: 'e2', house: 'maple', title: 'Floor meeting', date: today(), time: '17:00', description: '2nd floor lounge' }
    ],
    contacts: [
      { id: 'k1', house: 'maple', name: 'Resident Assistant', phone: '555-0101', email: 'ra@example.com' }
    ],
    announcements: [
      { id: 'a1', house: 'maple', title: 'House note', body: 'Quiet hours start at 10 PM.', author: 'demo', date: today() }
    ],
    messages: [
      { id: 'm1', house: 'maple', from: 'maya', to: null, body: 'Movie night tonight? 🍿', at: new Date().toISOString() }
    ],
    polls: [
      { id: 'p1', house: 'maple', title: 'What should we watch?', options: ['Comedy', 'Action', 'Horror'], votes: {} }
    ],
    suggestions: [],
    wishlists: []
  });

  /* ---------------------------------------------------------
   * 3. STATE & STORAGE
   * --------------------------------------------------------- */
  let db;
  try {
    db = JSON.parse(localStorage.getItem(KEY)) || seed();
  } catch {
    db = seed();
  }

  // UI state (not persisted)
  let page = 'Home';
  let modal = null;
  let authMode = 'login';
  let search = '';
  let chatTarget = 'group';

  // Make sure collections added in later versions exist in older saved data
  for (const k of ['messages', 'polls', 'suggestions', 'wishlists']) db[k] ??= [];

  const save = () => localStorage.setItem(KEY, JSON.stringify(db));

  /* ---------------------------------------------------------
   * 4. DATA ACCESSORS
   * --------------------------------------------------------- */
  const user    = () => db.users.find(u => u.id === db.current);
  const house   = () => db.households.find(h => h.id === db.activeHouse);
  const records = k => db[k].filter(x => x.house === db.activeHouse);   // records for active house
  const members = () => db.members.filter(m => m.house === db.activeHouse);

  // Display name for a user id (falls back to demo roommates)
  const name = id =>
    db.users.find(u => u.id === id)?.name ||
    ({ maya: 'Maya', alex: 'Alex', sam: 'Sam' }[id] || 'Roommate');

  const initials = n =>
    (n || '?').split(' ').map(x => x[0]).slice(0, 2).join('').toUpperCase();

  // Small toast message
  const notify = t => {
    const e = document.createElement('div');
    e.className = 'toast';
    e.textContent = t;
    document.body.append(e);
    setTimeout(() => e.remove(), 2600);
  };

  /* ---------------------------------------------------------
   * 5. UI BUILDING BLOCKS
   * --------------------------------------------------------- */
  // Button that triggers an action string handled in onAction()
  const btn = (text, fn, cls = 'btn') =>
    `<button class="${cls}" data-action="${fn}">${text}</button>`;

  // Sidebar pages and their icons (same order)
  const nav = [
    'Home', 'Chores', 'Calendar', 'Shopping', 'Household Items', 'Contacts',
    'Announcements', 'Chat', 'Voting', 'Suggestions', 'Chore Wheel', 'Wishlist',
    'Households', 'Settings'
  ];
  const icons = ['🏠', '✅', '🗓️', '🛒', '📦', '📇', '📣', '💬', '🗳️', '💡', '🎡', '🎁', '🏘️', '⚙️'];

  // Generic list row
  const listItem = (icon, title, subtitle, extra = '', done = false) => `
    <div class="item ${done ? 'done' : ''}">
      <span style="font-size:21px">${icon}</span>
      <div class="grow"><strong>${esc(title)}</strong><small>${esc(subtitle)}</small></div>
      ${extra}
    </div>`;

  /* ---------------------------------------------------------
   * 6. MAIN RENDER (app shell: sidebar + topbar + current page)
   * --------------------------------------------------------- */
  function render() {
    if (!user()) return renderAuth();
    if (!house() && page !== 'Households') page = 'Households';
    const current = house();

    $('#app').innerHTML = `
      <div class="shell">
        <aside class="sidebar">
          <div class="brand"><span>◆</span> DormHub</div>
          <nav class="nav">
            ${nav.map((n, i) => `
              <button data-nav="${n}" class="${page === n ? 'active' : ''}">
                <span class="ico">${icons[i]}</span>${n}
              </button>`).join('')}
          </nav>
          <div class="side-bottom">
            <div class="mini">✨ <strong>Level 5 perk</strong><p>Earn XP by helping your household.</p></div>
            <div class="person">
              <span class="avatar">${initials(user().name)}</span>
              <div><strong>${esc(user().name)}</strong><br><small>${esc(current?.name || 'No household')}</small></div>
            </div>
          </div>
        </aside>

        <main class="content">
          <header class="topbar">
            <button class="pill" data-nav="Households">⌂ ${esc(current?.name || 'Choose a household')} ▾</button>
            <div class="top-actions">
              <input class="search" id="search" placeholder="⌕ Search this page..." value="${esc(search)}">
              <button class="circle" data-nav="Announcements" title="Announcements">🔔</button>
              <button class="circle" data-nav="Settings" title="Profile">${initials(user().name)}</button>
            </div>
          </header>
          <div id="view">${view()}</div>
        </main>
      </div>
      ${modal ? modalHtml() : ''}`;

    bind();
  }

  // Pick which page to draw
  function view() {
    if (page === 'Home')       return home();
    if (page === 'Households') return households();
    if (page === 'Settings')   return settings();
    if (page === 'Chat')       return chat();
    if (page === 'Voting')     return voting();
    if (page === 'Suggestions') return suggestions();
    if (page === 'Chore Wheel') return wheel();
    if (page === 'Wishlist')   return wishlist();
    if (page === 'Calendar')   return section('Calendar', 'Plan and manage shared events.', 'events');

    // Generic list pages
    const subtitles = {
      Chores: 'Keep household tasks on track.',
      Shopping: 'Shop together and mark purchases.',
      'Household Items': 'Track what each roommate is bringing.',
      Contacts: 'Important numbers and emails.',
      Announcements: 'Keep everyone in the loop.'
    };
    const collections = {
      Chores: 'chores',
      Shopping: 'shopping',
      'Household Items': 'items',
      Contacts: 'contacts',
      Announcements: 'announcements'
    };
    return section(page, subtitles[page], collections[page]);
  }

  /* ---------------------------------------------------------
   * 7. PAGES
   * --------------------------------------------------------- */

  /* ---- Home dashboard ---- */
  function home() {
    const cs = records('chores');
    const ev = records('events')
      .slice()
      .sort((a, b) => a.date.localeCompare(b.date))
      .slice(0, 4);
    const doneCount = cs.filter(c => c.done).length;

    return `
      <h1 class="title">Hey, ${esc(user().name.split(' ')[0])} 👋</h1>
      <p class="subtitle">A little teamwork. A better home.</p>

      <div class="dashboard">
        <!-- Left column -->
        <div class="stack">
          <div class="hero">
            <div>
              <small>✨ Your home base</small>
              <h2>Level ${Math.max(1, Math.floor(doneCount / 3) + 4)}</h2>
              <p>${doneCount * 30 + 320} / 500 XP</p>
              <div class="progress"><span></span></div>
              <small>Earn XP by finishing chores</small>
            </div>
            <div class="art">🏠</div>
          </div>

          <div class="announcement">
            <b>📢 House note</b>
            <span>${esc(records('announcements')[0]?.body || house()?.rules || 'Welcome home!')}</span>
          </div>

          <section class="panel">
            <div class="panel-head">
              <h3>Your chores</h3>
              <div>${btn('+ Add chore', 'add:chores', 'btn small')} ${btn('See all ›', 'nav:Chores', 'link')}</div>
            </div>
            ${cs.length
              ? cs.slice(0, 5).map(c => `
                <div class="item ${c.done ? 'done' : ''}">
                  <input type="checkbox" class="check" data-toggle="chores:${c.id}" ${c.done ? 'checked' : ''}>
                  <span>🧹</span>
                  <div class="grow">
                    <strong>${esc(c.title)}</strong>
                    <small>${esc(name(c.assignee))} · ${esc(c.due || 'No due date')}</small>
                  </div>
                  <span class="tag ${c.due === today() ? 'orange' : ''}">${c.done ? 'Done' : 'To do'}</span>
                </div>`).join('')
              : '<p class="empty">No chores yet.</p>'}
          </section>

          <section class="panel">
            <div class="panel-head">
              <h3>Latest announcements</h3>
              ${btn('View all ›', 'nav:Announcements', 'link')}
            </div>
            ${records('announcements').slice(0, 2).map(a => listItem('📣', a.title, a.body)).join('')
              || '<p class="empty">No announcements yet.</p>'}
          </section>
        </div>

        <!-- Right column -->
        <div class="stack">
          <section class="panel">
            <div class="panel-head"><h3>Room leaderboard</h3><small class="muted">This week</small></div>
            ${members().map((m, i) => {
              const n = cs.filter(c => c.assignee === m.user && c.done).length;
              return `
                <div class="leader">
                  <span>${i + 1}</span>
                  <span class="avatar">${initials(name(m.user))}</span>
                  <div class="grow">
                    <strong>${esc(name(m.user))}${m.user === db.current ? ' (you)' : ''}</strong><br>
                    <small>${n} chores done</small>
                  </div>
                  <b>+${n * 30} XP</b>
                </div>`;
            }).join('')}
          </section>

          <section class="panel">
            <div class="panel-head"><h3>Coming up</h3>${btn('Calendar ›', 'nav:Calendar', 'link')}</div>
            ${ev.map(e => listItem('📅', e.title, `${e.date} · ${e.time || ''} · ${e.description || ''}`)).join('')
              || '<p class="empty">No upcoming events.</p>'}
          </section>

          <section class="panel">
            <div class="panel-head"><h3>Roommates</h3>${btn('Invite +', 'invite', 'link')}</div>
            ${members().map(m => listItem('👤', name(m.user), m.role)).join('')}
          </section>

          <section class="panel">
            <div class="panel-head"><h3>Shared wishlist</h3>${btn('Manage ›', 'nav:Wishlist', 'link')}</div>
            ${records('wishlists').map(w => listItem(
              '🎁', w.title, w.url,
              `<a class="btn small" href="${esc(safeUrl(w.url))}" target="_blank" rel="noopener noreferrer">Open ↗</a>`
            )).join('') || '<p class="empty">No wishlist link yet.</p>'}
          </section>
        </div>
      </div>`;
  }

  /* ---- Generic list page (chores, calendar, shopping, items, contacts, announcements) ---- */
  const labels = {
    chores: 'Chore',
    shopping: 'Shopping item',
    items: 'Household item',
    events: 'Event',
    contacts: 'Contact',
    announcements: 'Announcement'
  };

  function section(title, subtitle, k) {
    // Filter rows by the search box
    const filtered = records(k).filter(r =>
      JSON.stringify(r).toLowerCase().includes(search.toLowerCase())
    );

    return `
      <div class="section-heading">
        <div><h1>${esc(title)}</h1><p class="muted">${esc(subtitle)}</p></div>
        ${btn('+ Add ' + labels[k], 'add:' + k)}
      </div>
      <div class="panel">
        ${filtered.length
          ? filtered.map(r => row(k, r)).join('')
          : '<div class="empty">Nothing here yet. Add your first entry.</div>'}
      </div>`;
  }

  // One row in a generic list
  function row(k, r) {
    const title = r.title || r.name;
    const detail = ({
      chores:        `Assigned to ${name(r.assignee)} · Due ${r.due || 'anytime'}`,
      shopping:      r.quantity || 'Shared shopping list',
      items:         `Bringing: ${name(r.assignee)}`,
      events:        `${r.date} at ${r.time || 'anytime'} · ${r.description || ''}`,
      contacts:      `${r.phone || ''} · ${r.email || ''}`,
      announcements: `${r.body || ''} · ${r.date || ''}`
    })[k];

    // Checklist types get a checkbox, others get an icon
    const lead = ['chores', 'shopping', 'items'].includes(k)
      ? `<input type="checkbox" class="check" data-toggle="${k}:${r.id}" ${r.done ? 'checked' : ''}>`
      : `<span style="font-size:22px">${({ events: '🗓️', contacts: '📇', announcements: '📣' })[k]}</span>`;

    return `
      <div class="item ${r.done ? 'done' : ''}">
        ${lead}
        <div class="grow"><strong>${esc(title)}</strong><small>${esc(detail)}</small></div>
        ${btn('Edit', 'edit:' + k + ':' + r.id, 'btn secondary small')}
        ${btn('Delete', 'delete:' + k + ':' + r.id, 'btn danger small')}
      </div>`;
  }

  /* ---- Households (create / join / switch / invite) ---- */
  function households() {
    const mine = db.households.filter(h =>
      db.members.some(m => m.house === h.id && m.user === db.current)
    );

    return `
      <div class="section-heading">
        <div><h1>Households</h1><p class="muted">Create a room, join one, or invite roommates.</p></div>
        <div class="actions">${btn('+ Create', 'createHouse')} ${btn('Join with code', 'joinHouse', 'btn secondary')}</div>
      </div>
      ${mine.map(h => `
        <section class="panel" style="margin-bottom:16px">
          <div class="panel-head">
            <h3>🏠 ${esc(h.name)} ${h.id === db.activeHouse ? '· Current' : ''}</h3>
            <div class="actions">
              ${btn('Switch', 'switch:' + h.id, 'btn secondary small')}
              ${btn('Invite roommates', 'invite', 'btn small')}
            </div>
          </div>
          <p class="muted">${db.members.filter(m => m.house === h.id).length} members</p>
          ${db.members.filter(m => m.house === h.id).map(m => listItem('👤', name(m.user), m.role)).join('')}
        </section>`).join('') || '<p class="empty">Create or join a household to get started.</p>'}`;
  }

  /* ---- Chat (group + direct messages) ---- */
  function chat() {
    const people = members().filter(m => m.user !== db.current);

    // Group chat = messages with no recipient; DM = messages between me and target
    const ms = records('messages')
      .filter(m => chatTarget === 'group'
        ? !m.to
        : (m.to === db.current && m.from === chatTarget) ||
          (m.from === db.current && m.to === chatTarget))
      .sort((a, b) => a.at.localeCompare(b.at));

    return `
      <div class="section-heading">
        <div><h1>Messages</h1><p class="muted">Household group chat and direct messages</p></div>
      </div>
      <div class="chat-layout">
        <section class="panel">
          <h3>Conversations</h3>
          <button class="chat-tab ${chatTarget === 'group' ? 'selected' : ''}" data-chat="group">🏠 Household group</button>
          ${people.map(m => `
            <button class="chat-tab ${chatTarget === m.user ? 'selected' : ''}" data-chat="${esc(m.user)}">
              👤 ${esc(name(m.user))}
            </button>`).join('')}
        </section>
        <section class="panel">
          <h3>${chatTarget === 'group' ? 'Household group' : esc(name(chatTarget))}</h3>
          <div class="messages">
            ${ms.map(m => `
              <div class="bubble ${m.from === db.current ? 'mine' : ''}">
                <strong>${esc(name(m.from))}</strong>
                <p>${esc(m.body)}</p>
                <small>${new Date(m.at).toLocaleString()}</small>
              </div>`).join('') || '<p class="empty">No messages yet. Start the conversation.</p>'}
          </div>
          <form class="compose" data-form="message">
            <input name="body" placeholder="Write a message..." maxlength="2000" required autocomplete="off">
            <button class="btn">Send</button>
          </form>
        </section>
      </div>`;
  }

  /* ---- Voting (polls, one vote per member) ---- */
  function voting() {
    return `
      <div class="section-heading">
        <div><h1>Household polls</h1><p class="muted">One vote per member, per poll</p></div>
        ${btn('+ Create poll', 'createPoll')}
      </div>
      ${records('polls').map(p => {
        const total = Object.keys(p.votes || {}).length;
        const mine = p.votes?.[db.current];
        return `
          <section class="panel spaced">
            <h3>${esc(p.title)}</h3>
            <p class="muted">${total} vote${total === 1 ? '' : 's'} · ${mine !== undefined ? 'Your vote is recorded' : 'Choose an option'}</p>
            ${p.options.map((o, i) => {
              const count = Object.values(p.votes || {}).filter(v => Number(v) === i).length;
              return `
                <div class="vote-option">
                  <div class="panel-head">
                    <strong>${esc(o)}</strong>
                    <span>${count} (${total ? Math.round(count / total * 100) : 0}%)</span>
                  </div>
                  <div class="vote-track"><span style="width:${total ? count / total * 100 : 0}%"></span></div>
                  ${btn(mine === i ? '✓ Your vote' : 'Vote', 'vote:' + p.id + ':' + i, 'btn secondary small')}
                </div>`;
            }).join('')}
            ${btn('Delete poll', 'deletePoll:' + p.id, 'btn danger small')}
          </section>`;
      }).join('') || '<div class="panel empty">No polls yet.</div>'}`;
  }

  /* ---- Anonymous suggestions ---- */
  function suggestions() {
    return `
      <div class="section-heading">
        <div><h1>Anonymous suggestions</h1><p class="muted">Share ideas without displaying who submitted them</p></div>
        ${btn('+ Submit suggestion', 'addSuggestion')}
      </div>
      <div class="panel">
        <p class="muted-note">Suggestions are anonymous in this browser interface. This is not a secure anonymity guarantee.</p>
        ${records('suggestions').slice().reverse().map(s => listItem('💡', 'Anonymous suggestion', s.body)).join('')
          || '<p class="empty">No suggestions yet.</p>'}
      </div>`;
  }

  /* ---- Chore wheel (random assignment) ---- */
  function wheel() {
    const uncompleted = records('chores').filter(c => !c.done);

    return `
      <div class="section-heading">
        <div><h1>Chore wheel</h1><p class="muted">Randomly distribute unfinished chores among selected roommates</p></div>
      </div>
      <section class="panel">
        <h3>Choose roommates</h3>
        <form data-form="wheel" class="form">
          ${members().map(m => `
            <label class="member-check">
              <input type="checkbox" name="member" value="${esc(m.user)}" checked> ${esc(name(m.user))}
            </label>`).join('')}
          <p class="muted">${uncompleted.length} unfinished chores available for assignment.</p>
          <button class="btn" ${uncompleted.length ? '' : 'disabled'}>🎡 Spin and assign chores</button>
        </form>
      </section>
      <section class="panel spaced">
        <h3>Current assignments</h3>
        ${uncompleted.map(c => listItem('🧹', c.title, 'Assigned to ' + name(c.assignee))).join('')
          || '<p class="empty">All chores are complete.</p>'}
      </section>`;
  }

  /* ---- Shared wishlist (Amazon links) ---- */
  function wishlist() {
    return `
      <div class="section-heading">
        <div><h1>Shared wishlist</h1><p class="muted">Keep Amazon wishlist links available to everyone in your household</p></div>
        ${btn('+ Add wishlist', 'addWishlist')}
      </div>
      <section class="panel">
        ${records('wishlists').map(w => listItem('🎁', w.title, w.url, `
          <div class="actions">
            <a class="btn small" href="${esc(safeUrl(w.url))}" target="_blank" rel="noopener noreferrer">Open ↗</a>
            ${btn('Edit', 'editWishlist:' + w.id, 'btn secondary small')}
            ${btn('Delete', 'deleteWishlist:' + w.id, 'btn danger small')}
          </div>`)).join('') || '<p class="empty">Add a wishlist to share it with roommates.</p>'}
      </section>`;
  }

  /* ---- Settings (profile, password, house rules, logout) ---- */
  function settings() {
    return `
      <div class="section-heading"><h1>Settings & profile</h1></div>
      <div class="grid2">
        <div class="panel">
          <h3>Your profile</h3>
          <form class="form" data-form="profile">
            <label class="field">Name<input name="name" required value="${esc(user().name)}"></label>
            <label class="field">Email<input type="email" name="email" required value="${esc(user().email)}"></label>
            <button class="btn">Save profile</button>
          </form>
        </div>

        <div class="panel">
          <h3>Change password</h3>
          <form class="form" data-form="password">
            <label class="field">Current password<input type="password" name="old" required></label>
            <label class="field">New password<input type="password" name="new" minlength="8" required></label>
            <button class="btn">Update password</button>
          </form>
          <p class="muted-note">Prototype accounts are stored in this browser only, not securely on a server.</p>
        </div>

        ${house() ? `
          <div class="panel">
            <h3>House rules</h3>
            <form class="form" data-form="rules">
              <label class="field">Rules<textarea name="rules">${esc(house().rules || '')}</textarea></label>
              <button class="btn" ${house().owner !== db.current ? 'disabled' : ''}>Save house rules</button>
            </form>
          </div>` : ''}

        <div class="panel">
          <h3>Account</h3>
          ${btn('Log out', 'logout', 'btn danger')}
          <p class="muted-note">This prototype keeps data in localStorage on this device.</p>
        </div>
      </div>`;
  }

  /* ---- Login / register screen ---- */
  function renderAuth() {
    const join = new URLSearchParams(location.search).get('invite');

    $('#app').innerHTML = `
      <div class="auth">
        <div class="auth-card">
          <div class="brand"><span>◆</span> DormHub</div>
          <h1>Welcome home 👋</h1>
          <p class="muted">Manage your room, together.</p>
          <div class="tabs">
            ${btn('Log in', 'mode:login', authMode === 'login' ? 'btn' : 'btn secondary')}
            ${btn('Create account', 'mode:register', authMode === 'register' ? 'btn' : 'btn secondary')}
          </div>
          <form class="form" data-form="auth">
            ${authMode === 'register' ? '<label class="field">Full name<input name="name" required></label>' : ''}
            <label class="field">Email<input type="email" name="email" required></label>
            <label class="field">Password<input type="password" name="password" minlength="8" required></label>
            <button class="btn">${authMode === 'login' ? 'Log in' : 'Create account'}</button>
          </form>
          <p class="muted-note">Demo login: dean@dormhub.demo / demo1234</p>
          ${join ? '<p class="muted-note">Your invite will be applied after login.</p>' : ''}
        </div>
      </div>`;

    bind();
  }

  /* ---------------------------------------------------------
   * 8. MODALS & FORM FIELDS
   * --------------------------------------------------------- */

  // Form fields for each record type (add/edit modal)
  function fields(k, r = {}) {
    const input = (n, label, type = 'text', required = true) => `
      <label class="field">${label}
        <input name="${n}" type="${type}" value="${esc(r[n] || '')}" ${required ? 'required' : ''}>
      </label>`;

    const assignee = () => `
      <label class="field">Roommate
        <select name="assignee">
          ${members().map(m => `
            <option value="${m.user}" ${r.assignee === m.user ? 'selected' : ''}>${esc(name(m.user))}</option>`).join('')}
        </select>
      </label>`;

    return ({
      chores:   input('title', 'Chore') + assignee() + input('due', 'Due date', 'date', false),
      shopping: input('title', 'Item') + input('quantity', 'Quantity', 'text', false),
      items:    input('title', 'Household item') + assignee(),
      events:   input('title', 'Event') + input('date', 'Date', 'date') + input('time', 'Time', 'time', false) +
                input('description', 'Location / details', 'text', false),
      contacts: input('name', 'Contact name') + input('phone', 'Phone', 'tel', false) + input('email', 'Email', 'email', false),
      announcements: input('title', 'Title') +
                `<label class="field">Message<textarea name="body" required>${esc(r.body || '')}</textarea></label>`
    })[k];
  }

  // Build the HTML for whichever modal is open
  function modalHtml() {
    let body = '', title = '', form = '';

    if (modal.type === 'record') {
      title = (modal.id ? 'Edit ' : 'Add ') + labels[modal.key];
      form = 'record';
      body = fields(modal.key, modal.id ? db[modal.key].find(r => r.id === modal.id) : {});
    }
    if (modal.type === 'createHouse') {
      title = 'Create household';
      form = 'createHouse';
      body = '<label class="field">Household name<input name="name" placeholder="Maple Hall · Room 204" required></label>';
    }
    if (modal.type === 'joinHouse') {
      title = 'Join household';
      form = 'joinHouse';
      body = '<label class="field">Invite code or link<input name="code" required placeholder="Paste invite code or URL"></label>';
    }
    if (modal.type === 'poll') {
      title = 'Create poll';
      form = 'poll';
      body = `
        <label class="field">Question<input name="title" required maxlength="200"></label>
        <label class="field">Voting options (one per line)
          <textarea name="options" required placeholder="Pizza\nTacos\nSushi"></textarea>
        </label>`;
    }
    if (modal.type === 'suggestion') {
      title = 'Anonymous suggestion';
      form = 'suggestion';
      body = '<label class="field">Suggestion<textarea name="body" required maxlength="2000"></textarea></label>';
    }
    if (modal.type === 'wishlist') {
      title = modal.id ? 'Edit wishlist' : 'Add wishlist';
      form = 'wishlist';
      const w = db.wishlists.find(x => x.id === modal.id) || {};
      body = `
        <label class="field">Title<input name="title" required value="${esc(w.title || '')}"></label>
        <label class="field">Amazon wishlist URL
          <input name="url" type="url" required value="${esc(w.url || 'https://www.amazon.com/hz/wishlist/ls/')}">
        </label>`;
    }
    if (modal.type === 'invite') {
      title = 'Invite roommates';
      body = `
        <p class="muted">Share this link. Roommates can join after signing in.</p>
        <div class="code">${esc(modal.link)}</div>
        <p class="muted-note">Codes are valid in this browser prototype. Sharing between different browsers requires a deployed backend.</p>
        ${btn('Copy invite link', 'copyInvite')}`;
    }

    return `
      <div class="modal-bg" id="overlay">
        <div class="modal">
          <div class="panel-head"><h2>${esc(title)}</h2>${btn('✕', 'close', 'btn secondary small')}</div>
          ${form
            ? `<form class="form" data-form="${form}">
                 ${body}
                 <div class="modal-actions">${btn('Cancel', 'close', 'btn secondary')}<button class="btn">Save</button></div>
               </form>`
            : body}
        </div>
      </div>`;
  }

  function openModal(m) {
    modal = m;
    render();
  }

  /* ---------------------------------------------------------
   * 9. ACTION HANDLER (buttons with data-action="cmd:arg1:arg2")
   * --------------------------------------------------------- */
  function onAction(action) {
    const [cmd, k, id] = action.split(':');

    // --- Open modals ---
    if (cmd === 'createPoll')    { openModal({ type: 'poll' }); return; }
    if (cmd === 'addSuggestion') { openModal({ type: 'suggestion' }); return; }
    if (cmd === 'addWishlist' || cmd === 'editWishlist') { openModal({ type: 'wishlist', id: k }); return; }
    if (cmd === 'add' || cmd === 'edit')                 { openModal({ type: 'record', key: k, id }); return; }
    if (cmd === 'createHouse' || cmd === 'joinHouse')    { openModal({ type: cmd }); return; }

    // --- Deletes ---
    if (cmd === 'deleteWishlist') {
      if (!confirm('Delete wishlist?')) return;
      db.wishlists = db.wishlists.filter(x => x.id !== k);
      save(); render();
      return;
    }
    if (cmd === 'deletePoll') {
      if (!confirm('Delete poll?')) return;
      db.polls = db.polls.filter(x => x.id !== k);
      save(); render();
      return;
    }
    if (cmd === 'delete') {
      if (!confirm('Delete this entry?')) return;
      db[k] = db[k].filter(r => r.id !== id);
      save(); render(); notify('Deleted');
      return;
    }

    // --- Voting ---
    if (cmd === 'vote') {
      const p = db.polls.find(x => x.id === k && x.house === db.activeHouse);
      if (p) {
        p.votes ??= {};
        p.votes[db.current] = Number(id);
        save(); render(); notify('Vote recorded');
      }
      return;
    }

    // --- Navigation / UI ---
    if (cmd === 'nav')   { page = k; search = ''; render(); return; }
    if (cmd === 'mode')  { authMode = k; render(); return; }
    if (cmd === 'close') { modal = null; render(); return; }

    // --- Households ---
    if (cmd === 'switch') {
      db.activeHouse = k;
      page = 'Home';
      save(); render();
      return;
    }
    if (cmd === 'invite') {
      const token = uid();
      db.invites.push({ token, house: db.activeHouse });
      save();
      openModal({ type: 'invite', link: `${location.origin}${location.pathname}?invite=${token}` });
      return;
    }
    if (cmd === 'copyInvite') {
      navigator.clipboard?.writeText(modal.link)
        .then(() => notify('Link copied'))
        .catch(() => prompt('Copy invite link:', modal.link));
      return;
    }

    // --- Account ---
    if (cmd === 'logout') {
      db.current = null;
      page = 'Home';
      save(); render();
      return;
    }
  }

  // Join the household that an invite token points to
  function applyInvite(token) {
    const invite = db.invites.find(i => i.token === token);
    if (!invite) {
      notify('Invite not found in this browser');
      return false;
    }
    if (!db.members.some(m => m.user === db.current && m.house === invite.house)) {
      db.members.push({ user: db.current, house: invite.house, role: 'Member' });
    }
    db.activeHouse = invite.house;
    history.replaceState({}, '', location.pathname);
    return true;
  }

  /* ---------------------------------------------------------
   * 10. FORM SUBMISSION HANDLER
   * --------------------------------------------------------- */
  function submit(form, f) {
    const data = Object.fromEntries(new FormData(f));

    if (form === 'message') {
      // Send chat message (group when `to` is null)
      if (!house()) return;
      const to = chatTarget === 'group' ? null : chatTarget;
      if (to && !members().some(m => m.user === to)) return notify('Roommate not found');
      db.messages.push({
        id: uid(),
        house: db.activeHouse,
        from: db.current,
        to,
        body: data.body.trim(),
        at: new Date().toISOString()
      });

    } else if (form === 'poll') {
      // Create poll (needs 2+ options)
      const options = data.options.split(/\r?\n/).map(x => x.trim()).filter(Boolean);
      if (options.length < 2) return notify('Add at least two options');
      db.polls.push({ id: uid(), house: db.activeHouse, title: data.title.trim(), options, votes: {} });
      modal = null;

    } else if (form === 'suggestion') {
      // Anonymous suggestion (no author stored)
      if (!data.body.trim()) return;
      db.suggestions.push({ id: uid(), house: db.activeHouse, body: data.body.trim() });
      modal = null;

    } else if (form === 'wishlist') {
      // Add/edit wishlist; only HTTPS Amazon links allowed
      let u;
      try { u = new URL(data.url); } catch { return notify('Enter a valid URL'); }
      if (u.protocol !== 'https:' || !/(^|\.)amazon\.[a-z.]+$/i.test(u.hostname)) {
        return notify('Enter an HTTPS Amazon wishlist link');
      }
      let w = db.wishlists.find(x => x.id === modal.id && x.house === db.activeHouse);
      if (!w) {
        w = { id: uid(), house: db.activeHouse };
        db.wishlists.push(w);
      }
      w.title = data.title.trim();
      w.url = u.href;
      modal = null;

    } else if (form === 'wheel') {
      // Shuffle selected roommates (Fisher–Yates) then deal out unfinished chores round-robin
      const ids = [...f.querySelectorAll('input[name=member]:checked')].map(x => x.value);
      if (!ids.length) return notify('Select at least one roommate');
      const chores = records('chores').filter(c => !c.done);
      const shuffled = ids.slice();
      for (let i = shuffled.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
      }
      chores.forEach((c, i) => (c.assignee = shuffled[i % shuffled.length]));
      notify('Chores randomly assigned');

    } else if (form === 'auth') {
      // Log in or register
      const email = data.email.trim().toLowerCase();
      if (authMode === 'register') {
        if (db.users.some(u => u.email === email)) return notify('Email already registered');
        const id = uid();
        db.users.push({ id, name: data.name.trim(), email, password: data.password });
        db.current = id;
        db.activeHouse = null;
        page = 'Households';
      } else {
        const u = db.users.find(u => u.email === email && u.password === data.password);
        if (!u) return notify('Invalid email or password');
        db.current = u.id;
        db.activeHouse = db.members.find(m => m.user === u.id)?.house || null;
      }
      // Apply pending invite from the URL, if any
      const token = new URLSearchParams(location.search).get('invite');
      if (token) applyInvite(token);

    } else if (form === 'record') {
      // Generic add/edit for chores, shopping, items, events, contacts, announcements
      const k = modal.key;
      let record = modal.id ? db[k].find(r => r.id === modal.id) : null;
      if (!record) {
        record = { id: uid(), house: db.activeHouse, done: false };
        db[k].push(record);
      }
      Object.assign(record, data);
      if (k === 'announcements') {
        record.author = db.current;
        record.date = record.date || today();
      }
      modal = null;

    } else if (form === 'createHouse') {
      const id = uid();
      db.households.push({ id, name: data.name.trim(), owner: db.current, rules: 'Quiet hours start at 10 PM.' });
      db.members.push({ user: db.current, house: id, role: 'Owner' });
      db.activeHouse = id;
      page = 'Home';
      modal = null;

    } else if (form === 'joinHouse') {
      // Accept either a raw code or a full invite URL
      let code = data.code.trim();
      try { code = new URL(code).searchParams.get('invite') || code; } catch {}
      if (!applyInvite(code)) return;
      page = 'Home';
      modal = null;

    } else if (form === 'profile') {
      const email = data.email.trim().toLowerCase();
      if (db.users.some(u => u.id !== db.current && u.email === email)) return notify('Email is already in use');
      Object.assign(user(), { name: data.name.trim(), email });

    } else if (form === 'password') {
      if (user().password !== data.old) return notify('Current password is incorrect');
      user().password = data.new;

    } else if (form === 'rules') {
      if (house().owner !== db.current) return notify('Only the household owner can edit rules');
      house().rules = data.rules;
    }

    save();
    render();
    notify('Saved successfully');
  }

  /* ---------------------------------------------------------
   * 11. EVENT BINDING (re-run after every render)
   * --------------------------------------------------------- */
  function bind() {
    // Chat conversation tabs
    document.querySelectorAll('[data-chat]').forEach(e => {
      e.onclick = () => { chatTarget = e.dataset.chat; render(); };
    });

    // Sidebar / link navigation
    document.querySelectorAll('[data-nav]').forEach(e => {
      e.onclick = () => { page = e.dataset.nav; search = ''; render(); };
    });

    // Buttons with actions
    document.querySelectorAll('[data-action]').forEach(e => {
      e.onclick = () => onAction(e.dataset.action);
    });

    // Checkbox toggles (done / not done)
    document.querySelectorAll('[data-toggle]').forEach(e => {
      e.onchange = () => {
        const [k, id] = e.dataset.toggle.split(':');
        const r = db[k].find(x => x.id === id);
        if (r) { r.done = e.checked; save(); render(); }
      };
    });

    // Forms
    document.querySelectorAll('[data-form]').forEach(f => {
      f.onsubmit = e => { e.preventDefault(); submit(f.dataset.form, f); };
    });

    // Live search: re-render only the page body so the input keeps focus
    const s = $('#search');
    if (s) {
      s.oninput = e => {
        search = e.target.value;
        if (page !== 'Home') {
          const v = $('#view');
          const pos = s.selectionStart;
          v.innerHTML = view();
          bind();
          const ns = $('#search');
          if (ns) { ns.focus(); ns.setSelectionRange(pos, pos); }
        }
      };
    }

    // Click outside a modal to close it
    const overlay = $('#overlay');
    if (overlay) {
      overlay.onclick = e => {
        if (e.target === overlay) { modal = null; render(); }
      };
    }
  }

  /* ---------------------------------------------------------
   * 12. START
   * --------------------------------------------------------- */
  save();
  render();
})();
