<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPA - Login UEMA</title>
    <link rel="stylesheet" href="assets/css/login-page.css">
    <style>
        .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1000;justify-content:center;align-items:center;padding:20px}
        .modal-overlay.show{display:flex}
        .modal-box{background:#fff;border-radius:14px;width:100%;max-width:430px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;animation:modalIn .2s ease}
        @keyframes modalIn{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}
        .modal-box-header{background:#2B3C50;color:#fff;padding:18px 22px 14px;display:flex;align-items:center;gap:10px}
        .modal-box-header h5{margin:0;font-size:1rem;font-weight:700}
        .modal-box-header .close-btn{margin-left:auto;background:none;border:none;color:#fff;font-size:1.3rem;cursor:pointer;opacity:.8;transition:opacity .15s;line-height:1}
        .modal-box-header .close-btn:hover{opacity:1}
        .modal-box-body{padding:22px}
        .step{display:none}.step.active{display:block}
        .step-indicator{display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:20px}
        .step-dot{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;background:#e2e8f0;color:#64748b;transition:all .25s}
        .step-dot.active{background:#2B3C50;color:#fff}
        .step-dot.done{background:#22c55e;color:#fff}
        .step-line{flex:1;max-width:36px;height:2px;background:#e2e8f0;border-radius:2px;transition:background .25s}
        .step-line.done{background:#22c55e}
        .step-title{font-size:.93rem;font-weight:700;color:#1e293b;margin-bottom:4px}
        .step-sub{font-size:.82rem;color:#64748b;margin-bottom:16px;line-height:1.4}
        .field{margin-bottom:14px}
        .field label{display:block;font-size:.82rem;font-weight:600;color:#374151;margin-bottom:5px}
        .field input{width:100%;height:46px;border:1.5px solid #d1d5db;border-radius:8px;padding:0 12px;font-size:.9rem;outline:none;transition:border-color .2s;font-family:inherit;box-sizing:border-box}
        .field input:focus{border-color:#2B3C50}
        .codigo-inputs{display:flex;gap:8px;justify-content:center;margin-bottom:14px}
        .codigo-inputs input{width:46px;height:52px;text-align:center;font-size:1.4rem;font-weight:700;border:1.5px solid #d1d5db;border-radius:8px;outline:none;transition:border-color .2s,box-shadow .2s;font-family:inherit;box-sizing:border-box}
        .codigo-inputs input:focus{border-color:#2B3C50;box-shadow:0 0 0 3px rgba(43,60,80,.12)}
        .codigo-inputs input.ok{border-color:#22c55e;background:#f0fdf4}
        .forca-barra{display:flex;gap:4px;margin-top:6px}
        .forca-barra div{flex:1;height:4px;border-radius:2px;background:#e2e8f0;transition:background .3s}
        .forca-label{font-size:.72rem;color:#64748b;margin-top:2px}
        .input-olho{position:relative}
        .input-olho input{padding-right:44px}
        .btn-olho{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;font-size:1rem;padding:4px}
        .btn-olho:hover{color:#374151}
        .btn-acao{width:100%;height:46px;border:none;background:#2B3C50;color:#fff;font-size:.9rem;font-weight:700;border-radius:8px;cursor:pointer;transition:background .2s;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:8px}
        .btn-acao:hover:not(:disabled){background:#1e2d3d}
        .btn-acao:disabled{opacity:.6;cursor:not-allowed}
        .btn-voltar{background:none;border:none;color:#64748b;font-size:.82rem;cursor:pointer;display:flex;align-items:center;gap:4px;margin-bottom:12px;font-family:inherit;padding:0}
        .btn-voltar:hover{color:#2B3C50}
        .fb-box{padding:10px 14px;border-radius:8px;font-size:.83rem;margin-bottom:12px;display:none}
        .fb-box.erro{background:#fef2f2;color:#dc2626;border:1px solid #fecaca;display:block}
        .fb-box.ok{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;display:block}
        .fb-box.info{background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;display:block}
        .dev-box{background:#fefce8;border:1px dashed #eab308;border-radius:8px;padding:10px 14px;margin-bottom:12px;font-size:.82rem;color:#854d0e;display:none}
        .dev-box.show{display:block}
        .reenviar-wrap{text-align:center;margin-top:10px}
        .btn-reenviar{background:none;border:none;color:#2B3C50;font-size:.8rem;cursor:pointer;text-decoration:underline;font-family:inherit}
        .btn-reenviar:disabled{color:#9ca3af;text-decoration:none;cursor:default}
        .sucesso-final{text-align:center;padding:10px 0}
        .sucesso-final .icon{font-size:3rem}
        .sucesso-final h6{font-weight:700;margin:10px 0 4px;color:#1e293b}
        .sucesso-final p{font-size:.85rem;color:#64748b}
        @keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
        @keyframes shake{0%,100%{transform:translateX(0)}20%{transform:translateX(-6px)}40%{transform:translateX(6px)}60%{transform:translateX(-4px)}80%{transform:translateX(4px)}}

        .btn-voltar-inicio{
            position:fixed;top:22px;left:22px;z-index:10;
            display:inline-flex;align-items:center;gap:10px;
            background:#fff;border:1.5px solid #ddd;border-radius:12px;
            padding:10px 18px;box-shadow:0 8px 28px rgba(0,0,0,.13);
            color:#111ec9;text-decoration:none;font-family:'Montserrat',sans-serif;
            font-size:.875rem;font-weight:600;transition:box-shadow .2s,transform .15s,background .2s;
            white-space:nowrap;
        }
        .btn-voltar-inicio:hover{
            background:#f1f5f9;box-shadow:0 12px 36px rgba(0,0,0,.18);transform:translateY(-1px);
        }
        .btn-voltar-inicio__arrow{
            display:flex;align-items:center;justify-content:center;
            width:30px;height:30px;border-radius:8px;background:#111ec9;color:#fff;flex-shrink:0;
            transition:background .2s;
        }
        .btn-voltar-inicio:hover .btn-voltar-inicio__arrow{background:#0000a2;}
        @media(max-width:480px){
            .btn-voltar-inicio{top:12px;left:12px;padding:8px 14px;font-size:.8rem;}
            .btn-voltar-inicio__arrow{width:26px;height:26px;}
        }
    </style>
</head>
<body>

<a href="index.php" class="btn-voltar-inicio" title="Voltar ao início">
    <span class="btn-voltar-inicio__arrow">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>
        </svg>
    </span>
    <span class="btn-voltar-inicio__label">Voltar ao início</span>
</a>

<main class="container">
    <form id="formLogin" action="processa_login.php" method="POST">
        <div class="uema">
            <img src="assets/img/Brasao_UEMA_horizontal.png" alt="UEMA" class="logo">
            <img src="assets/img/Proexae.png" alt="Proexae" class="logo">
        </div>

        <?php if (isset($_GET['erro'])): ?>
        <div style="color:#dc2626;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:.875rem;">
            ⚠️ E-mail ou senha incorretos. Tente novamente.
        </div>
        <?php endif; ?>
        <?php if (isset($_GET['senha_redefinida'])): ?>
        <div style="color:#16a34a;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:.875rem;">
            ✅ Senha redefinida com sucesso! Faça login com a nova senha.
        </div>
        <?php endif; ?>

        <div class="input-box">
            <input placeholder="E-mail" type="email" name="email" required autocomplete="email">
        </div>
        <div class="input-box">
            <input placeholder="Senha" type="password" name="senha" required autocomplete="current-password">
        </div>

        <button type="submit" class="login">Entrar</button>

        <div class="create">
            <a href="#" onclick="abrirModal(event)">Esqueci minha senha</a>
        </div>
    </form>
</main>

<!-- ══ MODAL REDEFINIÇÃO ══ -->
<div class="modal-overlay" id="overlay" onclick="clicarFora(event)">
<div class="modal-box">
    <div class="modal-box-header">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
        <h5>Redefinição de Senha</h5>
        <button class="close-btn" onclick="fecharModal()">&#x2715;</button>
    </div>
    <div class="modal-box-body">

        <!-- Indicador de etapas -->
        <div class="step-indicator">
            <div class="step-dot active" id="d1">1</div>
            <div class="step-line" id="l1"></div>
            <div class="step-dot" id="d2">2</div>
            <div class="step-line" id="l2"></div>
            <div class="step-dot" id="d3">3</div>
        </div>

        <!-- ── STEP 1: E-mail ── -->
        <div class="step active" id="s1">
            <p class="step-title">Informe seu e-mail</p>
            <p class="step-sub">Enviaremos um código de 6 dígitos para verificar sua identidade.</p>
            <div class="fb-box" id="fb1"></div>
            <div class="field">
                <label>E-mail cadastrado</label>
                <input type="email" id="s1email" placeholder="seu@email.com" autocomplete="email" oninput="limparFb('fb1')">
            </div>
            <button class="btn-acao" id="btn1" onclick="step1()">
                <span id="btn1txt">Enviar código</span>
                <svg id="spin1" style="display:none;animation:spin 1s linear infinite" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
            </button>
        </div>

        <!-- ── STEP 2: Código OTP ── -->
        <div class="step" id="s2">
            <button class="btn-voltar" onclick="irStep(1)">← Voltar</button>
            <p class="step-title">Verifique seu e-mail</p>
            <p class="step-sub" id="s2desc">Digite o código de 6 dígitos enviado para o seu e-mail.</p>
            <div class="fb-box" id="fb2"></div>
            <div class="dev-box" id="devBox">🛠️ <strong>SMTP não configurado.</strong> Código de teste: <strong id="devCodigo" style="letter-spacing:4px;font-size:1.1rem"></strong></div>
            <div class="codigo-inputs" id="codigoWrap">
                <input type="text" maxlength="1" id="c0" oninput="otpInput(0)" onkeydown="otpBack(event,0)">
                <input type="text" maxlength="1" id="c1" oninput="otpInput(1)" onkeydown="otpBack(event,1)">
                <input type="text" maxlength="1" id="c2" oninput="otpInput(2)" onkeydown="otpBack(event,2)">
                <input type="text" maxlength="1" id="c3" oninput="otpInput(3)" onkeydown="otpBack(event,3)">
                <input type="text" maxlength="1" id="c4" oninput="otpInput(4)" onkeydown="otpBack(event,4)">
                <input type="text" maxlength="1" id="c5" oninput="otpInput(5)" onkeydown="otpBack(event,5)">
            </div>
            <button class="btn-acao" id="btn2" onclick="step2()">
                <span id="btn2txt">Confirmar código</span>
                <svg id="spin2" style="display:none;animation:spin 1s linear infinite" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
            </button>
            <div class="reenviar-wrap">
                <button class="btn-reenviar" id="btnReenv" disabled onclick="reenviar()">
                    Reenviar código (<span id="cnt">60</span>s)
                </button>
            </div>
        </div>

        <!-- ── STEP 3: Nova senha ── -->
        <div class="step" id="s3">
            <p class="step-title">Crie uma nova senha</p>
            <p class="step-sub">Escolha uma senha segura com pelo menos 6 caracteres.</p>
            <div class="fb-box" id="fb3"></div>
            <div class="field">
                <label>Nova senha</label>
                <div class="input-olho">
                    <input type="password" id="nova" placeholder="Mínimo 6 caracteres" oninput="forca(this.value)">
                    <button type="button" class="btn-olho" onclick="ver('nova')">👁</button>
                </div>
                <div class="forca-barra"><div id="b1"></div><div id="b2"></div><div id="b3"></div><div id="b4"></div></div>
                <div class="forca-label" id="bLbl"></div>
            </div>
            <div class="field">
                <label>Confirmar senha</label>
                <div class="input-olho">
                    <input type="password" id="conf" placeholder="Repita a nova senha">
                    <button type="button" class="btn-olho" onclick="ver('conf')">👁</button>
                </div>
            </div>
            <button class="btn-acao" id="btn3" onclick="step3()">
                <span id="btn3txt">Redefinir Senha</span>
                <svg id="spin3" style="display:none;animation:spin 1s linear infinite" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
            </button>
        </div>

        <!-- ── STEP 4: Sucesso ── -->
        <div class="step" id="s4">
            <div class="sucesso-final">
                <div class="icon">✅</div>
                <h6>Senha redefinida com sucesso!</h6>
                <p>Agora você pode fazer login com a nova senha.</p>
            </div>
            <button class="btn-acao" style="margin-top:18px" onclick="fecharModal()">Ir para o Login</button>
        </div>

    </div><!-- /modal-box-body -->
</div><!-- /modal-box -->
</div><!-- /overlay -->

<script>
let stepAtual=1, timerReenv=null;

function abrirModal(e){ e.preventDefault(); irStep(1); limparCodigo(); document.getElementById('s1email').value=''; limparFb('fb1'); document.getElementById('overlay').classList.add('show'); setTimeout(()=>document.getElementById('s1email').focus(),100); }
function fecharModal(){ document.getElementById('overlay').classList.remove('show'); clearInterval(timerReenv); if(stepAtual===4) location.href='login-page.php?senha_redefinida=1'; }
function clicarFora(e){ if(e.target===document.getElementById('overlay')) fecharModal(); }

function irStep(n){
    document.querySelectorAll('.step').forEach(s=>s.classList.remove('active'));
    document.getElementById('s'+n).classList.add('active');
    stepAtual=n;
    for(let i=1;i<=3;i++){
        const dot=document.getElementById('d'+i), line=i<3?document.getElementById('l'+i):null;
        dot.className='step-dot';
        if(i<n){dot.classList.add('done');dot.innerHTML='✓';if(line)line.className='step-line done';}
        else if(i===n){dot.classList.add('active');dot.textContent=i;}
        else{dot.textContent=i;if(line)line.className='step-line';}
    }
    if(n===1) setTimeout(()=>document.getElementById('s1email').focus(),50);
    if(n===2) setTimeout(()=>document.getElementById('c0').focus(),50);
    if(n===3) setTimeout(()=>document.getElementById('nova').focus(),50);
}

function limparFb(id){ const el=document.getElementById(id); el.className='fb-box'; el.textContent=''; }
function mostrarFb(id,tipo,msg){ const el=document.getElementById(id); el.className='fb-box '+tipo; el.innerHTML=msg; }
function loading(n,on){ document.getElementById('btn'+n).disabled=on; document.getElementById('btn'+n+'txt').style.display=on?'none':'inline'; document.getElementById('spin'+n).style.display=on?'inline':'none'; }

// ── STEP 1 ──────────────────────────────────────────────────
function step1(){
    const email=document.getElementById('s1email').value.trim();
    if(!email||!email.includes('@')){ mostrarFb('fb1','erro','Informe um e-mail válido.'); return; }
    loading(1,true); limparFb('fb1');
    const fd=new FormData(); fd.append('acao','verificar_email'); fd.append('email',email);
    fetch('pages-adm/api-redefinir-senha.php',{method:'POST',body:fd})
        .then(r=>r.json())
        .then(d=>{
            loading(1,false);
            if(!d.sucesso){ mostrarFb('fb1','erro',d.mensagem); return; }
            document.getElementById('s2desc').textContent=`Código enviado para ${email}. Verifique sua caixa de entrada e spam.`;
            if(d.codigo_dev){
                document.getElementById('devBox').classList.add('show');
                document.getElementById('devCodigo').textContent=d.codigo_dev;
            } else {
                document.getElementById('devBox').classList.remove('show');
            }
            limparCodigo(); limparFb('fb2');
            irStep(2); iniciarTimer();
        })
        .catch(()=>{ loading(1,false); mostrarFb('fb1','erro','Erro de conexão. Tente novamente.'); });
}

// ── Timer de reenvio ─────────────────────────────────────────
function iniciarTimer(){
    clearInterval(timerReenv); let s=60;
    const btn=document.getElementById('btnReenv'), cnt=document.getElementById('cnt');
    btn.disabled=true; cnt.textContent=s;
    timerReenv=setInterval(()=>{ s--; cnt.textContent=s; if(s<=0){ clearInterval(timerReenv); btn.disabled=false; btn.textContent='Reenviar código'; } },1000);
}
function reenviar(){
    const email=document.getElementById('s1email').value.trim();
    if(!email){ irStep(1); return; }
    loading(2,true); limparFb('fb2');
    const fd=new FormData(); fd.append('acao','verificar_email'); fd.append('email',email);
    fetch('pages-adm/api-redefinir-senha.php',{method:'POST',body:fd})
        .then(r=>r.json())
        .then(d=>{
            loading(2,false);
            if(d.codigo_dev){ document.getElementById('devBox').classList.add('show'); document.getElementById('devCodigo').textContent=d.codigo_dev; mostrarFb('fb2','info','Novo código gerado.'); }
            else mostrarFb('fb2','ok','Novo código enviado!');
            iniciarTimer(); limparCodigo();
        })
        .catch(()=>{ loading(2,false); mostrarFb('fb2','erro','Erro ao reenviar.'); });
}

// ── OTP inputs ───────────────────────────────────────────────
function otpInput(i){
    const el=document.getElementById('c'+i);
    el.value=el.value.replace(/[^0-9]/g,'').slice(-1);
    el.classList.toggle('ok',el.value!=='');
    if(el.value&&i<5) document.getElementById('c'+(i+1)).focus();
    const todos=Array.from({length:6},(_,j)=>document.getElementById('c'+j).value);
    if(todos.every(v=>v!=='')) setTimeout(step2,300);
}
function otpBack(e,i){ if(e.key==='Backspace'&&!document.getElementById('c'+i).value&&i>0) document.getElementById('c'+(i-1)).focus(); }
function limparCodigo(){ for(let i=0;i<6;i++){ const el=document.getElementById('c'+i); el.value=''; el.classList.remove('ok'); } }

// ── STEP 2 ──────────────────────────────────────────────────
function step2(){
    const cod=Array.from({length:6},(_,i)=>document.getElementById('c'+i).value).join('');
    if(cod.length<6){ mostrarFb('fb2','erro','Digite todos os 6 dígitos.'); return; }
    loading(2,true); limparFb('fb2');
    const fd=new FormData(); fd.append('acao','verificar_codigo'); fd.append('codigo',cod);
    fetch('pages-adm/api-redefinir-senha.php',{method:'POST',body:fd})
        .then(r=>r.json())
        .then(d=>{
            loading(2,false);
            if(!d.sucesso){
                mostrarFb('fb2','erro',d.mensagem);
                const w=document.getElementById('codigoWrap');
                w.style.animation='none'; w.offsetHeight; w.style.animation='shake .4s ease';
                return;
            }
            clearInterval(timerReenv);
            document.getElementById('nova').value=''; document.getElementById('conf').value='';
            resetForca(); limparFb('fb3'); irStep(3);
        })
        .catch(()=>{ loading(2,false); mostrarFb('fb2','erro','Erro de conexão.'); });
}

// ── STEP 3 ──────────────────────────────────────────────────
function step3(){
    const n=document.getElementById('nova').value, c=document.getElementById('conf').value;
    if(n.length<6){ mostrarFb('fb3','erro','A senha deve ter pelo menos 6 caracteres.'); return; }
    if(n!==c){ mostrarFb('fb3','erro','As senhas não conferem.'); return; }
    loading(3,true); limparFb('fb3');
    const fd=new FormData(); fd.append('acao','nova_senha'); fd.append('nova_senha',n); fd.append('confirma',c);
    fetch('pages-adm/api-redefinir-senha.php',{method:'POST',body:fd})
        .then(r=>r.json())
        .then(d=>{ loading(3,false); if(!d.sucesso){ mostrarFb('fb3','erro',d.mensagem); return; } irStep(4); })
        .catch(()=>{ loading(3,false); mostrarFb('fb3','erro','Erro de conexão.'); });
}

// ── Força da senha ───────────────────────────────────────────
function forca(v){
    let p=0;
    if(v.length>=6)p++; if(v.length>=10)p++; if(/[A-Z]/.test(v)&&/[a-z]/.test(v))p++; if(/[0-9]/.test(v)&&/[^A-Za-z0-9]/.test(v))p++;
    const cores=['#ef4444','#f97316','#eab308','#22c55e'], labels=['Muito fraca','Fraca','Boa','Forte'];
    for(let i=1;i<=4;i++) document.getElementById('b'+i).style.background=i<=p?cores[p-1]:'#e2e8f0';
    document.getElementById('bLbl').textContent=v.length>0?(labels[p-1]||''):'';
}
function resetForca(){ for(let i=1;i<=4;i++) document.getElementById('b'+i).style.background='#e2e8f0'; document.getElementById('bLbl').textContent=''; }
function ver(id){ const el=document.getElementById(id); el.type=el.type==='password'?'text':'password'; }

// Enter para avançar
document.addEventListener('keydown',e=>{ if(e.key!=='Enter') return; if(stepAtual===1) step1(); if(stepAtual===3) step3(); });

// Intercepta o submit do formulário de login
document.getElementById('formLogin').addEventListener('submit', function(e){
    e.preventDefault();
    const btn = this.querySelector('button[type=submit]');
    btn.disabled = true;
    btn.textContent = 'Entrando...';
    fetch('processa_login.php', {method:'POST', body: new FormData(this)})
        .then(r => r.json())
        .then(d => {
            if(d.status === 'ok'){
                window.location.href = d.redirect;
            } else {
                window.location.href = 'login-page.php?erro=1';
            }
        })
        .catch(() => { window.location.href = 'login-page.php?erro=1'; });
});
</script>
</body>
</html>
