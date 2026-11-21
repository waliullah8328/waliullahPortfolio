// Theme toggle -------------------------------------------------------------
const root = document.documentElement;
document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const dark = !root.classList.contains('dark');
        root.classList.toggle('dark', dark);
        try { localStorage.setItem('theme', dark ? 'dark' : 'light'); } catch (e) {}
    });
});

// Mobile menus / sidebars --------------------------------------------------
document.querySelectorAll('[data-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const target = document.getElementById(btn.dataset.toggle);
        if (!target) return;
        target.classList.toggle('hidden');
        btn.setAttribute('aria-expanded', String(!target.classList.contains('hidden')));
    });
});
document.querySelectorAll('[data-close-on-click] a').forEach((a) =>
    a.addEventListener('click', () => a.closest('[data-close-on-click]').classList.add('hidden'))
);

// Sticky header shadow -----------------------------------------------------
const header = document.querySelector('[data-header]');
if (header) {
    const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 10);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
}

// Scroll reveal + counters + skill bars ------------------------------------
const io = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        el.classList.add('in');
        if (el.dataset.count !== undefined) animateCount(el);
        if (el.dataset.level !== undefined) el.style.width = el.dataset.level + '%';
        if (el.dataset.ring !== undefined) el.style.strokeDashoffset = el.dataset.ring;
        io.unobserve(el);
    });
}, { threshold: 0.15 });
document.querySelectorAll('.reveal, [data-count], [data-level], [data-ring]').forEach((el) => io.observe(el));

// Skills: category tabs + mouse spotlight ----------------------------------
const skillTabs = document.querySelector('[data-skill-tabs]');
const skillTiles = document.querySelectorAll('[data-skill-cat]');
if (skillTabs) {
    skillTabs.addEventListener('click', (e) => {
        const tab = e.target.closest('[data-skill-tab]');
        if (!tab) return;
        skillTabs.querySelectorAll('[data-skill-tab]').forEach((t) => {
            t.classList.toggle('is-active', t === tab);
            t.setAttribute('aria-selected', String(t === tab));
        });
        const cat = tab.dataset.skillTab;
        let i = 0;
        skillTiles.forEach((tile) => {
            const show = cat === '*' || tile.dataset.skillCat === cat;
            tile.classList.toggle('is-hidden', !show);
            tile.classList.remove('pop');
            if (show) {
                void tile.offsetWidth; // restart animation
                tile.style.animationDelay = (i++ * 35) + 'ms';
                tile.classList.add('pop', 'in');
            }
        });
    });
}
document.querySelectorAll('.skill-tile, .project-card').forEach((tile) => {
    tile.addEventListener('pointermove', (e) => {
        const r = tile.getBoundingClientRect();
        tile.style.setProperty('--x', (e.clientX - r.left) + 'px');
        tile.style.setProperty('--y', (e.clientY - r.top) + 'px');
    });
});

function animateCount(el) {
    const raw = el.dataset.count;
    const target = parseInt(raw, 10);
    const suffix = raw.replace(/^[0-9]+/, '');
    if (Number.isNaN(target)) { el.textContent = raw; return; }
    const start = performance.now();
    const step = (t) => {
        const p = Math.min(1, (t - start) / 1400);
        el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))) + suffix;
        if (p < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
}

// Typing effect ------------------------------------------------------------
document.querySelectorAll('[data-typed]').forEach((el) => {
    const words = el.dataset.typed.split(',').map((w) => w.trim()).filter(Boolean);
    if (!words.length) return;
    let w = 0, i = 0, deleting = false;
    const tick = () => {
        const word = words[w];
        i += deleting ? -1 : 1;
        el.textContent = word.slice(0, i);
        let delay = deleting ? 45 : 90;
        if (!deleting && i === word.length) { deleting = true; delay = 1600; }
        else if (deleting && i === 0) { deleting = false; w = (w + 1) % words.length; delay = 300; }
        setTimeout(tick, delay);
    };
    tick();
});

// Project filter (home page) -----------------------------------------------
document.querySelectorAll('[data-filter-group]').forEach((group) => {
    const items = document.querySelectorAll(`[data-filter-item="${group.dataset.filterGroup}"]`);
    group.querySelectorAll('[data-filter]').forEach((btn) => {
        btn.addEventListener('click', () => {
            group.querySelectorAll('[data-filter]').forEach((b) => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            const f = btn.dataset.filter;
            items.forEach((item) => {
                // data-category may hold several values separated by "|" (e.g. "app|web").
                item.style.display = f === '*' || (item.dataset.category || '').split('|').includes(f) ? '' : 'none';
            });
        });
    });
});

// Star rating input --------------------------------------------------------
document.querySelectorAll('[data-rating]').forEach((wrap) => {
    const input = wrap.querySelector('input');
    const stars = wrap.querySelectorAll('button');
    const paint = (n) => stars.forEach((s, idx) => s.classList.toggle('text-amber-400', idx < n));
    paint(parseInt(input.value || '5', 10));
    stars.forEach((s, idx) => s.addEventListener('click', () => { input.value = idx + 1; paint(idx + 1); }));
});

// Admin: image preview on file input --------------------------------------
document.querySelectorAll('input[type=file][data-preview]').forEach((input) => {
    input.addEventListener('change', () => {
        const img = document.getElementById(input.dataset.preview);
        const file = input.files[0];
        if (img && file && file.type.startsWith('image/')) {
            img.src = URL.createObjectURL(file);
            img.classList.remove('hidden');
            img.parentElement.querySelector('[data-placeholder]')?.classList.add('hidden');
        }
        const name = document.getElementById(input.dataset.preview + '-name');
        if (name && file) name.textContent = file.name;
    });
});

// Admin: confirm destructive actions ---------------------------------------
document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (e) => {
        if (!confirm(form.dataset.confirm || 'Are you sure?')) e.preventDefault();
    });
});

// Admin: auto slug from title ----------------------------------------------
const titleInput = document.querySelector('[data-slug-source]');
const slugInput = document.querySelector('[data-slug-target]');
if (titleInput && slugInput) {
    let touched = slugInput.value !== '';
    slugInput.addEventListener('input', () => (touched = slugInput.value !== ''));
    titleInput.addEventListener('input', () => {
        if (touched) return;
        slugInput.placeholder = titleInput.value.toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
    });
}

// Admin: markdown editor with live preview ---------------------------------
document.querySelectorAll('[data-md-editor]').forEach((wrap) => {
    const textarea = wrap.querySelector('textarea');
    const preview = wrap.querySelector('[data-md-preview]');
    const tabs = wrap.querySelectorAll('[data-md-tab]');

    tabs.forEach((tab) => tab.addEventListener('click', async () => {
        tabs.forEach((t) => t.classList.toggle('is-active', t === tab));
        const showPreview = tab.dataset.mdTab === 'preview';
        textarea.classList.toggle('hidden', showPreview);
        preview.classList.toggle('hidden', !showPreview);
        if (showPreview) {
            preview.innerHTML = '<p class="text-slate-400">Rendering…</p>';
            const res = await fetch(wrap.dataset.mdEditor, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    Accept: 'application/json',
                },
                body: JSON.stringify({ body: textarea.value }),
            });
            preview.innerHTML = (await res.json()).html || '<p class="text-slate-400">Nothing to preview.</p>';
        }
    }));

    wrap.querySelectorAll('[data-md]').forEach((btn) => btn.addEventListener('click', () => {
        const [before, after = ''] = btn.dataset.md.split('|');
        const { selectionStart: s, selectionEnd: e, value } = textarea;
        const selected = value.slice(s, e) || btn.dataset.placeholder || '';
        textarea.setRangeText(before + selected + after, s, e, 'end');
        textarea.focus();
    }));
});

// Admin: show app-store fields only for app projects -----------------------
const typePicker = document.querySelector('[data-type-picker]');
const appFields = document.querySelector('[data-app-fields]');
if (typePicker && appFields) {
    const sync = () => {
        const v = typePicker.querySelector('input:checked')?.value;
        appFields.classList.toggle('hidden', v === 'website');
    };
    typePicker.addEventListener('change', sync);
    sync();
}

// Flash messages auto-hide -------------------------------------------------
document.querySelectorAll('[data-flash]').forEach((el) => {
    setTimeout(() => { el.style.transition = 'opacity .4s'; el.style.opacity = '0'; setTimeout(() => el.remove(), 400); }, 4000);
});
