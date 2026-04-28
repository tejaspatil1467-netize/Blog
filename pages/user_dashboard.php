<?php
// ============================================================
//  TechSync Blog — User Dashboard
//  pages/user_dashboard.php
// ============================================================
session_start();
require_once '../config/db.php';

// ── Authentication Guard ─────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=Please+login+to+access+your+dashboard');
    exit;
}
// Admin should not be here
if ($_SESSION['role'] === 'admin') {
    header('Location: admin_dashboard.php');
    exit;
}

$user_id  = $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username']);
$error    = htmlspecialchars($_GET['error']   ?? '');
$success  = htmlspecialchars($_GET['success'] ?? '');

// ── Fetch User's Own Blogs ────────────────────────────────────
$stmt = mysqli_prepare(
    $conn,
    "SELECT id, title, status, created_at FROM blogs WHERE user_id = ? ORDER BY created_at DESC"
);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$blogs_result = mysqli_stmt_get_result($stmt);
$blogs = mysqli_fetch_all($blogs_result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// ── Blog Counts ───────────────────────────────────────────────
$total    = count($blogs);
$pending  = count(array_filter($blogs, fn($b) => $b['status'] === 'pending'));
$approved = count(array_filter($blogs, fn($b) => $b['status'] === 'approved'));
$rejected = count(array_filter($blogs, fn($b) => $b['status'] === 'rejected'));
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Dashboard — TechSync</title>
  <meta name="description" content="Your personal TechSync blog dashboard." />
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
      <a href="../index.php" class="btn btn-ghost">Feed</a>
      <a href="write_blog.php" class="btn btn-ghost" id="write-btn">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        Write
      </a>
      <a href="../logout.php" class="btn btn-outline" id="logout-btn">Sign Out</a>
    </div>
  </div>
</nav>

<!-- ── Dashboard ─────────────────────────────────────────────── -->
<main class="dashboard-page" id="main-content">
  <div class="container">

    <!-- Header -->
    <div class="dashboard-header">
      <div>
        <h1>My Dashboard</h1>
        <p class="dashboard-subtitle">Welcome back, <strong><?= $username ?></strong> 👋</p>
      </div>
      <a href="write_blog.php" class="btn btn-primary" id="write-new-btn">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        New Article
      </a>
    </div>

    <!-- Flash Messages -->
    <?php if ($error): ?>
      <div class="flash-msg flash-error" role="alert">⚠️ <?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="flash-msg flash-success" role="alert">✅ <?= $success ?></div>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div class="stat-cards" aria-label="Blog statistics">
      <div class="stat-card">
        <div class="stat-number"><?= $total ?></div>
        <div class="stat-label">Total Blogs</div>
      </div>
      <div class="stat-card stat-card--pending">
        <div class="stat-number"><?= $pending ?></div>
        <div class="stat-label">Pending Review</div>
      </div>
      <div class="stat-card stat-card--approved">
        <div class="stat-number"><?= $approved ?></div>
        <div class="stat-label">Published</div>
      </div>
      <div class="stat-card stat-card--rejected">
        <div class="stat-number"><?= $rejected ?></div>
        <div class="stat-label">Rejected</div>
      </div>
    </div>

    <!-- Blog Table -->
    <div class="dashboard-section">
      <h2 class="section-title">My Articles</h2>

      <?php if (empty($blogs)): ?>
        <div class="empty-state">
          <div class="empty-icon">✍️</div>
          <h3>No articles yet</h3>
          <p>Start writing your first article and share it with the community.</p>
          <a href="write_blog.php" class="btn btn-primary" style="margin-top:16px;">Write Your First Article</a>
        </div>
      <?php else: ?>
        <div class="table-wrapper">
          <table class="dashboard-table" aria-label="My blogs table">
            <thead>
              <tr>
                <th>#</th>
                <th>Title</th>
                <th>Status</th>
                <th>Submitted</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($blogs as $i => $blog): ?>
                <tr>
                  <td><?= $i + 1 ?></td>
                  <td class="blog-title-cell">
                    <?php if ($blog['status'] === 'approved'): ?>
                      <a href="blog_detail.php?id=<?= $blog['id'] ?>" style="color:var(--link);">
                        <?= htmlspecialchars($blog['title']) ?>
                      </a>
                    <?php else: ?>
                      <?= htmlspecialchars($blog['title']) ?>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php
                      $status = $blog['status'];
                      $badge  = match($status) {
                          'approved' => 'badge-approved',
                          'rejected' => 'badge-rejected',
                          default    => 'badge-pending',
                      };
                    ?>
                    <span class="badge <?= $badge ?>">
                      <?= ucfirst($status) ?>
                    </span>
                  </td>
                  <td class="date-cell">
                    <?= date('d M Y', strtotime($blog['created_at'])) ?>
                  </td>
                  <td>
                    <?php if ($blog['status'] === 'approved'): ?>
                      <a href="blog_detail.php?id=<?= $blog['id'] ?>" class="btn btn-ghost" style="font-size:0.8rem;height:28px;padding:0 12px;">View</a>
                    <?php else: ?>
                      <span style="color:var(--text-muted); font-size:0.8rem;">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </div>
</main>

<!-- ── Footer ─────────────────────────────────────────────────── -->
<footer class="site-footer" role="contentinfo">
  <div class="container">
    <div class="footer-links">
      <a href="../index.php">Home</a>
      <a href="write_blog.php">Write</a>
      <a href="../logout.php">Sign Out</a>
    </div>
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

  // Auto-hide flash messages after 5 seconds
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
