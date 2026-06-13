/**
 * SIMPA — Navegação AJAX
 */
(function () {

    const container = document.getElementById('ajaxContent');
    if (!container) return;

    const SPINNER = '<div class="text-center mt-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></div>';

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
        container.innerHTML = SPINNER;

        fetch(window.location.pathname + '?page=' + encodeURIComponent(page) + '&ajax=1', { cache: 'no-store' })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.text();
            })
            .then(function (html) {
                container.innerHTML = html;
                reexecuteScripts(container);
                setActive(page);
                if (pushState) {
                    history.pushState(
                        { page: page }, '',
                        window.location.pathname + '?page=' + encodeURIComponent(page)
                    );
                }
                if (typeof closeSidebar === 'function') closeSidebar();
            })
            .catch(function (err) {
                container.innerHTML = "<div class='alert alert-danger m-3'>Erro ao carregar a página.<br><small>" + err.message + "</small></div>";
            });
    }

    function bindLinks() {
        document.querySelectorAll('#sidebar ul li a').forEach(function (link) {
            link.addEventListener('click', function (e) {
                const page = getPageParam(this.getAttribute('href'));
                if (!page) return;
                e.preventDefault();
                e.stopPropagation();
                loadPage(page, true);
            });
        });
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
