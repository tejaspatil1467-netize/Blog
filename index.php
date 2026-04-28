<?php
// ============================================================
//  TechSync Blog — Public Homepage
//  index.php
//
//  Displays only APPROVED blogs. Smart navbar based on session.
// ============================================================
session_start();
require_once 'config/db.php';

// ── Fetch Approved Blogs ──────────────────────────────────────
$result = mysqli_query(
    $conn,
    "SELECT b.id, b.title, b.content, b.created_at, u.username
     FROM blogs b
     JOIN users u ON b.user_id = u.id
     WHERE b.status = 'approved'
     ORDER BY b.created_at DESC"
);
$blogs = mysqli_fetch_all($result, MYSQLI_ASSOC);

// ── Session info ──────────────────────────────────────────────
$logged_in = isset($_SESSION['user_id']);
$is_admin  = $logged_in && $_SESSION['role'] === 'admin';
$username  = $logged_in ? htmlspecialchars($_SESSION['username']) : '';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TechSync — A Blog for Developers</title>
  <meta name="description" content="Discover high-quality articles on Java, React, AI, DevOps, System Design and more — written by developers, for developers." />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='%23111827'/><text x='6' y='23' font-family='monospace' font-size='20' fill='white'>T</text></svg>" />
</head>
<body>

<!-- ── Navbar ──────────────────────────────────────────────── -->
<nav class="navbar" role="navigation" aria-label="Main navigation">
  <div class="container">

    <a class="nav-logo" href="index.php" id="logo-link" aria-label="TechSync Home">
      <div class="logo-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M4 6h16M4 12h10M4 18h14" stroke="white" stroke-width="2.5" stroke-linecap="round" fill="none"/>
        </svg>
      </div>
      <span class="logo-text">TechSync</span>
    </a>

    <div class="nav-search" role="search">
      <span class="search-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
      </span>
      <input type="search" id="nav-search" placeholder="Search articles, authors…"
             aria-label="Search blogs" autocomplete="off" />
    </div>

    <div class="nav-actions">
      <button class="btn-icon" id="theme-toggle" aria-label="Toggle dark mode"></button>

      <?php if ($logged_in): ?>
        <?php if ($is_admin): ?>
          <!-- Admin nav -->
          <a href="pages/admin_dashboard.php" class="btn btn-ghost" id="dashboard-btn">Dashboard</a>
          <a href="logout.php" class="btn btn-outline" id="logout-btn">Sign Out</a>
        <?php else: ?>
          <!-- User nav -->
          <a href="pages/write_blog.php" class="btn btn-ghost" id="write-btn" aria-label="Write a blog">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Write
          </a>
          <a href="pages/user_dashboard.php" class="btn btn-ghost" id="my-blogs-btn"><?= $username ?></a>
          <a href="logout.php" class="btn btn-outline" id="logout-btn">Sign Out</a>
        <?php endif; ?>
      <?php else: ?>
        <!-- Guest nav -->
        <a href="pages/write_blog.php" class="btn btn-ghost" id="write-btn" aria-label="Write a blog">
          <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
          </svg>
          Write
        </a>
        <a href="pages/login.php" class="btn btn-primary" id="login-btn-nav" aria-label="Login">Sign In</a>
      <?php endif; ?>
    </div>

  </div>
</nav>

<!-- ── Hero ──────────────────────────────────────────────────── -->
<section class="hero fade-up" aria-label="Hero section">
  <div class="container">
    <div class="hero-label">
      <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
      </svg>
      Built for developers
    </div>
    <h1>Ideas Worth <span>Reading</span></h1>
    <p>Curated articles on Java, React, AI, DevOps, and System Design — written by engineers who ship.</p>
  </div>
</section>

<!-- ── Blog Grid ─────────────────────────────────────────────── -->
<main class="blog-grid-section" id="main-content">
  <div class="container">

    <?php if (empty($blogs)): ?>
      <div class="no-results" style="grid-column:1/-1; text-align:center; padding:80px 0;">
        <div class="emoji">📝</div>
        <h3>No articles published yet</h3>
        <p style="color:var(--text-muted); margin-top:8px;">
          <?php if ($logged_in && !$is_admin): ?>
            <a href="pages/write_blog.php" class="btn btn-primary" style="margin-top:16px;">Write the First Article</a>
          <?php else: ?>
            Check back soon — articles are reviewed by our admin before publishing.
          <?php endif; ?>
        </p>
      </div>
    <?php else: ?>
      <div class="blog-grid" id="blog-grid" aria-label="Blog posts" role="list">
        <?php foreach ($blogs as $blog):
          $avatar  = strtoupper(substr($blog['username'], 0, 2));
          $preview = mb_substr(strip_tags($blog['content']), 0, 160);
          if (strlen($blog['content']) > 160) $preview .= '…';
          $date    = date('d M Y', strtotime($blog['created_at']));
          // Gradient colors cycle
          $gradients = [
            'linear-gradient(90deg,#2563eb,#7c3aed)',
            'linear-gradient(90deg,#0891b2,#2563eb)',
            'linear-gradient(90deg,#7c3aed,#db2777)',
            'linear-gradient(90deg,#059669,#0891b2)',
            'linear-gradient(90deg,#d97706,#ef4444)',
          ];
          static $idx = 0;
          $grad = $gradients[$idx % count($gradients)];
          $idx++;
        ?>
          <article class="blog-card" role="listitem"
                   onclick="window.location='pages/blog_detail.php?id=<?= $blog['id'] ?>'"
                   style="cursor:pointer;"
                   aria-label="<?= htmlspecialchars($blog['title']) ?>">
            <div class="card-cover" style="background: <?= $grad ?>;"></div>
            <div class="card-body">
              <div class="card-meta">
                <div class="avatar" aria-hidden="true"><?= $avatar ?></div>
                <span class="author-name"><?= htmlspecialchars($blog['username']) ?></span>
                <span class="post-date"><?= $date ?></span>
              </div>
              <h2 class="card-title"><?= htmlspecialchars($blog['title']) ?></h2>
              <p class="card-excerpt"><?= htmlspecialchars($preview) ?></p>
              <div class="card-footer">
                <span></span>
                <span class="read-time">
                  <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                  </svg>
                  <?= max(1, round(str_word_count(strip_tags($blog['content'])) / 200)) ?> min read
                </span>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <!-- Client-side search filter overlay -->
      <div id="search-results" class="blog-grid" style="display:none;" aria-label="Search results" role="list"></div>
      <div id="no-search-results" class="no-results" style="display:none;">
        <div class="emoji">🔍</div>
        <h3>No results found</h3>
        <p>Try a different search term.</p>
      </div>
    <?php endif; ?>

  </div>
</main>

<!-- ── Footer ─────────────────────────────────────────────────── -->
<footer class="site-footer" role="contentinfo">
  <div class="container">
    <div class="footer-links">
      <a href="index.php">Home</a>
      <?php if ($logged_in): ?>
        <a href="pages/<?= $is_admin ? 'admin_dashboard' : 'user_dashboard' ?>.php">Dashboard</a>
        <a href="logout.php">Sign Out</a>
      <?php else: ?>
        <a href="pages/write_blog.php">Write</a>
        <a href="pages/login.php">Sign In</a>
      <?php endif; ?>
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

  // ── Client-side Search (filters visible cards) ────────────────
  const searchInput   = document.getElementById('nav-search');
  const blogGrid      = document.getElementById('blog-grid');
  const noResults     = document.getElementById('no-search-results');

  if (searchInput && blogGrid) {
    searchInput.addEventListener('input', () => {
      const query = searchInput.value.toLowerCase().trim();
      const cards = blogGrid.querySelectorAll('.blog-card');
      let visible = 0;

      cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        const show = !query || text.includes(query);
        card.style.display = show ? '' : 'none';
        if (show) visible++;
      });

      noResults.style.display = (query && visible === 0) ? 'block' : 'none';
    });
  }
</script>
</body>
</html>
