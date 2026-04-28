<?php
// ============================================================
//  TechSync Blog — Write Blog Page
//  pages/write_blog.php
// ============================================================
session_start();
require_once '../config/db.php';

// ── Authentication Guard ─────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=Please+login+to+write+a+blog');
    exit;
}
if ($_SESSION['role'] === 'admin') {
    header('Location: admin_dashboard.php');
    exit;
}

$username = htmlspecialchars($_SESSION['username']);
$error    = htmlspecialchars($_GET['error']   ?? '');
$success  = htmlspecialchars($_GET['success'] ?? '');
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Write — TechSync</title>
  <meta name="description" content="Share your knowledge with the developer community on TechSync." />
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
      <a href="user_dashboard.php" class="btn btn-ghost">← Dashboard</a>
      <a href="../logout.php" class="btn btn-outline" id="logout-btn">Sign Out</a>
    </div>
  </div>
</nav>

<!-- ── Write Page ─────────────────────────────────────────────── -->
<main class="write-page fade-up" id="main-content">

  <div class="write-header">
    <h1>Write a New Article</h1>
    <p>Hello, <strong><?= $username ?></strong>! Share your knowledge with developers on TechSync.</p>
  </div>

  <!-- Flash Messages -->
  <?php if ($error): ?>
    <div class="flash-msg flash-error" role="alert" style="margin-bottom:20px;">⚠️ <?= $error ?></div>
  <?php endif; ?>

  <form class="write-form" id="write-form" action="../actions/submit_blog.php" method="POST" aria-label="Write blog form">

    <!-- Title -->
    <div class="form-group">
      <label class="form-label" for="title-input">Article Title *</label>
      <input
        type="text"
        id="title-input"
        name="title"
        class="form-input title-input"
        placeholder="What's your article about?"
        maxlength="255"
        required
        aria-required="true"
      />
    </div>

    <!-- Content with Toolbar -->
    <div class="form-group">
      <label class="form-label">Content *
        <span id="word-count" style="font-weight:400; color:var(--text-muted); margin-left:8px; text-transform:none;">0 words</span>
      </label>

      <div class="editor-toolbar" role="toolbar" aria-label="Formatting toolbar">
        <button type="button" class="toolbar-btn" data-action="h2" title="Heading 2" aria-label="Insert H2">H2</button>
        <button type="button" class="toolbar-btn" data-action="h3" title="Heading 3" aria-label="Insert H3">H3</button>
        <div class="toolbar-divider" role="separator"></div>
        <button type="button" class="toolbar-btn" data-action="bold" title="Bold" aria-label="Bold"><strong>B</strong></button>
        <button type="button" class="toolbar-btn" data-action="italic" title="Italic" aria-label="Italic"><em>I</em></button>
        <div class="toolbar-divider" role="separator"></div>
        <button type="button" class="toolbar-btn" data-action="code" title="Inline code" aria-label="Inline code">`</button>
        <button type="button" class="toolbar-btn" data-action="codeblock" title="Code block" aria-label="Code block">{ }</button>
      </div>

      <textarea
        id="content-input"
        name="content"
        class="form-textarea content-textarea"
        placeholder="Write your article here... You can use Markdown formatting.

## Introduction
Start with a hook that grabs the reader's attention.

## Main Content
Dive deep with examples, code snippets, and clear explanations.

## Conclusion
Summarize key takeaways."
        aria-label="Article content"
        required
        aria-required="true"
      ></textarea>
    </div>

    <!-- Info bar -->
    <div class="publish-bar" role="region" aria-label="Publish actions">
      <p>
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:-2px;margin-right:4px;">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        Your blog will be reviewed by an admin before going public.
      </p>
      <button type="submit" class="btn-publish" id="publish-btn" aria-label="Submit article for review">
        Submit for Review →
      </button>
    </div>

  </form>

</main>

<!-- ── Footer ─────────────────────────────────────────────────── -->
<footer class="site-footer" role="contentinfo">
  <div class="container">
    <div class="footer-links">
      <a href="../index.php">Home</a>
      <a href="user_dashboard.php">Dashboard</a>
    </div>
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
           <line x1="12" y1="21" x2="12" y2="23"/><line x1="1" y1="12" x2="3" y2="12"/>
           <line x1="21" y1="12" x2="23" y2="12"/>
         </svg>`
      : `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
           <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
         </svg>`;
  }

  // ── Word Counter ──────────────────────────────────────────────
  const textarea  = document.getElementById('content-input');
  const wordCount = document.getElementById('word-count');

  textarea.addEventListener('input', () => {
    const words = textarea.value.trim().split(/\s+/).filter(w => w.length > 0).length;
    wordCount.textContent = words + ' word' + (words !== 1 ? 's' : '');
  });

  // ── Toolbar Formatting ────────────────────────────────────────
  document.querySelectorAll('.toolbar-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const action = btn.dataset.action;
      const start  = textarea.selectionStart;
      const end    = textarea.selectionEnd;
      const sel    = textarea.value.substring(start, end);
      let insert   = '';

      switch (action) {
        case 'h2':        insert = `## ${sel || 'Heading'}`; break;
        case 'h3':        insert = `### ${sel || 'Heading'}`; break;
        case 'bold':      insert = `**${sel || 'bold text'}**`; break;
        case 'italic':    insert = `*${sel || 'italic text'}*`; break;
        case 'code':      insert = `\`${sel || 'code'}\``; break;
        case 'codeblock': insert = `\`\`\`\n${sel || '// code here'}\n\`\`\``; break;
        default:          insert = sel;
      }

      textarea.setRangeText(insert, start, end, 'end');
      textarea.focus();
    });
  });
</script>
</body>
</html>
