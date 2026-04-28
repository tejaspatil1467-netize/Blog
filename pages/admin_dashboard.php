<?php
// ============================================================
//  TechSync Blog — Admin Dashboard
//  pages/admin_dashboard.php
// ============================================================
session_start();
require_once '../config/db.php';

// ── Admin Guard ───────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=Please+login+to+access+the+admin+dashboard');
    exit;
}
if ($_SESSION['role'] !== 'admin') {
    header('Location: user_dashboard.php');
    exit;
}

$admin_name = htmlspecialchars($_SESSION['username']);
$error      = htmlspecialchars($_GET['error']   ?? '');
$success    = htmlspecialchars($_GET['success'] ?? '');

// ── Fetch Summary Stats ───────────────────────────────────────
$stats_result = mysqli_query($conn,
    "SELECT
        COUNT(*) AS total,
        SUM(status = 'pending')  AS pending,
        SUM(status = 'approved') AS approved,
        SUM(status = 'rejected') AS rejected
     FROM blogs"
);
$stats = mysqli_fetch_assoc($stats_result);

// ── Fetch Pending Blogs (with author username) ─────────────────
$pending_result = mysqli_query($conn,
    "SELECT b.id, b.title, b.content, b.created_at, u.username
     FROM blogs b
     JOIN users u ON b.user_id = u.id
     WHERE b.status = 'pending'
     ORDER BY b.created_at ASC"
);
$pending_blogs = mysqli_fetch_all($pending_result, MYSQLI_ASSOC);

// ── Fetch Recent Approved/Rejected Blogs ──────────────────────
$reviewed_result = mysqli_query($conn,
    "SELECT b.id, b.title, b.status, b.created_at, u.username
     FROM blogs b
     JOIN users u ON b.user_id = u.id
     WHERE b.status IN ('approved','rejected')
     ORDER BY b.created_at DESC
     LIMIT 20"
);
$reviewed_blogs = mysqli_fetch_all($reviewed_result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard — TechSync</title>
  <meta name="description" content="TechSync admin panel — manage and approve blog submissions." />
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
      <a href="../index.php" class="btn btn-ghost">View Site</a>
      <a href="../logout.php" class="btn btn-outline" id="logout-btn">Sign Out</a>
    </div>
  </div>
</nav>

<!-- ── Admin Dashboard ───────────────────────────────────────── -->
<main class="dashboard-page" id="main-content">
  <div class="container">

    <!-- Header -->
    <div class="dashboard-header">
      <div>
        <h1>Admin Dashboard</h1>
        <p class="dashboard-subtitle">Logged in as <strong><?= $admin_name ?></strong> · Manage blog submissions</p>
      </div>
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
        <div class="stat-number"><?= intval($stats['total']) ?></div>
        <div class="stat-label">Total Blogs</div>
      </div>
      <div class="stat-card stat-card--pending">
        <div class="stat-number"><?= intval($stats['pending']) ?></div>
        <div class="stat-label">Pending Review</div>
      </div>
      <div class="stat-card stat-card--approved">
        <div class="stat-number"><?= intval($stats['approved']) ?></div>
        <div class="stat-label">Approved</div>
      </div>
      <div class="stat-card stat-card--rejected">
        <div class="stat-number"><?= intval($stats['rejected']) ?></div>
        <div class="stat-label">Rejected</div>
      </div>
    </div>

    <!-- ── Pending Blogs ────────────────────────────────────── -->
    <div class="dashboard-section">
      <h2 class="section-title">
        <span class="badge badge-pending" style="font-size:0.75rem;padding:4px 10px;">
          <?= count($pending_blogs) ?> Pending
        </span>
        Blogs Awaiting Review
      </h2>

      <?php if (empty($pending_blogs)): ?>
        <div class="empty-state">
          <div class="empty-icon">🎉</div>
          <h3>All caught up!</h3>
          <p>No blogs pending review right now.</p>
        </div>
      <?php else: ?>
        <div class="pending-list">
          <?php foreach ($pending_blogs as $blog):
            // ── Clean plain-text preview (150 chars) ─────────────
            $plain_content = strip_tags($blog['content']);
            $preview       = mb_substr($plain_content, 0, 150);
            $is_long       = mb_strlen($plain_content) > 150;
          ?>
            <div class="pending-card" id="blog-<?= $blog['id'] ?>">
              <div class="pending-card-header">
                <div>
                  <h3 class="pending-card-title"><?= htmlspecialchars($blog['title']) ?></h3>
                  <p class="pending-card-meta">
                    By <strong><?= htmlspecialchars($blog['username']) ?></strong>
                    · <?= date('d M Y, h:i A', strtotime($blog['created_at'])) ?>
                    · <?= max(1, round(str_word_count($plain_content) / 200)) ?> min read
                  </p>
                </div>
                <span class="badge badge-pending">Pending</span>
              </div>

              <!-- ── Content Preview (150 chars) ────────────────── -->
              <div class="pending-card-preview">
                <?= htmlspecialchars($preview) ?><?= $is_long ? '…' : '' ?>
              </div>

              <!-- ── Read More link ─────────────────────────────── -->
              <?php if ($is_long): ?>
                <a href="view_blog.php?id=<?= $blog['id'] ?>" target="_blank" class="read-more-link">
                  <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                  </svg>
                  Read Full Blog
                </a>
              <?php endif; ?>

              <div class="pending-card-actions">
                <!-- View Full Blog -->
                <a href="view_blog.php?id=<?= $blog['id'] ?>" target="_blank"
                   class="btn btn-view" id="view-<?= $blog['id'] ?>">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                    <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                  </svg>
                  View Full
                </a>
                <!-- Approve Form -->
                <form action="../actions/approve_blog.php" method="POST" style="display:inline;">
                  <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>">
                  <button type="submit" class="btn btn-approve" id="approve-<?= $blog['id'] ?>"
                          onclick="return confirm('Approve this blog and publish it?')">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                      <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Approve
                  </button>
                </form>
                <!-- Reject Form -->
                <form action="../actions/reject_blog.php" method="POST" style="display:inline;">
                  <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>">
                  <button type="submit" class="btn btn-reject" id="reject-<?= $blog['id'] ?>"
                          onclick="return confirm('Reject this blog submission?')">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                      <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                    Reject
                  </button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- ── Recently Reviewed ────────────────────────────────── -->
    <?php if (!empty($reviewed_blogs)): ?>
    <div class="dashboard-section">
      <h2 class="section-title">Recently Reviewed</h2>
      <div class="table-wrapper">
        <table class="dashboard-table" aria-label="Reviewed blogs table">
          <thead>
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>Author</th>
              <th>Status</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reviewed_blogs as $i => $blog): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td class="blog-title-cell"><?= htmlspecialchars($blog['title']) ?></td>
                <td><?= htmlspecialchars($blog['username']) ?></td>
                <td>
                  <span class="badge <?= $blog['status'] === 'approved' ? 'badge-approved' : 'badge-rejected' ?>">
                    <?= ucfirst($blog['status']) ?>
                  </span>
                </td>
                <td class="date-cell"><?= date('d M Y', strtotime($blog['created_at'])) ?></td>
                <td>
                  <?php if ($blog['status'] === 'approved'): ?>
                    <a href="blog_detail.php?id=<?= $blog['id'] ?>" class="btn btn-ghost" style="font-size:0.8rem;height:28px;padding:0 12px;">View</a>
                  <?php else: ?>
                    <!-- Re-approve a rejected blog -->
                    <form action="../actions/approve_blog.php" method="POST" style="display:inline;">
                      <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>">
                      <button type="submit" class="btn btn-ghost" style="font-size:0.8rem;height:28px;padding:0 12px;"
                              onclick="return confirm('Re-approve this blog?')">Re-approve</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

  </div>
</main>

<!-- ── Footer ─────────────────────────────────────────────────── -->
<footer class="site-footer" role="contentinfo">
  <div class="container">
    <div class="footer-links">
      <a href="../index.php">View Site</a>
      <a href="../logout.php">Sign Out</a>
    </div>
    <p>© 2026 TechSync — Admin Panel</p>
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

  // Auto-hide flash messages
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
