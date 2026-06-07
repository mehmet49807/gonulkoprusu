(function () {
    const MAX_BYTES = 5 * 1024 * 1024;
    const MAX_DIMENSION = 1600;
    const JPEG_QUALITY = 0.88;

    function isImageFile(file) {
        return file && (file.type.startsWith('image/') || /\.(jpe?g|png|gif|webp|heic|heif)$/i.test(file.name));
    }

    function readAsImage(file) {
        return new Promise(function (resolve, reject) {
            const url = URL.createObjectURL(file);
            const img = new Image();
            img.onload = function () {
                URL.revokeObjectURL(url);
                resolve(img);
            };
            img.onerror = function () {
                URL.revokeObjectURL(url);
                reject(new Error('invalid'));
            };
            img.src = url;
        });
    }

    function canvasToBlob(canvas, type, quality) {
        return new Promise(function (resolve) {
            canvas.toBlob(resolve, type, quality);
        });
    }

    async function preparePhotoFile(file) {
        if (!isImageFile(file)) {
            throw new Error('Lütfen JPEG, PNG, GIF veya WebP formatında bir fotoğraf seçin.');
        }

        if (file.size <= MAX_BYTES && /^image\/jpe?g$/i.test(file.type)) {
            return file;
        }

        const img = await readAsImage(file);
        const scale = Math.min(1, MAX_DIMENSION / Math.max(img.width, img.height));
        const width = Math.max(1, Math.round(img.width * scale));
        const height = Math.max(1, Math.round(img.height * scale));
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, width, height);

        const blob = await canvasToBlob(canvas, 'image/jpeg', JPEG_QUALITY);
        if (!blob) {
            throw new Error('Fotoğraf işlenemedi. Başka bir görsel deneyin.');
        }

        if (blob.size > MAX_BYTES) {
            throw new Error('Fotoğraf 5 MB sınırını aşıyor. Daha küçük bir görsel seçin.');
        }

        const baseName = file.name.replace(/\.[^.]+$/, '') || 'profile';
        return new File([blob], baseName + '.jpg', { type: 'image/jpeg', lastModified: Date.now() });
    }

    function setPreview(container, file) {
        if (!container) return;
        container.innerHTML = '';
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.alt = 'Profil önizleme';
        container.appendChild(img);
    }

    function showError(form, message) {
        let el = form.parentElement?.querySelector('.profile-photo-error');
        if (!el) {
            el = document.createElement('small');
            el.className = 'form-error profile-photo-error';
            form.closest('.profile-photo-wrap')?.insertAdjacentElement('afterend', el)
                || form.closest('.register-photo-block')?.insertAdjacentElement('afterend', el);
        }
        el.textContent = message;
    }

    function clearError(form) {
        const wrap = form.closest('.profile-page, .form-card, .auth-form-wrap');
        wrap?.querySelectorAll('.profile-photo-error').forEach(function (node) {
            node.remove();
        });
    }

    function setLoading(form, loading) {
        const btn = form.querySelector('.profile-photo-change, .register-photo-btn');
        if (btn) {
            btn.classList.toggle('profile-photo-change--loading', loading);
            btn.setAttribute('aria-busy', loading ? 'true' : 'false');
        }
        const input = form.querySelector('input[type="file"]');
        if (input) input.disabled = loading;
    }

    async function submitPhotoForm(form, prepared) {
        const formData = new FormData();
        formData.append('photo', prepared, prepared.name);

        const token = form.querySelector('input[name="_token"]')?.value;
        if (token) {
            formData.append('_token', token);
        }

        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json, text/html',
            },
        });

        if (response.ok) {
            window.location.reload();
            return;
        }

        let message = 'Profil fotoğrafı yüklenemedi. Lütfen tekrar deneyin.';
        try {
            const data = await response.json();
            if (data?.message) message = data.message;
            if (data?.errors?.photo?.[0]) message = data.errors.photo[0];
        } catch (e) {
            // HTML yanıt — sayfayı yenileyerek sunucu hatalarını göster.
            window.location.reload();
            return;
        }

        throw new Error(message);
    }

    async function handlePhotoInput(input, autoSubmit) {
        const form = input.form;
        const file = input.files?.[0];
        if (!form || !file) return;

        clearError(form);

        try {
            if (autoSubmit) setLoading(form, true);
            const prepared = await preparePhotoFile(file);
            const preview = document.getElementById('profilePhotoPreview')
                || document.getElementById('registerPhotoPreview');
            setPreview(preview, prepared);

            if (autoSubmit) {
                await submitPhotoForm(form, prepared);
            } else {
                const transfer = new DataTransfer();
                transfer.items.add(prepared);
                input.files = transfer.files;
            }
        } catch (err) {
            if (autoSubmit) setLoading(form, false);
            input.value = '';
            showError(form, err.message || 'Fotoğraf yüklenemedi.');
        }
    }

    document.querySelectorAll('.profile-photo-form input[type="file"]').forEach(function (input) {
        input.removeAttribute('required');
        input.addEventListener('change', function () {
            handlePhotoInput(input, true);
        });
    });

    document.querySelectorAll('.register-photo-input').forEach(function (input) {
        input.addEventListener('change', function () {
            handlePhotoInput(input, false);
        });
    });

    document.querySelectorAll('[data-profile-open-story]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (e.target.closest('.profile-photo-change')) return;
            const index = parseInt(btn.dataset.profileOpenStory, 10);
            const storyItem = document.querySelector('.story-item[data-story-index="' + index + '"]');
            if (storyItem) {
                storyItem.click();
                return;
            }
            const viewer = document.getElementById('igStoryViewer');
            if (viewer && typeof window.gkOpenStory === 'function') {
                window.gkOpenStory(index);
            }
        });
    });
})();
