<?php
// ============================================================
//  TechSync Blog — Register Page
//  pages/register.php
// ============================================================
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'));
    exit;
}

$error   = htmlspecialchars($_GET['error']   ?? '');
$success = htmlspecialchars($_GET['success'] ?? '');
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Account — TechSync</title>
  <meta name="description" content="Create your TechSync account and start sharing your developer articles." />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='%23111827'/><text x='6' y='23' font-family='monospace' font-size='20' fill='white'>T</text></svg>" />
</head>
<body>

<!-- ── Navbar ──────────────────────────────────────────────── -->
<nav class="navbar" role="navigation" aria-label="Main navigation">
  <div class="container">
    <a class="nav-logo" href="../index.php" aria-label="TechSync Home">
      <div class="logo-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M4 6h16M4 12h10M4 18h14" stroke="white" stroke-width="2.5" stroke-linecap="round" fill="none"/>
        </svg>
      </div>
      <span class="logo-text">TechSync</span>
    </a>
    <div class="nav-actions">
      <button class="btn-icon" id="theme-toggle" aria-label="Toggle dark mode"></button>
      <a href="login.php" class="btn btn-ghost">← Sign In</a>
    </div>
  </div>
</nav>

<!-- ── Auth Page ─────────────────────────────────────────────── -->
<main class="auth-page" id="main-content">
  <div class="auth-card fade-up">

    <!-- Logo -->
    <div class="auth-logo">
      <div class="logo-icon" style="width:44px;height:44px;" aria-hidden="true">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M4 6h16M4 12h10M4 18h14" stroke="white" stroke-width="2.5" stroke-linecap="round" fill="none"/>
        </svg>
      </div>
    </div>

    <h1 class="auth-title">Create an account</h1>
    <p class="auth-subtitle">Join TechSync and share your knowledge</p>

    <!-- Flash Messages -->
    <?php if ($error): ?>
      <div class="flash-msg flash-error" role="alert">⚠️ <?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="flash-msg flash-success" role="alert">✅ <?= $success ?></div>
    <?php endif; ?>

    <!-- Register Form -->
    <form class="auth-form" id="register-form" action="../actions/register_action.php" method="POST" aria-label="Register form">

      <div class="form-group">
        <label class="form-label" for="username-input">Username</label>
        <div class="form-input-group">
          <span class="input-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </span>
          <input
            type="text"
            id="username-input"
            name="username"
            class="form-input"
            placeholder="Choose a username (3–50 chars)"
            autocomplete="username"
            minlength="3"
            maxlength="50"
            required
            aria-required="true"
          />
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="password-input">Password</label>
        <div class="form-input-group">
          <span class="input-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
          </span>
          <input
            type="password"
            id="password-input"
            name="password"
            class="form-input"
            placeholder="At least 6 characters"
            autocomplete="new-password"
            minlength="6"
            required
            aria-required="true"
          />
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="confirm-password-input">Confirm Password</label>
        <div class="form-input-group">
          <span class="input-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
              <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
          </span>
          <input
            type="password"
            id="confirm-password-input"
            name="confirm_password"
            class="form-input"
            placeholder="Re-enter your password"
            autocomplete="new-password"
            required
            aria-required="true"
          />
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-full" id="register-btn" aria-label="Create account">
        Create Account
      </button>

    </form>

    <p class="auth-footer">
      Already have an account?
      <a href="login.php" style="color:var(--link); font-weight:600;">Sign in</a>
    </p>

  </div>
</main>

<!-- ── Footer ─────────────────────────────────────────────────── -->
<footer class="site-footer" role="contentinfo">
  <div class="container">
    <p>© 2026 TechSync — A space for developers to learn and share.</p>
  </div>
</footer>

<script>
  const html = document.documentElement;
  const themeBtn = document.getElementById('theme-toggle');
  const saved = localStorage.getItem('theme') || 'light';
  html.setAttribute('data-theme', saved);
  updateThemeIcon();

  themeBtn.addEventListener('click', () => {
    const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    updateThemeIcon();
  });

  function updateThemeIcon() {
    const isDark = html.getAttribute('data-theme') === 'dark';
    themeBtn.innerHTML = isDark
      ? `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
           <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/>
           <line x1="12" y1="21" x2="12" y2="23"/><line x1="1" y1="12" x2="3" y2="12"/>
           <line x1="21" y1="12" x2="23" y2="12"/>
         </svg>`
      : `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
           <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
         </svg>`;
  }

  // Client-side password match validation
  document.getElementById('register-form').addEventListener('submit', function (e) {
    const pw  = document.getElementById('password-input').value;
    const cpw = document.getElementById('confirm-password-input').value;
    if (pw !== cpw) {
      e.preventDefault();
      alert('Passwords do not match!');
    }
  });
</script>
</body>
</html>
