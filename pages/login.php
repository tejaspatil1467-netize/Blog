<?php
// ============================================================
//  TechSync Blog — Login Page
//  pages/login.php
// ============================================================
session_start();

// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: user_dashboard.php');
    }
    exit;
}

// Read flash messages from query string
$error   = htmlspecialchars($_GET['error']   ?? '');
$success = htmlspecialchars($_GET['success'] ?? '');
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign In — TechSync</title>
  <meta name="description" content="Sign in to TechSync to read, write, and share your developer articles." />
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
      <a href="../index.php" class="btn btn-ghost">← Home</a>
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

    <h1 class="auth-title">Welcome back</h1>
    <p class="auth-subtitle">Sign in to your TechSync account</p>

    <!-- Flash Messages -->
    <?php if ($error): ?>
      <div class="flash-msg flash-error" role="alert">⚠️ <?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="flash-msg flash-success" role="alert">✅ <?= $success ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form class="auth-form" id="login-form" action="../actions/login_action.php" method="POST" aria-label="Login form">

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
            placeholder="Your username"
            autocomplete="username"
            required
            aria-required="true"
          />
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="password-input">Password</label>
        <div class="form-input-group" style="position:relative;">
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
            placeholder="Enter your password"
            autocomplete="current-password"
            required
            aria-required="true"
          />
          <button type="button" class="toggle-password" id="toggle-password" aria-label="Show password">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-full" id="login-btn" aria-label="Sign in">
        Sign In
      </button>

    </form>

    <p class="auth-footer">
      Don't have an account?
      <a href="register.php" style="color:var(--link); font-weight:600;">Create one for free</a>
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
  // ── Theme Toggle ──────────────────────────────────────────────
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
           <line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
           <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/>
           <line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
           <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
         </svg>`
      : `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
           <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
         </svg>`;
  }

  // ── Password Visibility Toggle ────────────────────────────────
  document.getElementById('toggle-password').addEventListener('click', function () {
    const input = document.getElementById('password-input');
    input.type = input.type === 'password' ? 'text' : 'password';
  });
</script>
</body>
</html>
