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

    /* ── Modal trocar senha ─────────────────────────────────────── */
    window.abrirModalSenha = function () {
        fecharDropdowns();
        document.getElementById('mSenhaFeedback').textContent = '';
        document.getElementById('mSenhaAtual').value = '';
        document.getElementById('mSenhaNova').value  = '';
        document.getElementById('mSenhaConf').value  = '';
        document.getElementById('mForcaBar').style.width = '0%';
        document.getElementById('mForcaLbl').textContent  = '';
        const m = document.getElementById('modalSenha');
        if (m) { m.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
    };
    window.fecharModalSenha = function () {
        const m = document.getElementById('modalSenha');
        if (m) { m.style.display = 'none'; document.body.style.overflow = ''; }
    };

    /* ── Inicializar badge ──────────────────────────────────────── */
    atualizarBadge();
})();

function verM(id) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
}

function forcaM(v) {
    const bar = document.getElementById('mForcaBar');
    const lbl = document.getElementById('mForcaLbl');
    if (!v) { bar.style.width = '0%'; lbl.textContent = ''; return; }
    let pts = 0;
    if (v.length >= 8)          pts++;
    if (/[A-Z]/.test(v))        pts++;
    if (/[0-9]/.test(v))        pts++;
    if (/[^A-Za-z0-9]/.test(v)) pts++;
    const levels = ['', 'Fraca', 'Fraca', 'Média', 'Forte'];
    const colors = ['', '#ef4444', '#ef4444', '#f59e0b', '#22c55e'];
    const widths = ['0%', '30%', '50%', '75%', '100%'];
    bar.style.width      = widths[pts];
    bar.style.background = colors[pts];
    lbl.textContent      = levels[pts];
    lbl.style.color      = colors[pts];
}

function salvarSenha() {
    const feedback = document.getElementById('mSenhaFeedback');
    const btn      = document.getElementById('mBtnSalvarSenha');
    const at = document.getElementById('mSenhaAtual').value;
    const nv = document.getElementById('mSenhaNova').value;
    const cf = document.getElementById('mSenhaConf').value;
    if (!at || !nv || !cf) { feedback.style.color = '#ef4444'; feedback.textContent = 'Preencha todos os campos.'; return; }
    if (nv.length < 6)     { feedback.style.color = '#ef4444'; feedback.textContent = 'Mínimo 6 caracteres.'; return; }
    if (nv !== cf)         { feedback.style.color = '#ef4444'; feedback.textContent = 'As senhas não conferem.'; return; }
    btn.disabled = true;
    const fd = new FormData();
    fd.append('senha_atual', at);
    fd.append('nova_senha',  nv);
    fd.append('confirma',    cf);
    fetch('pages-aluno/api-perfil.php?acao=senha', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
            if (d.sucesso) {
                feedback.style.color = '#22c55e';
                feedback.textContent = d.mensagem;
                setTimeout(fecharModalSenha, 1500);
            } else {
                feedback.style.color = '#ef4444';
                feedback.textContent = d.mensagem || 'Erro ao alterar senha.';
            }
        })
        .catch(() => { feedback.style.color = '#ef4444'; feedback.textContent = 'Erro de conexão.'; })
        .finally(() => { btn.disabled = false; });
}
