/**
 * SIMPA — Navegação AJAX
 */
(function () {

    const container = document.getElementById('ajaxContent');
    if (!container) {
        console.warn('[AJAX-NAV] #ajaxContent não encontrado.');
        return;
    }

    console.log('[AJAX-NAV] Iniciado.');
    container.style.transition = 'opacity 0.15s ease';

    function getPageParam(href) {
        if (!href) return null;
        const match = (href + '').match(/[?&]page=([^&]+)/);
        return match ? decodeURIComponent(match[1]) : null;
    }

    function reexecuteScripts(el) {
        el.querySelectorAll('script').forEach(function (old) {
            const s = document.createElement('script');
            s.textContent = old.textContent;
            old.parentNode.replaceChild(s, old);
        });
    }

    function setActive(page) {
        document.querySelectorAll('#sidebar ul li a').forEach(function (a) {
            const p = getPageParam(a.getAttribute('href'));
            a.classList.toggle('active', p === page);
        });
    }

    function loadPage(page, pushState) {
        console.log('[AJAX-NAV] Carregando página:', page);
        container.style.opacity = '0.4';
        container.style.pointerEvents = 'none';

        fetch(window.location.pathname + '?page=' + encodeURIComponent(page) + '&ajax=1')
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.text();
            })
            .then(function (html) {
                container.innerHTML = html;
                reexecuteScripts(container);
                container.style.opacity = '';
                container.style.pointerEvents = '';
                setActive(page);
                if (pushState) {
                    history.pushState({ page: page }, '', window.location.pathname + '?page=' + encodeURIComponent(page));
                }
                if (typeof closeSidebar === 'function') closeSidebar();
                console.log('[AJAX-NAV] Página carregada:', page);
            })
            .catch(function (err) {
                console.error('[AJAX-NAV] Erro:', err);
                container.innerHTML = "<div class='alert alert-danger m-3'>Erro ao carregar a página.</div>";
                container.style.opacity = '';
                container.style.pointerEvents = '';
            });
    }

    // Adicionar listener em cada link do sidebar individualmente
    function bindLinks() {
        document.querySelectorAll('#sidebar ul li a').forEach(function (link) {
            link.addEventListener('click', function (e) {
                const page = getPageParam(this.getAttribute('href'));
                console.log('[AJAX-NAV] Clique no link, page:', page);
                if (!page) return;
                e.preventDefault();
                e.stopPropagation();
                loadPage(page, true);
            });
        });
        console.log('[AJAX-NAV] Links vinculados:', document.querySelectorAll('#sidebar ul li a').length);
    }

    bindLinks();

    window.addEventListener('popstate', function (e) {
        const page = (e.state && e.state.page)
            ? e.state.page
            : getPageParam(window.location.search) || 'pagina-inicial';
        loadPage(page, false);
    });

    const initialPage = getPageParam(window.location.search) || 'pagina-inicial';
    history.replaceState({ page: initialPage }, '', window.location.href);
    setActive(initialPage);

})();
