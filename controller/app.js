// controller/app.js — Main application controller

// ── Utilities ─────────────────────────────────────────────────────────────────
const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];

// ── Dark Mode ─────────────────────────────────────────────────────────────────
function initDarkMode() {
  const saved = localStorage.getItem('theme') || 'light';
  document.documentElement.setAttribute('data-theme', saved);

  const toggleBtn = $('#theme-toggle');
  if (!toggleBtn) return;

  updateThemeIcon(saved);

  toggleBtn.addEventListener('click', () => {
    const current = document.documentElement.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    updateThemeIcon(next);
  });
}

function updateThemeIcon(theme) {
  const btn = $('#theme-toggle');
  if (!btn) return;
  btn.innerHTML = theme === 'dark'
    ? `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
         <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
         <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
         <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
         <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
       </svg>`
    : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
         <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
       </svg>`;
  btn.setAttribute('aria-label', `Switch to ${theme === 'dark' ? 'light' : 'dark'} mode`);
}

// ── Router helpers ─────────────────────────────────────────────────────────────
function navigateTo(url) { window.location.href = url; }

function getQueryParam(name) {
  return new URLSearchParams(window.location.search).get(name);
}

// ── Tag color map ──────────────────────────────────────────────────────────────
const TAG_COLORS = {
  'Java':         '#e8543a',
  'Spring Boot':  '#6db33f',
  'React':        '#61dafb',
  'JavaScript':   '#f7df1e',
  'TypeScript':   '#3178c6',
  'Python':       '#3572a5',
  'AI':           '#7c3aed',
  'LLM':          '#a855f7',
  'Docker':       '#2496ed',
  'Kubernetes':   '#326ce5',
  'DevOps':       '#f59e0b',
  'CSS':          '#264de4',
  'Frontend':     '#e34c26',
  'Backend':      '#4caf50',
  'System Design':'#0f172a',
  'Architecture': '#374151',
  'Database':     '#336791',
  'PostgreSQL':   '#336791',
  'Git':          '#f05032',
  'DevTools':     '#6b7280',
  'Performance':  '#ef4444',
  'Scalability':  '#8b5cf6',
  'Functional':   '#ec4899',
  'Streams':      '#14b8a6',
  'REST API':     '#10b981',
  'Types':        '#6366f1',
  'Web Design':   '#f43f5e',
  'CS Fundamentals': '#374151',
};

function getTagStyle(tag) {
  const color = TAG_COLORS[tag];
  if (!color) return '';
  return `background: ${color}18; color: ${color}; border: 1px solid ${color}30;`;
}

// ── Render tag badge ───────────────────────────────────────────────────────────
function renderTag(tag) {
  return `<span class="tag" style="${getTagStyle(tag)}">${tag}</span>`;
}

// ── Blog card HTML ─────────────────────────────────────────────────────────────
function renderBlogCard(blog) {
  const tagsHtml = blog.tags.slice(0, 3).map(renderTag).join('');
  return `
    <article class="blog-card" role="button" tabindex="0"
             aria-label="Read: ${blog.title}"
             onclick="openBlog(${blog.id})"
             onkeydown="if(event.key==='Enter')openBlog(${blog.id})">
      <div class="card-cover" style="background: ${blog.coverColor};"></div>
      <div class="card-body">
        <div class="card-meta">
          <div class="avatar" aria-hidden="true">${blog.authorAvatar}</div>
          <span class="author-name">${blog.author}</span>
          <span class="post-date">${blog.date}</span>
        </div>
        <h2 class="card-title">${blog.title}</h2>
        <p class="card-excerpt">${blog.excerpt}</p>
        <div class="card-footer">
          <div class="card-tags">${tagsHtml}</div>
          <span class="read-time">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            ${blog.readTime}
          </span>
        </div>
      </div>
    </article>`;
}

// ── Open blog ──────────────────────────────────────────────────────────────────
function openBlog(id) {
  // Store current scroll position
  sessionStorage.setItem('homeScroll', window.scrollY);
  navigateTo(`../view/blog.html?id=${id}`);
}

// ─────────────────────────────────────────────────────────────────────────────
//  HOME PAGE
// ─────────────────────────────────────────────────────────────────────────────
function initHomePage() {
  const grid = $('#blog-grid');
  if (!grid) return;

  let allBlogs = [...blogs];
  let activeTag = 'All';
  let searchQuery = '';

  // Restore scroll
  const savedScroll = sessionStorage.getItem('homeScroll');
  if (savedScroll) {
    requestAnimationFrame(() => { window.scrollTo(0, parseInt(savedScroll)); });
    sessionStorage.removeItem('homeScroll');
  }

  // Build tag filter bar
  const allTags = ['All', ...new Set(blogs.flatMap(b => b.tags))].slice(0, 14);
  const tagBar = $('#tag-filter-bar');
  if (tagBar) {
    tagBar.innerHTML = allTags.map(tag =>
      `<button class="tag-pill ${tag === 'All' ? 'active' : ''}"
               onclick="filterByTag('${tag}')"
               id="tag-${tag.replace(/\s+/g, '-')}">${tag}</button>`
    ).join('');
  }

  // Expose filter function globally
  window.filterByTag = function(tag) {
    activeTag = tag;
    $$('.tag-pill').forEach(p => p.classList.remove('active'));
    const btn = $(`#tag-${tag.replace(/\s+/g, '-')}`);
    if (btn) btn.classList.add('active');
    renderCards();
  };

  // Search
  const searchInput = $('#nav-search');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      searchQuery = e.target.value.toLowerCase().trim();
      renderCards();
    });
  }

  function renderCards() {
    let filtered = allBlogs;
    if (activeTag !== 'All') {
      filtered = filtered.filter(b => b.tags.includes(activeTag));
    }
    if (searchQuery) {
      filtered = filtered.filter(b =>
        b.title.toLowerCase().includes(searchQuery) ||
        b.excerpt.toLowerCase().includes(searchQuery) ||
        b.author.toLowerCase().includes(searchQuery) ||
        b.tags.some(t => t.toLowerCase().includes(searchQuery))
      );
    }

    if (filtered.length === 0) {
      grid.innerHTML = `
        <div class="no-results">
          <div class="emoji">🔍</div>
          <h3>No blogs found</h3>
          <p>Try a different search term or tag.</p>
        </div>`;
      return;
    }

    grid.innerHTML = filtered.map(renderBlogCard).join('');
  }

  renderCards();
  window.openBlog = openBlog;
}

// ─────────────────────────────────────────────────────────────────────────────
//  BLOG DETAIL PAGE
// ─────────────────────────────────────────────────────────────────────────────
function initBlogPage() {
  const id = parseInt(getQueryParam('id'));
  const blog = blogs.find(b => b.id === id);

  if (!blog) {
    document.body.innerHTML = `
      <div style="text-align:center;padding:80px 24px;">
        <h1>Blog not found</h1>
        <a href="../view/home.html" style="color:var(--link)">← Back to Home</a>
      </div>`;
    return;
  }

  // Update page title & meta
  document.title = `${blog.title} — DevSync`;
  const metaDesc = document.querySelector('meta[name="description"]');
  if (metaDesc) metaDesc.content = blog.excerpt;

  // Populate elements
  const set = (id, html, prop = 'innerHTML') => {
    const el = document.getElementById(id);
    if (el) el[prop] = html;
  };

  set('blog-tags', blog.tags.map(renderTag).join(''));
  set('blog-title', blog.title);
  set('blog-author-avatar', blog.authorAvatar);
  set('blog-author-name', blog.author);
  set('blog-author-bio', blog.authorBio);
  set('blog-date', blog.date);
  set('blog-read-time', blog.readTime);
  set('blog-content', blog.content);

  // Avatar
  const avatarEl = document.getElementById('blog-author-avatar-el');
  if (avatarEl) avatarEl.textContent = blog.authorAvatar;

  // Like button
  let likeCount = parseInt(localStorage.getItem(`likes_${id}`)) || Math.floor(Math.random() * 600) + 50;
  let liked = localStorage.getItem(`liked_${id}`) === 'true';

  const likeBtn = $('#like-btn');
  const likeCountEl = $('#like-count');
  const likeCountText = $('#like-count-text');

  function updateLikeUI() {
    if (likeBtn) {
      likeBtn.classList.toggle('liked', liked);
      likeBtn.querySelector('.heart-icon').style.fill = liked ? 'var(--like-active)' : 'none';
      likeBtn.querySelector('.heart-icon').style.stroke = liked ? 'var(--like-active)' : 'currentColor';
    }
    if (likeCountEl) likeCountEl.textContent = likeCount;
    if (likeCountText) likeCountText.textContent = `${likeCount} people found this helpful`;
  }

  if (likeBtn) {
    updateLikeUI();
    likeBtn.addEventListener('click', () => {
      liked = !liked;
      likeCount += liked ? 1 : -1;
      localStorage.setItem(`liked_${id}`, liked);
      localStorage.setItem(`likes_${id}`, likeCount);

      // Pulse animation
      likeBtn.style.transform = 'scale(1.15)';
      setTimeout(() => likeBtn.style.transform = '', 200);

      updateLikeUI();
    });
  }

  // Related posts (same tags)
  const related = blogs
    .filter(b => b.id !== id && b.tags.some(t => blog.tags.includes(t)))
    .slice(0, 3);

  const relatedGrid = $('#related-grid');
  if (relatedGrid && related.length) {
    relatedGrid.innerHTML = related.map(renderBlogCard).join('');
  }

  window.openBlog = openBlog;
}

// ─────────────────────────────────────────────────────────────────────────────
//  WRITE PAGE
// ─────────────────────────────────────────────────────────────────────────────
function initWritePage() {
  const form = $('#write-form');
  if (!form) return;

  // Tag input handling
  let tags = [];
  const tagChipsContainer = $('#tag-chips');
  const tagInput = $('#tag-input');

  function renderTags() {
    if (!tagChipsContainer) return;
    tagChipsContainer.innerHTML = tags.map((t, i) =>
      `<span class="tag-chip">${t}
        <button onclick="removeTag(${i})" aria-label="Remove ${t}">×</button>
       </span>`
    ).join('');
  }

  window.removeTag = function(i) {
    tags.splice(i, 1);
    renderTags();
  };

  if (tagInput) {
    tagInput.addEventListener('keydown', (e) => {
      if ((e.key === 'Enter' || e.key === ',') && tagInput.value.trim()) {
        e.preventDefault();
        const tag = tagInput.value.trim().replace(/,/g, '');
        if (tag && !tags.includes(tag) && tags.length < 5) {
          tags.push(tag);
          renderTags();
        }
        tagInput.value = '';
      }
      if (e.key === 'Backspace' && !tagInput.value && tags.length) {
        tags.pop();
        renderTags();
      }
    });
  }

  // Toolbar buttons
  const textarea = $('#content-input');
  $$('.toolbar-btn[data-action]').forEach(btn => {
    btn.addEventListener('click', () => {
      if (!textarea) return;
      const action = btn.dataset.action;
      const start = textarea.selectionStart;
      const end = textarea.selectionEnd;
      const selected = textarea.value.substring(start, end);
      const map = {
        bold:    ['**', '**'],
        italic:  ['_', '_'],
        code:    ['`', '`'],
        h2:      ['## ', ''],
        h3:      ['### ', ''],
        link:    ['[', '](https://)'],
        codeblock: ['```\n', '\n```'],
      };
      const wrap = map[action];
      if (!wrap) return;
      const newText = wrap[0] + (selected || 'text') + wrap[1];
      textarea.value = textarea.value.substring(0, start) + newText + textarea.value.substring(end);
      textarea.focus();
    });
  });

  // Publish
  const publishBtn = $('#publish-btn');
  const toast = $('#toast');

  if (publishBtn) {
    publishBtn.addEventListener('click', () => {
      const title = $('#title-input')?.value.trim();
      if (!title) {
        $('#title-input')?.focus();
        return;
      }
      // Simulate save
      setTimeout(() => {
        if (toast) {
          toast.classList.add('show');
          setTimeout(() => toast.classList.remove('show'), 3500);
        }
      }, 200);
    });
  }

  // Character count for excerpt
  const excerptInput = $('#excerpt-input');
  const excerptCount = $('#excerpt-count');
  if (excerptInput && excerptCount) {
    excerptInput.addEventListener('input', () => {
      excerptCount.textContent = `${excerptInput.value.length}/160`;
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
//  LOGIN PAGE
// ─────────────────────────────────────────────────────────────────────────────
function initLoginPage() {
  const form = $('#login-form');
  if (!form) return;

  // Toggle password visibility
  const toggleBtn = $('#toggle-password');
  const pwInput = $('#password-input');
  if (toggleBtn && pwInput) {
    toggleBtn.addEventListener('click', () => {
      const isText = pwInput.type === 'text';
      pwInput.type = isText ? 'password' : 'text';
      toggleBtn.innerHTML = isText
        ? `<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`
        : `<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`;
    });
  }

  // Form submit
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const email = $('#email-input')?.value.trim();
    const password = pwInput?.value;

    const submitBtn = $('#login-btn');
    if (submitBtn) {
      submitBtn.textContent = 'Signing in…';
      submitBtn.disabled = true;
    }

    // Simulate auth delay
    setTimeout(() => {
      // Store mock user session
      localStorage.setItem('user', JSON.stringify({ email, name: 'Developer' }));
      navigateTo('../view/home.html');
    }, 1000);
  });

  // Real-time validation
  const emailInput = $('#email-input');
  if (emailInput) {
    emailInput.addEventListener('blur', () => {
      const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value);
      emailInput.style.borderColor = emailInput.value && !valid ? 'var(--like-active)' : '';
    });

    emailInput.addEventListener('focus', () => {
      emailInput.style.borderColor = '';
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
//  Bootstrap — detect current page and init
// ─────────────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  initDarkMode();

  const path = window.location.pathname;

  if (path.includes('home.html') || path.endsWith('/') || path.endsWith('index.html')) {
    initHomePage();
  } else if (path.includes('blog.html')) {
    initBlogPage();
  } else if (path.includes('write.html')) {
    initWritePage();
  } else if (path.includes('login.html')) {
    initLoginPage();
  } else {
    // Try all inits (fallback)
    initHomePage();
    initBlogPage();
    initWritePage();
    initLoginPage();
  }
});
