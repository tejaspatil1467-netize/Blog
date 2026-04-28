<?php
// ============================================================
//  TechSync Blog — Blog Detail Page
//  pages/blog_detail.php?id=X
// ============================================================
session_start();
require_once '../config/db.php';

// ── Validate Blog ID ──────────────────────────────────────────
$blog_id = intval($_GET['id'] ?? 0);
if ($blog_id <= 0) {
    header('Location: ../index.php');
    exit;
}

// ── Fetch Blog (only approved) ────────────────────────────────
$stmt = mysqli_prepare(
    $conn,
    "SELECT b.id, b.title, b.content, b.created_at, u.username
     FROM blogs b
     JOIN users u ON b.user_id = u.id
     WHERE b.id = ? AND b.status = 'approved'"
);
mysqli_stmt_bind_param($stmt, 'i', $blog_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$blog   = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// If not found or not approved, go back to homepage
if (!$blog) {
    header('Location: ../index.php?error=Blog+not+found');
    exit;
}

// ── Fetch Related Blogs (other approved, excluding current) ───
$rel_result = mysqli_query(
    $conn,
    "SELECT b.id, b.title, b.created_at, u.username
     FROM blogs b
     JOIN users u ON b.user_id = u.id
     WHERE b.status = 'approved' AND b.id != {$blog_id}
     ORDER BY b.created_at DESC
     LIMIT 3"
);
$related = mysqli_fetch_all($rel_result, MYSQLI_ASSOC);

// ── Render markdown-like content ─────────────────────────────
// Simple server-side markdown → HTML conversion
function renderMarkdown(string $text): string {
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    // Code blocks (must come before inline code)
    $text = preg_replace('/```(.*?)```/s', '<pre><code>$1</code></pre>', $text);
    // Inline code
    $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
    // Headings
    $text = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^## (.+)$/m',  '<h2>$1</h2>', $text);
    // Bold / Italic
    $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.+?)\*/',     '<em>$1</em>', $text);
    // Unordered lists
    $text = preg_replace('/^- (.+)$/m', '<li>$1</li>', $text);
    $text = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $text);
    // Paragraphs — wrap lines separated by blank lines
    $paragraphs = preg_split('/\n{2,}/', $text);
    $html = '';
    foreach ($paragraphs as $p) {
        $p = trim($p);
        if ($p === '') continue;
        // Don't wrap headings, pre, ul in <p>
        if (preg_match('/^<(h[2-3]|pre|ul)/', $p)) {
            $html .= $p . "\n";
        } else {
            $html .= '<p>' . nl2br($p) . '</p>' . "\n";
        }
    }
    return $html;
}

$title       = htmlspecialchars($blog['title']);
$author      = htmlspecialchars($blog['username']);
$date        = date('d M Y', strtotime($blog['created_at']));
$avatar_char = strtoupper(substr($blog['username'], 0, 2));
$content_html = renderMarkdown($blog['content']);

// Estimate read time (~200 words/min)
$word_count = str_word_count(strip_tags($blog['content']));
$read_time  = max(1, round($word_count / 200)) . ' min read';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $title ?> — TechSync</title>
  <meta name="description" content="<?= htmlspecialchars(mb_substr(strip_tags($blog['content']), 0, 160)) ?>" />
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
      <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($_SESSION['role'] === 'admin'): ?>
          <a href="admin_dashboard.php" class="btn btn-ghost">Dashboard</a>
        <?php else: ?>
          <a href="write_blog.php" class="btn btn-ghost">Write</a>
          <a href="user_dashboard.php" class="btn btn-ghost"><?= htmlspecialchars($_SESSION['username']) ?></a>
        <?php endif; ?>
        <a href="../logout.php" class="btn btn-outline">Sign Out</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-primary">Sign In</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- ── Blog Detail ─────────────────────────────────────────── -->
<main class="blog-detail-page fade-in" id="main-content">

  <!-- Back Button -->
  <button class="blog-back" onclick="history.length > 1 ? history.back() : window.location='../index.php'" aria-label="Go back">
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
      <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
    </svg>
    Back to blogs
  </button>

  <!-- Title -->
  <h1 class="blog-detail-title" id="blog-title"><?= $title ?></h1>

  <!-- Author Row -->
  <div class="author-row" role="contentinfo" aria-label="Author info">
    <div class="avatar lg" aria-hidden="true"><?= $avatar_char ?></div>
    <div class="author-info">
      <h4 id="blog-author-name"><?= $author ?></h4>
      <p id="blog-author-bio">TechSync Author</p>
    </div>
    <div class="author-meta">
      <span id="blog-date"><?= $date ?></span>
      <span id="blog-read-time"><?= $read_time ?></span>
    </div>
  </div>

  <!-- Content -->
  <article class="blog-content" id="blog-content" aria-label="Blog content">
    <?= $content_html ?>
  </article>

  <!-- Like Section (UI only for college project) -->
  <div class="like-section" aria-label="Like this post">
    <button class="btn-like" id="like-btn" aria-label="Like this post" aria-pressed="false">
      <svg class="heart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
      </svg>
      <span id="like-count">0</span> Likes
    </button>
  </div>

</main>

<!-- ── Related Posts ─────────────────────────────────────────── -->
<?php if (!empty($related)): ?>
<section style="background:var(--bg-secondary); padding:48px 0; margin-top:0;" aria-label="Related posts">
  <div class="container">
    <h2 style="font-size:1.25rem; font-weight:700; margin-bottom:24px; letter-spacing:-0.02em;">
      More articles you might like
    </h2>
    <div class="blog-grid">
      <?php foreach ($related as $rel): ?>
        <a href="blog_detail.php?id=<?= $rel['id'] ?>" class="blog-card" style="text-decoration:none;" role="listitem">
          <div class="card-cover" style="background: linear-gradient(90deg,#2563eb,#7c3aed);"></div>
          <div class="card-body">
            <div class="card-meta">
              <div class="avatar"><?= strtoupper(substr($rel['username'], 0, 2)) ?></div>
              <span class="author-name"><?= htmlspecialchars($rel['username']) ?></span>
              <span class="post-date"><?= date('d M', strtotime($rel['created_at'])) ?></span>
            </div>
            <h3 class="card-title"><?= htmlspecialchars($rel['title']) ?></h3>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── Footer ─────────────────────────────────────────────────── -->
<footer class="site-footer" role="contentinfo">
  <div class="container">
    <div class="footer-links">
      <a href="../index.php">Home</a>
      <a href="write_blog.php">Write</a>
      <a href="login.php">Sign In</a>
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

  // ── Like Button (UI only) ─────────────────────────────────────
  const likeBtn   = document.getElementById('like-btn');
  const likeCount = document.getElementById('like-count');
  let liked = false, count = 0;

  likeBtn.addEventListener('click', () => {
    liked = !liked;
    count += liked ? 1 : -1;
    likeBtn.classList.toggle('liked', liked);
    likeBtn.setAttribute('aria-pressed', liked);
    likeCount.textContent = count;
  });
</script>
</body>
</html>
