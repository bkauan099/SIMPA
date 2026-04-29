<?php
session_start();

// 1. Verificação de Acesso
if (!isset($_SESSION["usuario"]) || $_SESSION["tipo"] != "professor") {
    header("Location: login-page.php");
    exit();
}

// 2. Definição da Página Atual
$page = isset($_GET['page']) ? $_GET['page'] : 'pagina-inicial';

// 3. Conexão e Busca de Tipos (Essencial para o Modal funcionar)
require_once 'conexao/conexao.php';
try {
    $stmt_tipos = $pdo->query("SELECT id_tipo, nome FROM tipo_projetos ORDER BY nome ASC");
    $tipos = $stmt_tipos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $tipos = [];
}

// 4. AJAX: Navegação dinâmica
if (!empty($_GET['ajax'])) {
    $allowed_pages = ['pagina-inicial', 'meus-projetos', 'alunos', 'tarefas', 'cronograma', 'documentos', 'relatorios'];
    if (in_array($page, $allowed_pages)) {
        include "pages-professor/{$page}.php";
    } else {
        echo "<div class='alert alert-danger'>Página não encontrada.</div>";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPA - UEMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/professor-page.css?v=4">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/pt.js"></script>
    <script>
        if (localStorage.getItem('sidebarExpanded') === 'true') {
            document.documentElement.classList.add('sidebar-pre-expanded');
        }
    </script>
    <style>
        /* Mantendo o padrão visual do SIMPA */
        .sidebar-pre-expanded #sidebar {
            width: 240px;
            min-width: 240px;
        }

        .sidebar-pre-expanded #sidebar .nav-label {
            display: inline;
        }

        .sidebar-pre-expanded #sidebar ul li a {
            justify-content: flex-start;
            padding: 12px 20px;
        }

        .sidebar-pre-expanded #sidebar ul li a i {
            width: 28px;
            margin-right: 4px;
        }

        .sidebar-pre-expanded #sidebar .sidebar-brand {
            opacity: 1;
            transform: translateX(0);
        }

        /* Container das setas e mês */
        .flatpickr-month {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            height: 45px !important;
            position: relative;
        }

        /* Centraliza o mês e coloca as setas coladas nele */
        .flatpickr-current-month {
            position: static !important;
            display: flex !important;
            align-items: center;
            width: auto !important;
            padding: 0 10px !important;
            order: 1;
            /* Mês no meio */
        }

        .flatpickr-prev-month {
            position: static !important;
            order: 0;
            /* Seta esquerda antes do mês */
            margin-right: 5px;
        }

        .flatpickr-next-month {
            position: static !important;
            order: 2;
            /* Seta direita logo após o mês */
            margin-right: 15px;
            /* Espaço para o ano não colar na seta */
        }

        /* Estilo do Dropdown do Ano (o que criamos via JS) */
        .ano-dropdown-simpa {
            order: 3;
            /* Fica por último, à direita */
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 2px 10px;
            font-weight: 600;
            color: var(--azul-uema, #003366);
            background-color: #fff;
            cursor: pointer;
            outline: none;
            font-family: 'Montserrat', sans-serif;
        }

        /* Esconde qualquer resquício de dropdown de mês ou ano padrão */
        .flatpickr-monthDropdown-months,
        .numInputWrapper {
            display: none !important;
        }

        /* 2. Ajuste do Ano: Sem borda, mas com a seta de volta */
        .flatpickr-month select,
        .ano-dropdown-simpa {
            border: none !important;
            outline: none !important;
            background: transparent !important;
            color: #333 !important;
            font-weight: 600 !important;
            cursor: pointer !important;

            /* Reativa a seta do dropdown de forma limpa */
            appearance: auto !important;
            -webkit-appearance: menulist !important;
            /* Força a seta no Chrome/Safari */
            -moz-appearance: menulist !important;
            /* Força a seta no Firefox */

            padding: 0 5px !important;
            margin-left: 10px !important;
        }

        /* 3. Garante que as setas laterais não fiquem azuis (opcional, para contraste) */
        .flatpickr-prev-month i,
        .flatpickr-next-month i {
            color: #666 !important;
        }

        /* Remove o negrito do nome do mês e aplica a cor azul */
        .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-current-month span.cur-month {
            font-weight: 500 !important;
            /* 500 é um peso médio, ou use 400 para normal */
        }

        #resultados_busca {
            max-height: 250px;
            overflow-y: auto;
            background: white;
            border: 1px solid #dee2e6;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 9999;
        }

        .modal-simpa {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            /* Fundo escuro transparente */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            /* Garante que fique acima do outro modal */
        }

        .modal-content-simpa {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            border: none;
        }
    </style>
</head>

<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="wrapper">
        <nav id="sidebar">
            <div class="sidebar-toggle-wrap">
                <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Menu">
                    <i class="bi bi-list"></i>
                </button>
                <div class="sidebar-brand">
                    <div>
                        <span class="sidebar-brand-text">SIMPA</span>
                        <span class="sidebar-brand-sub">Sistema Integrado de Monitoramento de Projetos Acadêmicos</span>
                    </div>
                </div>
            </div>

            <ul class="list-unstyled components">
                <li><a href="?page=pagina-inicial" class="<?= $page == 'pagina-inicial' ? 'active' : '' ?>"><i
                            class="bi bi-house-door"></i><span class="nav-label">Página Inicial</span></a></li>
                <li><a href="?page=meus-projetos" class="<?= $page == 'meus-projetos' ? 'active' : '' ?>"><i
                            class="bi bi-folder"></i><span class="nav-label">Meus Projetos</span></a></li>
                <li><a href="?page=alunos" class="<?= $page == 'alunos' ? 'active' : '' ?>"><i
                            class="bi bi-people"></i><span class="nav-label">Meus Alunos</span></a></li>
                <li><a href="?page=tarefas" class="<?= $page == 'tarefas' ? 'active' : '' ?>"><i
                            class="bi bi-check2-square"></i><span class="nav-label">Tarefas</span></a></li>
                <li><a href="?page=cronograma" class="<?= $page == 'cronograma' ? 'active' : '' ?>"><i
                            class="bi bi-calendar-event"></i><span class="nav-label">Cronograma</span></a></li>
                <li><a href="?page=documentos" class="<?= $page == 'documentos' ? 'active' : '' ?>"><i
                            class="bi bi-file-earmark-text"></i><span class="nav-label">Documentos</span></a></li>
                <li><a href="?page=relatorios" class="<?= $page == 'relatorios' ? 'active' : '' ?>"><i
                            class="bi bi-bar-chart-line"></i><span class="nav-label">Relatórios</span></a></li>
                <li class="sidebar-sair"><a href="logout.php"><i class="bi bi-box-arrow-left"></i><span
                            class="nav-label">Sair</span></a></li>
            </ul>
        </nav>

        <div id="content">
            <header class="navbar-custom">
                <div class="topbar-left">
                    <button class="topbar-toggle" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
                    <img src="assets/img/uema-logo.png" alt="UEMA" class="logo-uema-top">
                    <div class="logo-sep"></div>
                    <img src="assets/img/proexae-branco-semfundo.png" alt="ProExae" class="logo-proexae-top">
                </div>
                <div class="topbar-right">
                    <div class="d-flex align-items-center gap-2" style="cursor:pointer">
                        <img src="https://ui-avatars.com/api/?name=Professor&background=random" class="rounded-circle"
                            width="34">
                        <span class="fw-medium d-none d-sm-inline">Professor <i
                                class="bi bi-chevron-down small"></i></span>
                    </div>
                </div>
            </header>

            <div class="dashboard-container" id="ajaxContent">
                <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
                    <div id="alertaSucesso" class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4"
                        role="alert" style="border-left: 5px solid #16a34a;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <div><strong>Projeto cadastrado com sucesso!</strong></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php
                $allowed_pages = ['pagina-inicial', 'meus-projetos', 'alunos', 'tarefas', 'cronograma', 'documentos', 'relatorios'];
                if (in_array($page, $allowed_pages)) {
                    include "pages-professor/{$page}.php";
                } else {
                    include "pages-professor/pagina-inicial.php";
                }
                ?>
            </div>
        </div>
    </div>

    <div id="modalProjeto" class="modal-container">
        <div class="modal-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="m-0" style="color: var(--azul-uema); font-weight: 700;">Novo Projeto</h4>
                <button type="button" onclick="fecharQualquerModal()" class="btn-close"></button>
            </div>

            <form action="controllers/controller-professor/cadastrar-projeto.php" method="POST">
                <input type="hidden" name="pagina_origem" id="input_pagina_origem" value="<?= $page ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Título do Projeto</label>
                    <input type="text" name="titulo" class="form-control" required
                        placeholder="Ex: Monitoramento de Solo">
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Área</label>
                        <input type="text" name="area" class="form-control" placeholder="Ex: Agronomia">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select name="id_tipo" class="form-select" required>
                            <option value="" disabled selected>Selecione o tipo...</option>
                            <?php foreach ($tipos as $tipo): ?>
                                <option value="<?= $tipo['id_tipo'] ?>"><?= htmlspecialchars($tipo['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Descrição</label>
                    <textarea name="descricao" class="form-control" rows="3"></textarea>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Data de Início</label>
                        <div class="input-group">
                            <input type="text" name="data_inicio" id="data_inicio" class="form-control date-mask"
                                placeholder="dd/mm/aaaa" maxlength="10">
                            <span class="input-group-text btn-calendar" id="btn_inicio" style="cursor: pointer;"><i
                                    class="bi bi-calendar3"></i></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Data de Término</label>
                        <div class="input-group">
                            <input type="text" name="data_fim" id="data_fim" class="form-control date-mask"
                                placeholder="dd/mm/aaaa" maxlength="10">
                            <span class="input-group-text btn-calendar" id="btn_fim" style="cursor: pointer;"><i
                                    class="bi bi-calendar3"></i></span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Carga Horária (Horas)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-clock"></i></span>
                        <input type="number" name="carga_horaria" class="form-control" placeholder="Ex: 20" min="1"
                            required>
                    </div>
                    <small class="text-muted">Informe a carga horária média do projeto.</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" onclick="fecharModal()" class="btn btn-light border">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cadastrar Projeto</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalAlunos" class="modal-container">
        <div class="modal-content modal-lg">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="m-0 text-primary fw-bold">Gerenciar Alunos</h4>
                <button type="button" onclick="fecharQualquerModal()" class="btn-close"></button>
            </div>

            <div class="card bg-light border-0 mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Adicionar Novo Aluno</h6>
                    <div class="row g-2">
                        <div class="col-md-7 position-relative">
                            <input type="text" id="busca_aluno" class="form-control" placeholder="Nome ou matrícula..." autocomplete="off">
                            <input type="hidden" id="id_aluno_selecionado">
                            <div id="resultados_busca" class="list-group position-absolute w-100 shadow" style="z-index: 9999; display: none;"></div>
                        </div>
                        <div class="col-md-3">
                            <input type="number" id="ch_aluno" class="form-control" placeholder="CH (Horas)" min="1">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary w-100" onclick="dispararVinculo()">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="lista_alunos_projeto" class="table-responsive">
                <p class="text-center text-muted">Carregando alunos...</p>
            </div>
        </div>
    </div>

    <div id="modalConfirmacao" class="modal-simpa" style="display: none;">
        <div class="modal-content-simpa" style="max-width: 400px; text-align: center;">
            <div class="mb-4">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                <h4 class="fw-bold mt-3">Confirmar Exclusão</h4>
                <p class="text-muted">Tem certeza que deseja remover este aluno do projeto? Esta ação não pode ser desfeita.</p>
            </div>

            <div class="d-flex gap-2">
                <button type="button" id="btnConfirmarRemocao" class="btn btn-success w-100 fw-bold">SIM</button>
                <button type="button" class="btn btn-danger w-100 fw-bold" onclick="fecharConfirmacao()">NÃO</button>
            </div>
        </div>
    </div>

    <div id="modalAvisoCH" class="modal-simpa" style="display: none;">
        <div class="modal-content-simpa" style="max-width: 350px; text-align: center;">
            <div class="mb-4">
                <i class="bi bi-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                <h4 class="fw-bold mt-3">Atenção</h4>
                <p class="text-muted">Por favor, informe a <strong>Carga Horária</strong> do aluno para continuar.</p>
            </div>

            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-primary px-5 fw-bold" onclick="fecharAvisoCH()">OK</button>
            </div>
        </div>
    </div>

    <div id="modalAvisoSelecao" class="modal-simpa" style="display: none;">
        <div class="modal-content-simpa" style="max-width: 380px; text-align: center;">
            <div class="mb-4">
                <i class="bi bi-person-x text-danger" style="font-size: 3rem;"></i>
                <h4 class="fw-bold mt-3">Aluno não selecionado</h4>
                <p class="text-muted">Você precisa <strong>clicar em um nome</strong> na lista de sugestões para poder adicionar.</p>
            </div>

            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-danger px-5 fw-bold" onclick="fecharAvisoSelecao()">ENTENDI</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/ajax-nav.js"></script>

    <script>
        // 1. Variáveis Globais e Configurações
        let houveAlteracaoNoBanco = false;
        let fpInicio, fpFim;
        let alunoParaRemover = null;
        let projetoParaRemover = null;

        const configFlatpickr = {
            locale: "pt",
            dateFormat: "d/m/Y",
            allowInput: true,
            clickOpens: false,
            disableMobile: true,
            monthSelectorType: "static",
            prevArrow: '<i class="bi bi-chevron-left"></i>',
            nextArrow: '<i class="bi bi-chevron-right"></i>',
            onReady: function(selectedDates, dateStr, instance) {
                const oldYearInput = instance.calendarContainer.querySelector('.numInputWrapper');
                if (oldYearInput) oldYearInput.style.display = "none";
                const yearSelect = document.createElement("select");
                yearSelect.className = "ano-dropdown-simpa";
                for (let i = 2020; i <= 2030; i++) {
                    const opt = document.createElement("option");
                    opt.value = i;
                    opt.text = i;
                    if (i === instance.currentYear) opt.selected = true;
                    yearSelect.appendChild(opt);
                }
                yearSelect.addEventListener("change", (e) => instance.changeYear(parseInt(e.target.value)));
                instance.monthNav.appendChild(yearSelect);
            }
        };

        // 2. FUNÇÃO UNIVERSAL DE FECHAMENTO
        function fecharQualquerModal() {
            const modais = ['modalProjeto', 'modalAlunos', 'modalConfirmacao'];
            modais.forEach(id => {
                const m = document.getElementById(id);
                if (m) m.style.display = 'none';
            });

            if (document.getElementById('busca_aluno')) {
                document.getElementById('busca_aluno').value = '';
                document.getElementById('id_aluno_selecionado').value = '';
                document.getElementById('resultados_busca').style.display = 'none';
            }

            if (houveAlteracaoNoBanco) {
                window.location.reload();
            }
        }
        // 4. Inicialização da Busca de Alunos (AJAX)
        document.addEventListener('DOMContentLoaded', function() {
            const inputBusca = document.getElementById('busca_aluno');
            const divResultados = document.getElementById('resultados_busca');
            let debounceTimer;
            let abortController;

            if (inputBusca) {
                inputBusca.addEventListener('input', function() {
                    this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, "");
                    let termo = this.value.trim().toLowerCase();
                    if (termo.length < 1) {
                        divResultados.style.display = 'none';
                        return;
                    }

                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        if (abortController) abortController.abort();
                        abortController = new AbortController();
                        fetch(`controllers/controller-professor/buscar-alunos.php?busca=${termo}`, {
                                signal: abortController.signal
                            })
                            .then(res => res.text())
                            .then(html => {
                                divResultados.innerHTML = html;
                                divResultados.style.display = 'block';
                            });
                    }, 300);
                });
            }
            document.addEventListener('click', (e) => {
                if (divResultados && e.target !== inputBusca) divResultados.style.display = 'none';
            });
        });

        // 5. FUNÇÕES DE PROJETO (NOVO / EDITAR)
        function inicializarComponentes() {
            if (fpInicio) fpInicio.destroy();
            if (fpFim) fpFim.destroy();

            // Vincula o Flatpickr aos inputs
            fpInicio = flatpickr("#data_inicio", configFlatpickr);
            fpFim = flatpickr("#data_fim", configFlatpickr);

            // VINCULA O CLIQUE DOS ÍCONES (Isso estava faltando!)
            const btnI = document.getElementById('btn_inicio');
            const btnF = document.getElementById('btn_fim');
            if (btnI) btnI.onclick = () => fpInicio.open();
            if (btnF) btnF.onclick = () => fpFim.open();

            document.querySelectorAll('.date-mask').forEach(input => {
                input.addEventListener('input', (e) => {
                    let v = e.target.value.replace(/\D/g, "");
                    if (v.length >= 5) v = v.replace(/^(\d{2})(\d{2})(\d{0,4}).*/, "$1/$2/$3");
                    else if (v.length >= 3) v = v.replace(/^(\d{2})(\d{0,2}).*/, "$1/$2");
                    e.target.value = v;
                });
            });
        }

        function abrirModal() {
            const modal = document.getElementById('modalProjeto');
            const form = modal.querySelector('form');
            form.reset();
            modal.querySelector('h4').innerText = "Novo Projeto";
            form.action = "controllers/controller-professor/cadastrar-projeto.php";
            if (document.getElementById('id_projeto_edit')) document.getElementById('id_projeto_edit').remove();
            modal.style.display = 'flex';
            inicializarComponentes();
        }

        // --- FUNÇÃO QUE ESTAVA FALTANDO ---
        function abrirModalEditar(projeto) {
            const modal = document.getElementById('modalProjeto');
            const form = modal.querySelector('form');
            form.reset();
            modal.querySelector('h4').innerText = "Editar Projeto";
            form.querySelector('button[type="submit"]').innerText = "Salvar Alterações"
            form.action = "controllers/controller-professor/editar-projeto.php";

            // Preencher campos
            form.querySelector('input[name="titulo"]').value = projeto.titulo;
            form.querySelector('input[name="area"]').value = projeto.area;
            form.querySelector('textarea[name="descricao"]').value = projeto.descricao;
            form.querySelector('select[name="id_tipo"]').value = projeto.id_tipo;
            form.querySelector('input[name="carga_horaria"]').value = projeto.carga_horaria;

            // Datas (converte de AAAA-MM-DD para DD/MM/AAAA)
            if (projeto.data_inicio) {
                const d = projeto.data_inicio.split('-');
                document.getElementById('data_inicio').value = `${d[2]}/${d[1]}/${d[0]}`;
            }
            if (projeto.data_fim) {
                const d = projeto.data_fim.split('-');
                document.getElementById('data_fim').value = `${d[2]}/${d[1]}/${d[0]}`;
            }

            // Criar ou atualizar o campo oculto com o ID do projeto
            let inputId = document.getElementById('id_projeto_edit') || document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'id_projeto';
            inputId.id = 'id_projeto_edit';
            inputId.value = projeto.id_projeto;
            form.appendChild(inputId);

            modal.style.display = 'flex';
            inicializarComponentes();
        }

        // 6. FUNÇÕES DE ALUNOS
        function abrirModalAlunos(idProjeto) {
            const modal = document.getElementById('modalAlunos');
            const container = document.getElementById('lista_alunos_projeto');
            modal.setAttribute('data-id-projeto', idProjeto);
            modal.style.display = 'flex';
            container.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>`;
            fetch(`controllers/controller-professor/buscar-alunos.php?id_projeto=${idProjeto}`)
                .then(res => res.text())
                .then(html => {
                    container.innerHTML = html;
                });
        }

        function vincularAluno(idUsuario, idProjeto, chAluno) {
            const formData = new FormData();
            formData.append('acao', 'vincular');
            formData.append('id_usuario', idUsuario);
            formData.append('id_projeto', idProjeto);
            formData.append('carga_horaria', chAluno); // Envia para o servidor

            fetch('controllers/controller-professor/gerenciar-participacao.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        houveAlteracaoNoBanco = true;
                        document.getElementById('busca_aluno').value = '';
                        document.getElementById('id_aluno_selecionado').value = '';
                        document.getElementById('ch_aluno').value = ''; // Limpa o campo CH
                        abrirModalAlunos(idProjeto);
                    } else {
                        alert(data.mensagem || "Erro ao vincular.");
                    }
                });
        }

        function removerAluno(idUsuario, idProjeto) {
            alunoParaRemover = idUsuario;
            projetoParaRemover = idProjeto || document.getElementById('modalAlunos').getAttribute('data-id-projeto');
            document.getElementById('modalConfirmacao').style.display = 'flex';
            document.getElementById('btnConfirmarRemocao').onclick = executarExclusaoReal;
        }

        function fecharConfirmacao() {
            document.getElementById('modalConfirmacao').style.display = 'none';
        }

        function executarExclusaoReal() {
            const idProjetoAtual = document.getElementById('modalAlunos').getAttribute('data-id-projeto');
            const formData = new FormData();
            formData.append('acao', 'remover');
            formData.append('id_usuario', alunoParaRemover);
            formData.append('id_projeto', idProjetoAtual);
            fetch('controllers/controller-professor/gerenciar-participacao.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        houveAlteracaoNoBanco = true;
                        fecharConfirmacao();
                        abrirModalAlunos(idProjetoAtual);
                    }
                });
        }

        // Compatibilidade de nomes de funções
        function fecharModal() {
            fecharQualquerModal();
        }

        function fecharModalAlunos() {
            fecharQualquerModal();
        }

        function selecionarAluno(nome, id) {
            document.getElementById('busca_aluno').value = nome;
            document.getElementById('id_aluno_selecionado').value = id;
            document.getElementById('resultados_busca').style.display = 'none';
        }

        function dispararVinculo() {
            const idUsuario = document.getElementById('id_aluno_selecionado').value;
            const chAluno = document.getElementById('ch_aluno').value;
            const idProjeto = document.getElementById('modalAlunos').getAttribute('data-id-projeto');
            const textoBusca = document.getElementById('busca_aluno').value.trim();

            // ERRO 1: Digitou mas não clicou na sugestão (ID vazio)
            if (textoBusca !== "" && !idUsuario) {
                document.getElementById('modalAvisoSelecao').style.display = 'flex';
                return;
            }

            // ERRO 2: Não digitou nada
            if (!idUsuario) {
                document.getElementById('modalAvisoSelecao').style.display = 'flex';
                return;
            }

            // ERRO 3: Carga Horária vazia (Usa o modal que criamos antes)
            if (!chAluno || chAluno <= 0) {
                document.getElementById('modalAvisoCH').style.display = 'flex';
                return;
            }

            vincularAluno(idUsuario, idProjeto, chAluno);
        }

        // Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (window.innerWidth < 768) {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('expanded');
                localStorage.setItem('sidebarExpanded', sidebar.classList.contains('expanded'));
            }
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
        }

        function fecharAvisoCH() {
            document.getElementById('modalAvisoCH').style.display = 'none';
            // Opcional: foca no campo de carga horária para facilitar a vida do professor
            document.getElementById('ch_aluno').focus();
        }

        function fecharAvisoSelecao() {
            document.getElementById('modalAvisoSelecao').style.display = 'none';
            document.getElementById('busca_aluno').focus();
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") {
                const modalConfirm = document.getElementById('modalConfirmacao');
                const modalAvisoCH = document.getElementById('modalAvisoCH');
                const modalAvisoSel = document.getElementById('modalAvisoSelecao');

                if (modalConfirm && modalConfirm.style.display === 'flex') {
                    fecharConfirmacao();
                } else if (modalAvisoCH && modalAvisoCH.style.display === 'flex') {
                    fecharAvisoCH();
                } else if (modalAvisoSel && modalAvisoSel.style.display === 'flex') {
                    fecharAvisoSelecao();
                } else {
                    fecharQualquerModal();
                }
            }
        });
    </script>

</body>

</html>