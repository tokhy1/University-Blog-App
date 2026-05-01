/* =============================================
   Markdown Editor
   ============================================= */
function insertMd(before, after) {
    var textarea = document.getElementById('content');
    if (!textarea) return;
    var start = textarea.selectionStart;
    var end = textarea.selectionEnd;
    var text = textarea.value.substring(start, end) || 'text';
    textarea.setRangeText(before + text + after, start, end, 'select');
    textarea.focus();
}

function triggerImageUpload() {
    var input = document.getElementById('content-image-upload');
    if (input) input.click();
}

function uploadContentImage(input) {
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];
    var allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (allowed.indexOf(file.type) === -1) {
        alert('Invalid file type. Use JPG, PNG, GIF, or WebP.');
        input.value = '';
        return;
    }
    if (file.size > 2 * 1024 * 1024) {
        alert('File too large. Maximum is 2MB.');
        input.value = '';
        return;
    }

    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    var formData = new FormData();
    formData.append('image', file);
    if (csrfToken) formData.append('csrf_token', csrfToken.getAttribute('content'));

    var xhr = new XMLHttpRequest();
    xhr.open('POST', BASE_URL + '/upload-image.php', true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            try {
                var res = JSON.parse(xhr.responseText);
                if (res.url) {
                    var textarea = document.getElementById('content');
                    var pos = textarea.selectionStart;
                    var md = '![](' + res.url + ')';
                    textarea.setRangeText('\n' + md + '\n', pos, pos, 'end');
                    textarea.focus();
                }
            } catch (e) {
                alert('Failed to process upload response.');
            }
        } else {
            alert('Upload failed. Server returned ' + xhr.status + '.');
        }
        input.value = '';
    };

    xhr.onerror = function () {
        alert('Network error.');
        input.value = '';
    };

    xhr.send(formData);
}

/* =============================================
   Thumbnail Preview (Create/Edit Post)
   ============================================= */
(function () {
    var thumbInput = document.getElementById('thumbnail');
    var thumbDrop = document.getElementById('thumbDrop');
    var thumbPlaceholder = document.getElementById('thumbPlaceholder');
    var thumbPreview = document.getElementById('thumbPreview');

    if (thumbInput && thumbDrop && thumbPlaceholder && thumbPreview) {
        thumbDrop.addEventListener('click', function (e) {
            if (e.target.closest('.file-remove')) return;
            thumbInput.click();
        });

        thumbDrop.addEventListener('dragover', function (e) {
            e.preventDefault();
            thumbDrop.classList.add('drag-over');
        });

        thumbDrop.addEventListener('dragleave', function () {
            thumbDrop.classList.remove('drag-over');
        });

        thumbDrop.addEventListener('drop', function (e) {
            e.preventDefault();
            thumbDrop.classList.remove('drag-over');
            if (e.dataTransfer.files.length) {
                thumbInput.files = e.dataTransfer.files;
                showThumbPreview(e.dataTransfer.files[0]);
            }
        });

        thumbInput.addEventListener('change', function () {
            if (thumbInput.files.length) {
                showThumbPreview(thumbInput.files[0]);
            }
        });

        function showThumbPreview(file) {
            if (!file.type.startsWith('image/')) return;
            var reader = new FileReader();
            reader.onload = function (e) {
                thumbPreview.querySelector('img').src = e.target.result;
                thumbPlaceholder.classList.add('hidden');
                thumbPreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        window.removeThumbPreview = function () {
            thumbInput.value = '';
            thumbPreview.querySelector('img').src = '';
            thumbPreview.classList.add('hidden');
            thumbPlaceholder.classList.remove('hidden');
        };
    }
})();

/* =============================================
   Like & Favorite
   ============================================= */
function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function toggleLike(postId) {
    var btn = event.currentTarget;
    var countEl = document.getElementById('like-count-' + postId);
    if (!countEl) return;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', BASE_URL + '/api-like.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-CSRF-Token', getCsrfToken());

    xhr.onload = function () {
        if (xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);
            countEl.textContent = res.count;
            var svg = btn.querySelector('svg');
            if (res.liked) {
                btn.classList.add('liked');
                svg.setAttribute('fill', 'currentColor');
            } else {
                btn.classList.remove('liked');
                svg.setAttribute('fill', 'none');
            }
        }
    };

    xhr.send('post_id=' + encodeURIComponent(postId));
}

function toggleFavorite(postId) {
    var btn = event.currentTarget;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', BASE_URL + '/api-favorite.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-CSRF-Token', getCsrfToken());

    xhr.onload = function () {
        if (xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);
            var svg = btn.querySelector('svg');
            var label = btn.querySelector('span');
            if (res.favorited) {
                btn.classList.add('favorited');
                svg.setAttribute('fill', 'currentColor');
                if (label) label.textContent = 'Saved';
            } else {
                btn.classList.remove('favorited');
                svg.setAttribute('fill', 'none');
                if (label) label.textContent = 'Save';
            }
        }
    };

    xhr.send('post_id=' + encodeURIComponent(postId));
}

/* =============================================
   Mobile Navigation
   ============================================= */
(function () {
    var toggle = document.getElementById('mobileToggle');
    var nav = document.getElementById('mainNav');
    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            nav.classList.toggle('open');
            var spans = toggle.querySelectorAll('span');
            toggle.classList.toggle('active');
        });
    }

    // User dropdown toggle on mobile
    var menu = document.getElementById('userMenu');
    if (menu) {
        menu.addEventListener('click', function (e) {
            if (window.innerWidth <= 768) {
                e.stopPropagation();
                var dd = document.getElementById('userDropdown');
                if (dd) dd.classList.toggle('open');
            }
        });
        document.addEventListener('click', function () {
            var dd = document.getElementById('userDropdown');
            if (dd) dd.classList.remove('open');
        });
    }
})();

/* =============================================
   Admin Sidebar
   ============================================= */
(function () {
    var toggle = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('adminSidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var main = document.getElementById('adminMain');

    function openSidebar() {
        if (sidebar) sidebar.classList.add('open');
        if (overlay) overlay.classList.add('open');
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('open');
    }

    if (toggle) toggle.addEventListener('click', openSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);

    // Close on resize if window gets wide
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) closeSidebar();
    });
})();

/* =============================================
   Auto-dismiss Flash Messages
   ============================================= */
(function () {
    document.querySelectorAll('.flash').forEach(function (el) {
        setTimeout(function () {
            el.style.opacity = '0';
            el.style.transform = 'translateY(-8px)';
            setTimeout(function () { if (el.parentElement) el.remove(); }, 200);
        }, 4500);
    });
})();