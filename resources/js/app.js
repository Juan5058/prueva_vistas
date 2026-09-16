document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.quick-login').forEach((button) => {
        button.addEventListener('click', () => {
            const email = document.getElementById('login-input-email');
            const password = document.getElementById('login-input-password');
            if (email && password) {
                email.value = button.dataset.email;
                password.value = button.dataset.password;
                document.getElementById('login-form')?.submit();
            }
        });
    });

    const ipModal = document.getElementById('ip-modal');
    document.getElementById('btn-open-ip-tester')?.addEventListener('click', () => ipModal?.classList.remove('hidden'));
    document.getElementById('btn-close-ip-modal')?.addEventListener('click', () => ipModal?.classList.add('hidden'));
    document.querySelectorAll('.ip-preset').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById('input-simulated-ip');
            if (input) input.value = button.dataset.ip;
        });
    });

    document.querySelectorAll('[data-repeat-root]').forEach((root) => {
        const list = root.querySelector('[data-repeat-list]');
        root.querySelector('.repeat-add')?.addEventListener('click', () => {
            const row = list?.querySelector('.repeat-row');
            if (!row || !list) return;
            const clone = row.cloneNode(true);
            const index = list.querySelectorAll('.repeat-row').length;
            clone.querySelectorAll('input, select, textarea').forEach((field) => {
                if (field.name) {
                    field.name = field.name.replace(/\[\d+]/, `[${index}]`);
                }
                if (field.type !== 'hidden' && field.tagName !== 'SELECT') {
                    field.value = field.type === 'number' ? field.value : '';
                }
            });
            list.appendChild(clone);
        });
    });

    const dataEl = document.getElementById('trd-structures-data');
    const trdSelect = document.getElementById('trd_structure_id');
    const serieSelect = document.getElementById('serie_id');
    const subSelect = document.getElementById('sub_serie_id');
    if (dataEl && trdSelect && serieSelect && subSelect) {
        const structures = JSON.parse(dataEl.textContent || '[]');
        const fill = () => {
            const current = structures.find((item) => String(item.id) === String(trdSelect.value));
            const selectedSerie = serieSelect.dataset.selected;
            const selectedSub = subSelect.dataset.selected;
            serieSelect.innerHTML = '';
            subSelect.innerHTML = '';
            (current?.series || []).forEach((serie) => {
                const option = document.createElement('option');
                option.value = serie.serie_id;
                option.textContent = `${serie.serie_code} — ${serie.serie_name}`;
                if (serie.serie_id === selectedSerie) option.selected = true;
                serieSelect.appendChild(option);
            });
            (current?.sub_series || []).forEach((sub) => {
                const option = document.createElement('option');
                option.value = sub.sub_serie_id;
                option.textContent = `${sub.sub_serie_code} — ${sub.sub_serie_name}`;
                if (sub.sub_serie_id === selectedSub) option.selected = true;
                subSelect.appendChild(option);
            });
        };
        trdSelect.addEventListener('change', () => {
            serieSelect.dataset.selected = '';
            subSelect.dataset.selected = '';
            fill();
        });
        fill();
    }
});
