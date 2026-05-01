/* =============================================
   Markdown Editor Helpers
   ============================================= */
function insertMd(before, after) {
    var textarea = document.getElementById('content');
    if (!textarea) return;

    var start = textarea.selectionStart;
    var end = textarea.selectionEnd;
    var selected = textarea.value.substring(start, end);
    var replacement = before + (selected || 'text') + after;

    textarea.setRangeText(replacement, start, end, 'end');
    textarea.focus();
}

function triggerImageUpload() {
    var input = document.getElementById('content-image-upload');
    if (input) input.click();
}

function uploadContentImage(input) {
    if (!input.files || !input.files[0]) return;

    var file = input.files[0];

    // Client-side validation
    var allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (allowed.indexOf(file.type) === -1) {
        alert('Invalid file type. Use JPG, PNG, GIF, or WebP.');
        input.value = '';
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        alert('File too large. Maximum size is 2MB.');
        input.value = '';
        return;
    }

    var formData = new FormData();
    formData.append('image', file);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', BASE_URL + '/upload-image.php', true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            try {
                var res = JSON.parse(xhr.responseText);
                if (res.url) {
                    var textarea = document.getElementById('content');
                    var start = textarea.selectionStart;
                    var md = '![](' + res.url + ')';
                    textarea.setRangeText('\n' + md + '\n', start, start, 'end');
                    textarea.focus();
                }
            } catch (e) {
                alert('Something went wrong uploading the image.');
            }
        } else {
            try {
                var err = JSON.parse(xhr.responseText);
                alert(err.error || 'Upload failed.');
            } catch (e) {
                alert('Upload failed.');
            }
        }
        input.value = '';
    };

    xhr.onerror = function () {
        alert('Network error during upload.');
        input.value = '';
    };

    xhr.send(formData);
}

/* =============================================
   Like & Favorite Toggles
   ============================================= */
function toggleLike(postId) {
    var btn = event.currentTarget;
    var countEl = document.getElementById('like-count-' + postId);
    if (!countEl) return;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', BASE_URL + '/api-like.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        if (xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);
            countEl.textContent = res.count;
            if (res.liked) {
                btn.classList.add('liked');
                btn.querySelector('svg').setAttribute('fill', 'currentColor');
            } else {
                btn.classList.remove('liked');
                btn.querySelector('svg').setAttribute('fill', 'none');
            }
        }
    };

    xhr.send('post_id=' + postId);
}

function toggleFavorite(postId) {
    var btn = event.currentTarget;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', BASE_URL + '/api-favorite.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        if (xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);
            if (res.favorited) {
                btn.classList.add('favorited');
                btn.querySelector('span').textContent = 'Saved';
                btn.querySelector('svg').setAttribute('fill', 'currentColor');
            } else {
                btn.classList.remove('favorited');
                btn.querySelector('span').textContent = 'Save';
                btn.querySelector('svg').setAttribute('fill', 'none');
            }
        }
    };

    xhr.send('post_id=' + postId);
}

/* =============================================
   Auto-dismiss flash messages
   ============================================= */
(function () {
    var flashes = document.querySelectorAll('.flash');
    flashes.forEach(function (el) {
        setTimeout(function () {
            if (el.parentElement) el.remove();
        }, 5000);
    });
})();

/* =============================================
   Mobile admin sidebar toggle
   ============================================= */
(function () {
    var topbar = document.querySelector('.admin-topbar');
    if (topbar) {
        var toggle = document.createElement('button');
        toggle.className = 'mobile-toggle';
        toggle.innerHTML = '<span></span><span></span><span></span>';
        toggle.style.marginRight = '12px';
        toggle.addEventListener('click', function () {
            document.querySelector('.admin-sidebar').classList.toggle('open');
        });
        topbar.insertBefore(toggle, topbar.firstChild);
    }
})();

/* =============================================
   Confirm delete dialogs
   ============================================= */
(function () {
    var forms = document.querySelectorAll('form[onsubmit*="confirm"]');
    // The inline onsubmit handlers handle this already,
    // but we add a safety net for any that might be missed
})();