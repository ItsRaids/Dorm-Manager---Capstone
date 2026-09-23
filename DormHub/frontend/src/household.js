if (!DormhubAPI.isLoggedIn()) {
  window.location.href = "login.html";
}

document.getElementById("create-household-form").addEventListener("submit", async (e) => {
  e.preventDefault();
  const name = document.getElementById("household-name").value;
  const errorEl = document.getElementById("create-error");
  errorEl.textContent = "";

  // TODO:
  // - Call DormhubAPI.createHousehold(name)
  // - On success, redirect to index.html (the dashboard)
  // - On failure, show err.message in errorEl
});

document.getElementById("join-household-form").addEventListener("submit", async (e) => {
  e.preventDefault();
  const code = document.getElementById("invite-code").value;
  const errorEl = document.getElementById("join-error");
  errorEl.textContent = "";

  // TODO:
  // - Optionally call DormhubAPI.previewInvite(code) first to show the
  //   household name and confirm before joining
  // - Call DormhubAPI.acceptInvite(code)
  // - On success, redirect to index.html
  // - On failure, show err.message in errorEl
});

// TODO: if the user already belongs to a household, consider redirecting
// straight to index.html instead of showing this page at all. Requires
// calling DormhubAPI.getMyHouseholds() on load.
