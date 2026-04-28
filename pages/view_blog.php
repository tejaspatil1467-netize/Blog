<?php
// ============================================================
//  TechSync Blog — Admin: View Full Blog
//  pages/view_blog.php
//
//  Allows admin to read complete blog content before deciding
//  to Approve or Reject. Accessible to admin only.
// ============================================================
session_start();
require_once '../config/db.php';

// ── Admin Guard ───────────────────────────────────────────────
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php?error=Admin+access+required');
    exit;
}

// ── Validate Blog ID ──────────────────────────────────────────
$blog_id = intval($_GET['id'] ?? 0);
if ($blog_id <= 0) {
    header('Location: admin_dashboard.php?error=Invalid+blog+ID');
    exit;
}

// ── Fetch Blog (any status — admin can view all) ──────────────
$stmt = mysqli_prepare(
    $conn,
    "SELECT b.id, b.title, b.content, b.status, b.created_at, u.username
     FROM blogs b
     JOIN users u ON b.user_id = u.id
     WHERE b.id = ?"
);
mysqli_stmt_bind_param($stmt, 'i', $blog_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$blog   = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$blog) {
    header('Location: admin_dashboard.php?error=Blog+not+found');
    exit;
}

// ── Prepare display values ────────────────────────────────────
$title        = htmlspecialchars($blog['title']);
$author       = htmlspecialchars($blog['username']);
$status       = $blog['status'];
$date         = date('d M Y, h:i A', strtotime($blog['created_at']));
$avatar_char  = strtoupper(substr($blog['username'], 0, 2));
$word_count   = str_word_count(strip_tags($blog['content']));
$read_time    = max(1, round($word_count / 200)) . ' min read';

// Convert content for display:
// 1. Escape HTML special chars to prevent XSS
// 2. Convert newlines to <br> so paragraphs and line breaks render
$content_html = nl2br(htmlspecialchars($blog['content'], ENT_QUOTES, 'UTF-8'));

// Badge class based on status
$badge_class = match($status) {
    'approved' => 'badge-approved',
    'rejected' => 'badge-rejected',
    default    => 'badge-pending',
};

$flash_error   = htmlspecialchars($_GET['error']   ?? '');
$flash_success = htmlspecialchars($_GET['success'] ?? '');
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>View Blog — <?= $title ?> | TechSync Admin</title>
  <meta name="description" content="Admin view of blog submission: <?= $title ?>" />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='%23111827'/><text x='6' y='23' font-family='monospace' font-size='20' fill='white'>A</text></svg>" />
</head>
<body>

<!-- ── Navbar ──────────────────────────────────────────────── -->
<nav class="navbar" role="navigation" aria-label="Main navigation">
  <div class="container">
    <a class="nav-logo" href="../index.php" aria-label="TechSync Home">
      <div class="logo-icon" style="background: linear-gradient(135deg, #2563eb, #7c3aed);" aria-hidden="true">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M4 6h16M4 12h10M4 18h14" stroke="white" stroke-width="2.5" stroke-linecap="round" fill="none"/>
        </svg>
      </div>
      <span class="logo-text">TechSync</span>
      <span class="admin-badge">Admin</span>
    </a>
    <div class="nav-actions">
      <button class="btn-icon" id="theme-toggle" aria-label="Toggle dark mode"></button>
      <a href="admin_dashboard.php" class="btn btn-ghost">← Dashboard</a>
      <a href="../logout.php" class="btn btn-outline">Sign Out</a>
    </div>
  </div>
</nav>

<!-- ── View Blog Page ─────────────────────────────────────────── -->
<main class="view-blog-page" id="main-content">
  <div class="view-blog-container">

    <!-- Flash Messages -->
    <?php if ($flash_error): ?>
      <div class="flash-msg flash-error" role="alert">⚠️ <?= $flash_error ?></div>
    <?php endif; ?>
    <?php if ($flash_success): ?>
      <div class="flash-msg flash-success" role="alert">✅ <?= $flash_success ?></div>
    <?php endif; ?>

    <!-- ── Admin Action Bar ─────────────────────────────────── -->
    <div class="view-blog-actions-bar">
      <a href="admin_dashboard.php" class="btn btn-ghost" id="back-btn">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
        </svg>
        Back to Dashboard
      </a>

      <div class="view-blog-action-btns">
        <span class="badge <?= $badge_class ?>" style="font-size:0.8rem; padding:6px 14px;">
          <?= ucfirst($status) ?>
        </span>

        <?php if ($status === 'pending' || $status === 'rejected'): ?>
          <!-- Approve Button -->
          <form action="../actions/approve_blog.php" method="POST" style="display:inline;">
            <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>">
            <button type="submit" class="btn btn-approve" id="approve-btn"
                    onclick="return confirm('Approve this blog and publish it?')">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
              Approve & Publish
            </button>
          </form>
        <?php endif; ?>

        <?php if ($status === 'pending' || $status === 'approved'): ?>
          <!-- Reject Button -->
          <form action="../actions/reject_blog.php" method="POST" style="display:inline;">
            <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>">
            <button type="submit" class="btn btn-reject" id="reject-btn"
                    onclick="return confirm('Reject this blog submission?')">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
              Reject
            </button>
          </form>
        <?php endif; ?>
      </div>
    </div>

    <!-- ── Blog Meta ────────────────────────────────────────── -->
    <div class="view-blog-header">
      <h1 class="view-blog-title"><?= $title ?></h1>

      <div class="view-blog-meta">
        <div class="avatar lg" aria-hidden="true"><?= $avatar_char ?></div>
        <div class="view-blog-meta-info">
          <span class="view-blog-author">By <strong><?= $author ?></strong></span>
          <span class="view-blog-date"><?= $date ?> · <?= $read_time ?> · <?= $word_count ?> words</span>
        </div>
      </div>
    </div>

    <!-- ── Full Blog Content ─────────────────────────────────── -->
    <!-- nl2br() preserves all line breaks and paragraphs exactly as written -->
    <div class="view-blog-content" id="blog-full-content">
      <?= $content_html ?>
    </div>

    <!-- ── Bottom Action Bar (repeat for convenience) ────────── -->
    <div class="view-blog-bottom-actions">
      <?php if ($status === 'pending' || $status === 'rejected'): ?>
        <form action="../actions/approve_blog.php" method="POST" style="display:inline;">
          <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>">
          <button type="submit" class="btn btn-approve"
                  onclick="return confirm('Approve this blog and publish it?')">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            Approve & Publish
          </button>
        </form>
      <?php endif; ?>

      <?php if ($status === 'pending' || $status === 'approved'): ?>
        <form action="../actions/reject_blog.php" method="POST" style="display:inline;">
          <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>">
          <button type="submit" class="btn btn-reject"
                  onclick="return confirm('Reject this blog submission?')">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
            Reject
          </button>
        </form>
      <?php endif; ?>

      <a href="admin_dashboard.php" class="btn btn-ghost">← Back to Dashboard</a>
    </div>

  </div>
</main>

<!-- ── Footer ─────────────────────────────────────────────────── -->
<footer class="site-footer" role="contentinfo">
  <div class="container">
    <div class="footer-links">
      <a href="admin_dashboard.php">Dashboard</a>
      <a href="../index.php">View Site</a>
      <a href="../logout.php">Sign Out</a>
    </div>
    <p>© 2026 TechSync — Admin Panel</p>
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

  // ── Auto-hide flash messages ──────────────────────────────────
  setTimeout(() => {
    document.querySelectorAll('.flash-msg').forEach(el => {
      el.style.opacity = '0';
      el.style.transition = 'opacity 0.5s';
      setTimeout(() => el.remove(), 500);
    });
  }, 5000);
</script>
</body>
</html>
