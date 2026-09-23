document.getElementById("login-form").addEventListener("submit", async (e) => {
  e.preventDefault();
  const email = document.getElementById("login-email").value;
  const password = document.getElementById("login-password").value;
  const errorEl = document.getElementById("login-error");
  errorEl.textContent = "";

  // TODO:
  // - Call DormhubAPI.login(email, password)
  // - On success, redirect to household.html if the user has no household
  //   yet, or index.html if they do (may need a call to getMyHouseholds()
  //   to decide, or the login response could include this)
  // - On failure, show err.message in errorEl
});

document.getElementById("register-form").addEventListener("submit", async (e) => {
  e.preventDefault();
  const fullName = document.getElementById("register-name").value;
  const email = document.getElementById("register-email").value;
  const password = document.getElementById("register-password").value;
  const errorEl = document.getElementById("register-error");
  errorEl.textContent = "";

  // TODO:
  // - Call DormhubAPI.register(fullName, email, password)
  // - Then log the new user in (DormhubAPI.login) and redirect to
  //   household.html (new users won't have a household yet)
  // - On failure, show err.message in errorEl
});
