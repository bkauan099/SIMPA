<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPA - Login UEMA</title>
    <link rel="stylesheet" href="assets/css/login-page.css">
    <style>
        /* ── Modal de redefinição ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .modal-overlay.show { display: flex; }

        .modal-box {
            background: #fff;
            border-radius: 14px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            overflow: hidden;
            animation: modalIn .2s ease;
        }
        @keyframes modalIn { from { opacity:0; transform:scale(.95); } to { opacity:1; transform:scale(1); } }

        .modal-box-header {
            background: #2B3C50;
            color: #fff;
            padding: 20px 24px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .modal-box-header h5 { margin:0; font-size:1rem; font-weight:700; }
        .modal-box-header .close-btn {
            margin-left: auto;
            background: none;
            border: none;
            color: #fff;
            font-size: 1.3rem;
            cursor: pointer;
            line-height: 1;
            opacity: .8;
            transition: opacity .15s;
        }
        .modal-box-header .close-btn:hover { opacity: 1; }

        .modal-box-body { padding: 24px; }

        .step { display: none; }
        .step.active { display: block; }

        .step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-bottom: 20px;
        }
        .step-dot {
            width: 28px; height: 28px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 700;
            background: #e2e8f0; color: #64748b;
            transition: all .25s;
        }
        .step-dot.active { background: #2B3C50; color: #fff; }
        .step-dot.done   { background: #22c55e; color: #fff; }
        .step-line { flex: 1; max-width: 36px; height: 2px; background: #e2e8f0; border-radius:2px; transition: background .25s; }
        .step-line.done { background: #22c55e; }

        .step-title { font-size: .93rem; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
        .step-sub   { font-size: .82rem; color: #64748b; margin-bottom: 18px; line-height: 1.4; }

        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: .82rem; font-weight: 600; color: #374151; margin-bottom: 5px; }

        .field input {
            width: 100%;
            height: 46px;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            padding: 0 12px;
            font-size: .9rem;
            outline: none;
            transition: border-color .2s;
            font-family: 'Montserrat', sans-serif;
        }
        .field input:focus { border-color: #2B3C50; }

        /* código de 6 dígitos */
        .codigo-inputs {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-bottom: 16px;
        }
        .codigo-inputs input {
            width: 46px; height: 52px;
            text-align: center;
            font-size: 1.4rem; font-weight: 700;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            font-family: 'Montserrat', sans-serif;
        }
        .codigo-inputs input:focus {
            border-color: #2B3C50;
            box-shadow: 0 0 0 3px rgba(43,60,80,.12);
        }
        .codigo-inputs input.preenchido { border-color: #22c55e; background: #f0fdf4; }

        /* força da senha */
        .forca-barra { display: flex; gap: 4px; margin-top: 6px; margin-bottom: 4px; }
        .forca-barra div { flex: 1; height: 4px; border-radius: 2px; background: #e2e8f0; transition: background .3s; }
        .forca-label { font-size: .75rem; color: #64748b; }

        /* olho na senha */
        .input-senha-wrap { position: relative; }
        .input-senha-wrap input { padding-right: 44px; }
        .btn-olho {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; color: #9ca3af; font-size: 1rem;
            padding: 4px;
        }
        .btn-olho:hover { color: #374151; }

        .btn-acao {
            width: 100%;
            height: 46px;
            border: none;
            background: #2B3C50;
            color: #fff;
            font-size: .9rem;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            transition: background .2s;
            font-family: 'Montserrat', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-acao:hover:not(:disabled) { background: #1e2d3d; }
        .btn-acao:disabled { opacity: .6; cursor: not-allowed; }

        .btn-voltar {
            background: none;
            border: none;
            color: #64748b;
            font-size: .82rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 14px;
            font-family: 'Montserrat', sans-serif;
            padding: 0;
        }
        .btn-voltar:hover { color: #2B3C50; }

        .feedback-box {
            padding: 10px 14px;
            border-radius: 8px;
            font-size: .83rem;
            margin-bottom: 14px;
            display: none;
        }
        .feedback-box.erro    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; display: block; }
        .feedback-box.sucesso { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; display: block; }
        .feedback-box.info    { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; display: block; }

        .reenviar-wrap { text-align: center; margin-top: 12px; }
        .btn-reenviar {
            background: none; border: none; color: #2B3C50; font-size: .8rem;
            cursor: pointer; text-decoration: underline; font-family: 'Montserrat', sans-serif;
        }
        .btn-reenviar:disabled { color: #9ca3af; text-decoration: none; cursor: default; }

        .codigo-dev-box {
            background: #fefce8;
            border: 1px dashed #eab308;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 14px;
            font-size: .82rem;
            color: #854d0e;
            display: none;
        }
        .codigo-dev-box.show { display: block; }

        /* Mensagem de sucesso final */
        .sucesso-final {
            text-align: center;
            padding: 10px 0 6px;
        }
        .sucesso-final i { font-size: 3rem; color: #22c55e; }
        .sucesso-final h6 { font-weight: 700; margin: 10px 0 4px; color: #1e293b; }
        .sucesso-final p { font-size: .85rem; color: #64748b; }
    </style>
</head>
<body>
    <main class="container">
        <form id="formLogin" action="processa_login.php" method="POST">
            <div class="uema">
                <img src="assets/img/uema-logo.png" alt="UEMA" class="logo">
                <img src="assets/img/Proexae.png" alt="Proexae" class="logo">
            </div>

            <?php if (isset($_GET['erro'])): ?>
                <div style="color:#dc2626; background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:10px 14px; margin-bottom:15px; font-size:0.875rem;">
                    ⚠️ E-mail ou senha incorretos. Tente novamente.
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['senha_redefinida'])): ?>
                <div style="color:#16a34a; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px 14px; margin-bottom:15px; font-size:0.875rem;">
                    ✅ Senha redefinida! Faça login com a nova senha.
                </div>
            <?php endif; ?>

            <div class="input-box">
                <input placeholder="E-mail" type="email" name="email" required>
            </div>

            <div class="input-box">
                <input placeholder="Senha" type="password" name="senha" required>
            </div>

            <button type="submit" class="login">Entrar</button>

            <div class="create">
                <a href="#" onclick="abrirModalRedefinir(event)">Redefinir senha</a>
            </div>
        </form>
    </main>

    <!-- ════════════════════════════════════════
         MODAL DE REDEFINIÇÃO DE SENHA
    ════════════════════════════════════════ -->
    <div class="modal-overlay" id="modalOverlay" onclick="fecharSeClicarFora(event)">
        <div class="modal-box" id="modalBox">

            <div class="modal-box-header">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                </svg>
                <h5>Redefinição de Senha</h5>
                <button class="close-btn" onclick="fecharModal()" title="Fechar">&#x2715;</button>
            </div>

            <div class="modal-box-body">

                <!-- Indicador de etapas -->
                <div class="step-indicator" id="stepIndicator">
                    <div class="step-dot active" id="dot1">1</div>
                    <div class="step-line" id="line1"></div>
                    <div class="step-dot" id="dot2">2</div>
                    <div class="step-line" id="line2"></div>
                    <div class="step-dot" id="dot3">3</div>
                </div>

                <!-- ── STEP 1: E-mail ── -->
                <div class="step active" id="step1">
                    <p class="step-title">Informe seu e-mail</p>
                    <p class="step-sub">Digite o e-mail cadastrado na sua conta. Enviaremos um código de verificação.</p>

                    <div class="feedback-box" id="fb1"></div>

                    <div class="field">
                        <label>E-mail cadastrado</label>
                        <input type="email" id="s1_email" placeholder="seu@email.com" oninput="limparFeedback('fb1')">
                    </div>

                    <button class="btn-acao" id="btnStep1" onclick="verificarEmail()">
                        <span id="btnStep1Text">Enviar código de verificação</span>
                        <span id="spinnerStep1" style="display:none">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin 1s linear infinite">
                                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                            </svg>
                        </span>
                    </button>
                </div>

                <!-- ── STEP 2: Código ── -->
                <div class="step" id="step2">
                    <button class="btn-voltar" onclick="irParaStep(1)">← Voltar</button>
                    <p class="step-title">Verifique seu e-mail</p>
                    <p class="step-sub" id="s2_descricao">Digite o código de 6 dígitos enviado para o seu e-mail.</p>

                    <div class="feedback-box" id="fb2"></div>

                    <!-- Caixa de dev (quando SMTP não está configurado) -->
                    <div class="codigo-dev-box" id="codigoDevBox">
                        🛠️ <strong>Modo desenvolvimento:</strong> SMTP não configurado.<br>
                        Seu código de verificação é: <strong id="codigoDevValor" style="font-size:1.1rem;letter-spacing:2px"></strong>
                    </div>

                    <div class="codigo-inputs" id="codigoInputs">
                        <input type="text" maxlength="1" id="c0" oninput="avancarCodigo(0)" onkeydown="voltarCodigo(event,0)">
                        <input type="text" maxlength="1" id="c1" oninput="avancarCodigo(1)" onkeydown="voltarCodigo(event,1)">
                        <input type="text" maxlength="1" id="c2" oninput="avancarCodigo(2)" onkeydown="voltarCodigo(event,2)">
                        <input type="text" maxlength="1" id="c3" oninput="avancarCodigo(3)" onkeydown="voltarCodigo(event,3)">
                        <input type="text" maxlength="1" id="c4" oninput="avancarCodigo(4)" onkeydown="voltarCodigo(event,4)">
                        <input type="text" maxlength="1" id="c5" oninput="avancarCodigo(5)" onkeydown="voltarCodigo(event,5)">
                    </div>

                    <button class="btn-acao" id="btnStep2" onclick="verificarCodigo()">
                        <span id="btnStep2Text">Confirmar Código</span>
                        <span id="spinnerStep2" style="display:none">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                        </span>
                    </button>

                    <div class="reenviar-wrap">
                        <button class="btn-reenviar" id="btnReenviar" onclick="reenviarCodigo()" disabled>
                            Reenviar código (<span id="contador">60</span>s)
                        </button>
                    </div>
                </div>

                <!-- ── STEP 3: Nova senha ── -->
                <div class="step" id="step3">
                    <p class="step-title">Crie uma nova senha</p>
                    <p class="step-sub">Escolha uma senha segura com pelo menos 6 caracteres.</p>

                    <div class="feedback-box" id="fb3"></div>

                    <div class="field">
                        <label>Nova senha</label>
                        <div class="input-senha-wrap">
                            <input type="password" id="s3_nova" placeholder="Mínimo 6 caracteres" oninput="calcularForca(this.value)">
                            <button type="button" class="btn-olho" onclick="toggleSenhaField('s3_nova')">👁</button>
                        </div>
                        <div class="forca-barra">
                            <div id="bf1"></div><div id="bf2"></div><div id="bf3"></div><div id="bf4"></div>
                        </div>
                        <div class="forca-label" id="bfLabel"></div>
                    </div>

                    <div class="field">
                        <label>Confirmar nova senha</label>
                        <div class="input-senha-wrap">
                            <input type="password" id="s3_confirma" placeholder="Repita a nova senha">
                            <button type="button" class="btn-olho" onclick="toggleSenhaField('s3_confirma')">👁</button>
                        </div>
                    </div>

                    <button class="btn-acao" id="btnStep3" onclick="redefinirSenha()">
                        <span id="btnStep3Text">Redefinir Senha</span>
                        <span id="spinnerStep3" style="display:none">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                        </span>
                    </button>
                </div>

                <!-- ── STEP 4: Sucesso final ── -->
                <div class="step" id="step4">
                    <div class="sucesso-final">
                        <i>✅</i>
                        <h6>Senha redefinida com sucesso!</h6>
                        <p>Sua senha foi alterada. Agora você já pode fazer login com a nova senha.</p>
                    </div>
                    <button class="btn-acao" style="margin-top:18px;" onclick="fecharModal()">
                        Ir para o Login
                    </button>
                </div>

            </div><!-- /modal-box-body -->
        </div><!-- /modal-box -->
    </div><!-- /modal-overlay -->

    <style>
        @keyframes spin { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }
    </style>

    <script>
    // ══════════════════════════════════════════════
    // CONTROLE DO MODAL E DAS ETAPAS
    // ══════════════════════════════════════════════
    let stepAtual = 1;
    let timerReenvio = null;

    function abrirModalRedefinir(e) {
        if (e) e.preventDefault();
        irParaStep(1);
        document.getElementById('s1_email').value = '';
        limparFeedback('fb1');
        document.getElementById('modalOverlay').classList.add('show');
        setTimeout(() => document.getElementById('s1_email').focus(), 100);
    }

    function fecharModal() {
        document.getElementById('modalOverlay').classList.remove('show');
        clearInterval(timerReenvio);
        if (stepAtual === 4) {
            // Senha foi redefinida com sucesso — recarregar para mostrar aviso
            window.location.href = 'login-page.php?senha_redefinida=1';
        }
    }

    function fecharSeClicarFora(e) {
        if (e.target === document.getElementById('modalOverlay')) fecharModal();
    }

    function irParaStep(n) {
        // Esconder todos os steps
        document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
        document.getElementById('step' + n).classList.add('active');
        stepAtual = n;

        // Atualizar indicadores
        for (let i = 1; i <= 3; i++) {
            const dot  = document.getElementById('dot' + i);
            const line = document.getElementById('line' + i);
            dot.className = 'step-dot';
            if (line) line.className = 'step-line';

            if (i < n)      { dot.classList.add('done'); dot.innerHTML = '✓'; if (line) line.classList.add('done'); }
            else if (i === n) { dot.classList.add('active'); dot.textContent = i; }
            else            { dot.textContent = i; }
        }

        // Focar no primeiro input do step
        if (n === 1) setTimeout(() => document.getElementById('s1_email').focus(), 50);
        if (n === 2) setTimeout(() => document.getElementById('c0').focus(), 50);
        if (n === 3) setTimeout(() => document.getElementById('s3_nova').focus(), 50);
    }

    function limparFeedback(id) {
        const el = document.getElementById(id);
        el.className = 'feedback-box';
        el.textContent = '';
    }

    function mostrarFeedback(id, tipo, msg) {
        const el = document.getElementById(id);
        el.className = 'feedback-box ' + tipo;
        el.textContent = msg;
    }

    function setLoading(btn, spin, loading) {
        document.getElementById(btn).disabled = loading;
        document.getElementById(btn + 'Text').style.display = loading ? 'none' : 'inline';
        document.getElementById(spin).style.display         = loading ? 'inline' : 'none';
    }

    // ══════════════════════════════════════════════
    // STEP 1: Verificar e-mail
    // ══════════════════════════════════════════════
    function verificarEmail() {
        const email = document.getElementById('s1_email').value.trim();
        if (!email || !email.includes('@')) {
            mostrarFeedback('fb1', 'erro', 'Informe um e-mail válido.');
            return;
        }

        setLoading('btnStep1', 'spinnerStep1', true);
        limparFeedback('fb1');

        const body = new FormData();
        body.append('acao', 'verificar_email');
        body.append('email', email);

        fetch('pages-adm/api-redefinir-senha.php', { method: 'POST', body })
            .then(r => r.json())
            .then(data => {
                setLoading('btnStep1', 'spinnerStep1', false);
                if (!data.sucesso) {
                    mostrarFeedback('fb1', 'erro', data.mensagem);
                    return;
                }

                // Atualizar descrição do step 2
                document.getElementById('s2_descricao').textContent =
                    `Código enviado para ${email}. Verifique sua caixa de entrada e spam.`;

                // Se e-mail falhou (modo dev), mostrar código na tela
                if (data.codigo_dev) {
                    document.getElementById('codigoDevBox').classList.add('show');
                    document.getElementById('codigoDevValor').textContent = data.codigo_dev;
                    mostrarFeedback('fb1', 'info', data.mensagem);
                } else {
                    document.getElementById('codigoDevBox').classList.remove('show');
                }

                // Limpar inputs de código
                for (let i = 0; i < 6; i++) {
                    document.getElementById('c' + i).value = '';
                    document.getElementById('c' + i).classList.remove('preenchido');
                }
                limparFeedback('fb2');

                irParaStep(2);
                iniciarTimerReenvio();
            })
            .catch(() => {
                setLoading('btnStep1', 'spinnerStep1', false);
                mostrarFeedback('fb1', 'erro', 'Erro de conexão. Tente novamente.');
            });
    }

    // ── Temporizador de reenvio ──
    function iniciarTimerReenvio() {
        clearInterval(timerReenvio);
        let seg = 60;
        const btn = document.getElementById('btnReenviar');
        const cnt = document.getElementById('contador');
        btn.disabled = true;
        cnt.textContent = seg;

        timerReenvio = setInterval(() => {
            seg--;
            cnt.textContent = seg;
            if (seg <= 0) {
                clearInterval(timerReenvio);
                btn.disabled = false;
                btn.textContent = 'Reenviar código';
            }
        }, 1000);
    }

    function reenviarCodigo() {
        const email = document.getElementById('s1_email').value.trim();
        if (!email) { irParaStep(1); return; }
        // Simular clique no botão do step 1 sem voltar de step
        setLoading('btnStep2', 'spinnerStep2', true);
        limparFeedback('fb2');

        const body = new FormData();
        body.append('acao', 'verificar_email');
        body.append('email', email);

        fetch('pages-adm/api-redefinir-senha.php', { method: 'POST', body })
            .then(r => r.json())
            .then(data => {
                setLoading('btnStep2', 'spinnerStep2', false);
                if (data.codigo_dev) {
                    document.getElementById('codigoDevBox').classList.add('show');
                    document.getElementById('codigoDevValor').textContent = data.codigo_dev;
                    mostrarFeedback('fb2', 'info', 'Novo código gerado (modo dev): ' + data.codigo_dev);
                } else {
                    mostrarFeedback('fb2', 'sucesso', 'Novo código enviado para ' + email);
                }
                iniciarTimerReenvio();
            })
            .catch(() => {
                setLoading('btnStep2', 'spinnerStep2', false);
                mostrarFeedback('fb2', 'erro', 'Erro ao reenviar. Tente novamente.');
            });
    }

    // ══════════════════════════════════════════════
    // STEP 2: Inputs de código OTP
    // ══════════════════════════════════════════════
    function avancarCodigo(idx) {
        const input = document.getElementById('c' + idx);
        // Aceitar apenas dígito
        input.value = input.value.replace(/[^0-9]/g, '').slice(-1);
        input.classList.toggle('preenchido', input.value !== '');

        if (input.value && idx < 5) {
            document.getElementById('c' + (idx + 1)).focus();
        }
        // Se todos preenchidos, verificar automaticamente
        const todos = Array.from({length:6}, (_, i) => document.getElementById('c' + i).value);
        if (todos.every(v => v !== '')) {
            setTimeout(verificarCodigo, 300);
        }
    }

    function voltarCodigo(e, idx) {
        if (e.key === 'Backspace' && !document.getElementById('c' + idx).value && idx > 0) {
            document.getElementById('c' + (idx - 1)).focus();
        }
    }

    function verificarCodigo() {
        const codigo = Array.from({length:6}, (_, i) => document.getElementById('c' + i).value).join('');
        if (codigo.length < 6) {
            mostrarFeedback('fb2', 'erro', 'Digite todos os 6 dígitos do código.');
            return;
        }

        setLoading('btnStep2', 'spinnerStep2', true);
        limparFeedback('fb2');

        const body = new FormData();
        body.append('acao', 'verificar_codigo');
        body.append('codigo', codigo);

        fetch('pages-adm/api-redefinir-senha.php', { method: 'POST', body })
            .then(r => r.json())
            .then(data => {
                setLoading('btnStep2', 'spinnerStep2', false);
                if (!data.sucesso) {
                    mostrarFeedback('fb2', 'erro', data.mensagem);
                    // Tremer os inputs
                    document.getElementById('codigoInputs').style.animation = 'none';
                    document.getElementById('codigoInputs').offsetHeight;
                    document.getElementById('codigoInputs').style.animation = 'shake .4s ease';
                    return;
                }
                clearInterval(timerReenvio);
                document.getElementById('s3_nova').value     = '';
                document.getElementById('s3_confirma').value = '';
                resetForca();
                limparFeedback('fb3');
                irParaStep(3);
            })
            .catch(() => {
                setLoading('btnStep2', 'spinnerStep2', false);
                mostrarFeedback('fb2', 'erro', 'Erro de conexão. Tente novamente.');
            });
    }

    // ══════════════════════════════════════════════
    // STEP 3: Nova senha
    // ══════════════════════════════════════════════
    function redefinirSenha() {
        const nova     = document.getElementById('s3_nova').value;
        const confirma = document.getElementById('s3_confirma').value;

        if (nova.length < 6) {
            mostrarFeedback('fb3', 'erro', 'A senha deve ter pelo menos 6 caracteres.');
            return;
        }
        if (nova !== confirma) {
            mostrarFeedback('fb3', 'erro', 'As senhas não conferem.');
            return;
        }

        setLoading('btnStep3', 'spinnerStep3', true);
        limparFeedback('fb3');

        const body = new FormData();
        body.append('acao', 'nova_senha');
        body.append('nova_senha', nova);
        body.append('confirma', confirma);

        fetch('pages-adm/api-redefinir-senha.php', { method: 'POST', body })
            .then(r => r.json())
            .then(data => {
                setLoading('btnStep3', 'spinnerStep3', false);
                if (!data.sucesso) {
                    mostrarFeedback('fb3', 'erro', data.mensagem);
                    return;
                }
                irParaStep(4);
            })
            .catch(() => {
                setLoading('btnStep3', 'spinnerStep3', false);
                mostrarFeedback('fb3', 'erro', 'Erro de conexão. Tente novamente.');
            });
    }

    // ── Força da senha ──
    function calcularForca(senha) {
        let p = 0;
        if (senha.length >= 6)  p++;
        if (senha.length >= 10) p++;
        if (/[A-Z]/.test(senha) && /[a-z]/.test(senha)) p++;
        if (/[0-9]/.test(senha) && /[^A-Za-z0-9]/.test(senha)) p++;
        const cores  = ['#ef4444','#f97316','#eab308','#22c55e'];
        const labels = ['Muito fraca','Fraca','Boa','Forte'];
        for (let i = 1; i <= 4; i++) {
            document.getElementById('bf'+i).style.background = i <= p ? (cores[p-1]||'#e2e8f0') : '#e2e8f0';
        }
        document.getElementById('bfLabel').textContent = senha.length > 0 ? (labels[p-1]||'') : '';
    }
    function resetForca() {
        for (let i=1;i<=4;i++) document.getElementById('bf'+i).style.background='#e2e8f0';
        document.getElementById('bfLabel').textContent='';
    }

    // ── Mostrar/ocultar senha ──
    function toggleSenhaField(id) {
        const inp = document.getElementById(id);
        inp.type = inp.type === 'password' ? 'text' : 'password';
    }

    // ── Enter para avançar ──
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter') return;
        if (stepAtual === 1) verificarEmail();
        if (stepAtual === 3) redefinirSenha();
    });
    </script>
    <style>
        @keyframes shake {
            0%,100%{ transform:translateX(0) }
            20%    { transform:translateX(-6px) }
            40%    { transform:translateX(6px) }
            60%    { transform:translateX(-4px) }
            80%    { transform:translateX(4px) }
        }
    </style>
</body>
</html>
