(function () {
    const btnNotif    = document.getElementById('btnNotif');
    const dropNotif   = document.getElementById('dropNotif');
    const btnPerfil   = document.getElementById('btnPerfil');
    const dropPerfil  = document.getElementById('dropPerfil');
    const badgeNotif  = document.getElementById('badgeNotif');
    const btnLerTodas = document.getElementById('btnLerTodas');
    const listaNotif  = document.getElementById('listaNotif');

    /* ── Badge counter ─────────────────────────────────────────── */
    function atualizarBadge() {
        const n = listaNotif.querySelectorAll('.tb-notif-item[data-lida="0"]').length;
        badgeNotif.textContent = n;
        badgeNotif.style.display = n > 0 ? '' : 'none';
    }

    /* ── Fechar todos os dropdowns ──────────────────────────────── */
    function fecharDropdowns() {
        dropNotif.classList.remove('aberto');
        dropPerfil.classList.remove('aberto');
    }
    window.fecharDropdowns = fecharDropdowns;

    /* ── Toggle sininho ─────────────────────────────────────────── */
    btnNotif.addEventListener('click', function (e) {
        e.stopPropagation();
        const estaAberto = dropNotif.classList.contains('aberto');
        fecharDropdowns();
        if (!estaAberto) dropNotif.classList.add('aberto');
    });

    /* ── Toggle perfil ──────────────────────────────────────────── */
    btnPerfil.addEventListener('click', function (e) {
        e.stopPropagation();
        const estaAberto = dropPerfil.classList.contains('aberto');
        fecharDropdowns();
        if (!estaAberto) dropPerfil.classList.add('aberto');
    });

    /* ── Fechar ao clicar fora ──────────────────────────────────── */
    document.addEventListener('click', fecharDropdowns);

    /* ── Evitar fechar ao clicar dentro dos painéis ─────────────── */
    dropNotif.addEventListener('click',  function (e) { e.stopPropagation(); });
    dropPerfil.addEventListener('click', function (e) { e.stopPropagation(); });


    /* ── Modal de perfil ────────────────────────────────────────── */
    window.abrirModalPerfil = function () {
        fecharDropdowns();
        const m = document.getElementById('modalPerfil');
        if (m) { m.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
    };
    window.fecharModalPerfil = function () {
        const m = document.getElementById('modalPerfil');
        if (m) { m.style.display = 'none'; document.body.style.overflow = ''; }
    };

    /* ── Inicializar badge ──────────────────────────────────────── */
    atualizarBadge();
})();
