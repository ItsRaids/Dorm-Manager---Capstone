if (!DormhubAPI.isLoggedIn()) {
  window.location.href = "login.html";
}

document.getElementById("logout-btn").addEventListener("click", () => {
  DormhubAPI.logout();
  window.location.href = "login.html";
});

async function loadCurrentUser() {
  // TODO: call DormhubAPI.getCurrentUser() and set
  // #user-greeting to `Hi, ${user.full_name}`.
  // On failure (expired/invalid token), log out and redirect to login.
}

async function loadHousehold() {
  // TODO:
  // - Call DormhubAPI.getMyHouseholds()
  // - If the user has no household, redirect to household.html
  // - Otherwise set #household-name text and populate #members-table
  //   (one row per member: name, role, joined date)
}

document.getElementById("generate-invite-btn").addEventListener("click", async () => {
  const resultEl = document.getElementById("invite-result");
  resultEl.textContent = "";

  // TODO:
  // - Call DormhubAPI.createInvite(householdId) — householdId will need
  //   to come from loadHousehold()'s result, so this probably needs
  //   that stored somewhere accessible (a module-level variable is fine)
  // - Display the resulting code/link in resultEl
});

(async function init() {
  await loadCurrentUser();
  await loadHousehold();
})();
