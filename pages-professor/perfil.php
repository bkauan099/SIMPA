<?php
// $pdo e $_SESSION já disponíveis via professor-page.php
$stmt = $pdo->prepare("SELECT nome, matricula FROM usuarios WHERE id_usuario = :id");
$stmt->execute([':id' => $_SESSION['id_usuario']]);
$dp = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['nome' => 'Professor', 'matricula' => '—'];

$partes   = array_filter(explode(' ', $dp['nome']));
$iniciais = implode('', array_map(
    fn($p) => mb_strtoupper(mb_substr($p, 0, 1)),
    array_slice($partes, 0, 2)
));
?>
<div id="modalPerfil"
     style="display:none;position:fixed;inset:0;z-index:1090;
            background:rgba(0,0,0,0.45);align-items:center;justify-content:center;"
     onclick="if(event.target===this) fecharModalPerfil()">

    <div style="background:#fff;border-radius:20px;width:90%;max-width:360px;
                box-shadow:0 8px 40px rgba(0,0,0,0.22);overflow:hidden;
                animation:tbFadeIn .2s ease;">

        <!-- Banner -->
        <div style="background:linear-gradient(135deg,#0F2557 0%,#1d4ed8 100%);
                    padding:32px 24px 22px;text-align:center;position:relative;">

            <button onclick="fecharModalPerfil()"
                    style="position:absolute;top:12px;right:14px;
                           background:rgba(255,255,255,0.15);border:none;
                           border-radius:8px;width:30px;height:30px;
                           color:white;cursor:pointer;font-size:0.95rem;
                           display:flex;align-items:center;justify-content:center;
                           transition:background .15s;"
                    onmouseenter="this.style.background='rgba(255,255,255,0.25)'"
                    onmouseleave="this.style.background='rgba(255,255,255,0.15)'">
                <i class="bi bi-x-lg"></i>
            </button>

            <div style="width:72px;height:72px;border-radius:50%;
                        background:rgba(255,255,255,0.18);
                        border:3px solid rgba(255,255,255,0.45);
                        display:flex;align-items:center;justify-content:center;
                        font-size:1.6rem;font-weight:700;color:white;
                        margin:0 auto 12px;letter-spacing:1px;">
                <?= htmlspecialchars($iniciais) ?>
            </div>

            <div style="color:white;font-weight:700;font-size:1.05rem;line-height:1.3;">
                <?= htmlspecialchars($dp['nome']) ?>
            </div>
            <div style="color:rgba(255,255,255,0.6);font-size:0.78rem;margin-top:4px;">
                Professor · UEMA
            </div>
        </div>

        <!-- Campos -->
        <div style="padding:18px 24px 24px;">

            <div style="display:flex;align-items:center;gap:12px;
                        padding:12px 0;border-bottom:1px solid #f1f5f9;">
                <div style="width:36px;height:36px;border-radius:10px;
                            background:#eff6ff;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0;">
                    <i class="bi bi-person" style="color:#3b82f6;font-size:1rem;"></i>
                </div>
                <div>
                    <div style="font-size:0.68rem;font-weight:700;color:#94a3b8;
                                text-transform:uppercase;letter-spacing:.05em;">Nome</div>
                    <div style="font-size:0.88rem;color:#1e293b;font-weight:500;margin-top:2px;">
                        <?= htmlspecialchars($dp['nome']) ?>
                    </div>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:12px;padding:12px 0;">
                <div style="width:36px;height:36px;border-radius:10px;
                            background:#f0fdf4;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0;">
                    <i class="bi bi-card-text" style="color:#22c55e;font-size:1rem;"></i>
                </div>
                <div>
                    <div style="font-size:0.68rem;font-weight:700;color:#94a3b8;
                                text-transform:uppercase;letter-spacing:.05em;">Matrícula</div>
                    <div style="font-size:0.88rem;color:#1e293b;font-weight:500;margin-top:2px;">
                        <?= htmlspecialchars($dp['matricula'] ?? '—') ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="modalSenha"
     style="display:none;position:fixed;inset:0;z-index:1091;
            background:rgba(0,0,0,0.45);align-items:center;justify-content:center;"
     onclick="if(event.target===this) fecharModalSenha()">

    <div style="background:#fff;border-radius:20px;width:90%;max-width:380px;
                box-shadow:0 8px 40px rgba(0,0,0,0.22);overflow:hidden;
                animation:tbFadeIn .2s ease;">

        <div style="background:linear-gradient(135deg,#0F2557 0%,#1d4ed8 100%);
                    padding:28px 24px 22px;text-align:center;position:relative;">

            <button onclick="fecharModalSenha()"
                    style="position:absolute;top:12px;right:14px;
                           background:rgba(255,255,255,0.15);border:none;
                           border-radius:8px;width:30px;height:30px;
                           color:white;cursor:pointer;font-size:0.95rem;
                           display:flex;align-items:center;justify-content:center;
                           transition:background .15s;"
                    onmouseenter="this.style.background='rgba(255,255,255,0.25)'"
                    onmouseleave="this.style.background='rgba(255,255,255,0.15)'">
                <i class="bi bi-x-lg"></i>
            </button>

            <div style="width:56px;height:56px;border-radius:50%;
                        background:rgba(255,255,255,0.18);
                        border:3px solid rgba(255,255,255,0.45);
                        display:flex;align-items:center;justify-content:center;
                        font-size:1.5rem;color:white;margin:0 auto 12px;">
                <i class="bi bi-key-fill"></i>
            </div>

            <div style="color:white;font-weight:700;font-size:1rem;">Trocar Senha</div>
            <div style="color:rgba(255,255,255,0.6);font-size:0.78rem;margin-top:3px;">
                Defina uma nova senha de acesso
            </div>
        </div>

        <div style="padding:20px 24px 24px;">

            <div style="margin-bottom:14px;">
                <label style="font-size:0.73rem;font-weight:700;color:#64748b;
                              text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:5px;">
                    Senha Atual
                </label>
                <div style="display:flex;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;">
                    <input type="password" id="mSenhaAtual" placeholder="••••••••"
                           style="flex:1;border:none;padding:9px 12px;font-size:0.88rem;outline:none;color:#1e293b;">
                    <button type="button" onclick="verM('mSenhaAtual')"
                            style="border:none;background:transparent;padding:0 12px;color:#94a3b8;cursor:pointer;">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div style="margin-bottom:8px;">
                <label style="font-size:0.73rem;font-weight:700;color:#64748b;
                              text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:5px;">
                    Nova Senha
                </label>
                <div style="display:flex;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;">
                    <input type="password" id="mSenhaNova" placeholder="••••••••" oninput="forcaM(this.value)"
                           style="flex:1;border:none;padding:9px 12px;font-size:0.88rem;outline:none;color:#1e293b;">
                    <button type="button" onclick="verM('mSenhaNova')"
                            style="border:none;background:transparent;padding:0 12px;color:#94a3b8;cursor:pointer;">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div style="margin-bottom:14px;">
                <div style="height:4px;background:#f1f5f9;border-radius:2px;margin-bottom:3px;">
                    <div id="mForcaBar" style="height:100%;border-radius:2px;transition:width .3s,background .3s;width:0%"></div>
                </div>
                <span id="mForcaLbl" style="font-size:0.72rem;font-weight:600;"></span>
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:0.73rem;font-weight:700;color:#64748b;
                              text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:5px;">
                    Confirmar Nova Senha
                </label>
                <div style="display:flex;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;">
                    <input type="password" id="mSenhaConf" placeholder="••••••••"
                           style="flex:1;border:none;padding:9px 12px;font-size:0.88rem;outline:none;color:#1e293b;">
                    <button type="button" onclick="verM('mSenhaConf')"
                            style="border:none;background:transparent;padding:0 12px;color:#94a3b8;cursor:pointer;">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div id="mSenhaFeedback" style="font-size:0.82rem;margin-bottom:12px;min-height:18px;text-align:center;font-weight:500;"></div>

            <div style="display:flex;gap:10px;">
                <button onclick="fecharModalSenha()"
                        style="flex:1;padding:9px;border:1px solid #e2e8f0;border-radius:10px;
                               background:#fff;color:#64748b;cursor:pointer;font-size:0.88rem;
                               transition:background .15s;"
                        onmouseenter="this.style.background='#f8fafc'"
                        onmouseleave="this.style.background='#fff'">
                    Cancelar
                </button>
                <button id="mBtnSalvarSenha" onclick="salvarSenha()"
                        style="flex:1;padding:9px;border:none;border-radius:10px;
                               background:linear-gradient(135deg,#1d4ed8,#3b82f6);
                               color:white;cursor:pointer;font-size:0.88rem;font-weight:600;
                               transition:opacity .15s;"
                        onmouseenter="this.style.opacity='0.9'"
                        onmouseleave="this.style.opacity='1'">
                    Salvar
                </button>
            </div>

        </div>
    </div>
</div>
