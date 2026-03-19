<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Seletivos em Aberto</h3>
        <p class="text-muted mb-0">Oportunidades de participação em projetos e pesquisas da UEMA</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <span class="badge bg-danger" style="font-size:0.8rem;">3 novos</span>
    </div>
</div>

<!-- Filtros -->
<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Buscar seletivo..." id="filtroSeletivo">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select" id="filtroTipo">
                <option value="">Tipo (Todos)</option>
                <option value="esp">Projeto Especial</option>
                <option value="liga">Ligas Acadêmicas</option>
                <option value="jr">Empresa Jr</option>
                <option value="atl">Atlética</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select class="form-select" id="filtroVinculo">
                <option value="">Vínculo</option>
            </select>
        </div>
        <div class="col-12 col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
    </div>
</div>

<!-- Cards de seletivos -->
<div id="listaSeletivos">

    <!-- IC 1 -->
    <div class="seletivo-card ic">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="badge" style="background:#ede9fe;color:#7c3aed;">Ligas Acadêmicas</span>
                    <span class="prazo-badge prazo-urgente">Encerra em 3 dias</span>
                </div>
                <h5 class="fw-bold mb-1">Análise de Algoritmos de IA aplicados à Agricultura de Precisão</h5>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    <i class="bi bi-person me-1"></i>Prof. Dr. Carlos Mendes &nbsp;·&nbsp;
                    <i class="bi bi-building me-1"></i>Dep. de Computação
                </p>
            </div>
        </div>
        <p style="font-size:0.88rem;color:#475569;margin-bottom:12px;">
            Pesquisa sobre aplicação de redes neurais para identificação de pragas em culturas agrícolas.
            Exige conhecimento em Python e interesse em Machine Learning. Dedicação de 20h semanais.
        </p>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i>1 vaga</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>20h/semana</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-calendar me-1"></i>Inscrições: até 21/03/2026</span>
            </div>
            <button class="btn btn-sm btn-primary px-4">Candidatar-se</button>
        </div>
    </div>

    <!-- IC 2 -->
    <div class="seletivo-card ic">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="badge" style="background:#ede9fe;color:#7c3aed;">Projeto Especial</span>
                    <span class="prazo-badge prazo-breve">Encerra em 10 dias</span>
                </div>
                <h5 class="fw-bold mb-1">Impacto das Mudanças Climáticas na Biodiversidade do Cerrado Maranhense</h5>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    <i class="bi bi-person me-1"></i>Profª Dra. Márcia Lima &nbsp;·&nbsp;
                    <i class="bi bi-building me-1"></i>Dep. de Biologia
                </p>
            </div>
        </div>
        <p style="font-size:0.88rem;color:#475569;margin-bottom:12px;">
            Levantamento e análise de dados ambientais na região do cerrado. Participação em campo
            uma vez por mês. Ideal para alunos de Ciências Ambientais, Biologia ou áreas correlatas.
        </p>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i>2 vagas</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>10h/semana</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-calendar me-1"></i>Inscrições: até 28/03/2026</span>
            </div>
            <button class="btn btn-sm btn-primary px-4">Candidatar-se</button>
        </div>
    </div>

    <!-- Extensão -->
    <div class="seletivo-card ext">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="badge" style="background:#d1fae5;color:#065f46;">Empresa Jr</span>
                    <span class="prazo-badge prazo-normal">Encerra em 15 dias</span>
                </div>
                <h5 class="fw-bold mb-1">Projeto Inclui — Tecnologia Assistiva para Pessoas com Deficiência</h5>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    <i class="bi bi-person me-1"></i>Prof. Dr. Ricardo Alves &nbsp;·&nbsp;
                    <i class="bi bi-building me-1"></i>PROEXAE / Computação
                </p>
            </div>
        </div>
        <p style="font-size:0.88rem;color:#475569;margin-bottom:12px;">
            Desenvolvimento de aplicativo mobile para comunicação alternativa. Equipe multidisciplinar.
            Exige interesse em acessibilidade e desenvolvimento mobile (React Native ou Flutter).
        </p>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i>3 vagas</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>15h/semana</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-calendar me-1"></i>Inscrições: até 02/04/2026</span>
            </div>
            <button class="btn btn-sm btn-primary px-4">Candidatar-se</button>
        </div>
    </div>

    <!-- Projeto Especial -->
    <div class="seletivo-card esp">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="badge" style="background:#fef3c7;color:#92400e;">Atlética</span>
                    <span class="prazo-badge prazo-normal">Encerra em 20 dias</span>
                </div>
                <h5 class="fw-bold mb-1">Equipe Baja UEMA — Engenharia Off-Road 2026</h5>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    <i class="bi bi-person me-1"></i>Prof. Dr. Fábio Costa &nbsp;·&nbsp;
                    <i class="bi bi-building me-1"></i>Dep. de Engenharia Mecânica
                </p>
            </div>
        </div>
        <p style="font-size:0.88rem;color:#475569;margin-bottom:12px;">
            Equipe de competição Baja SAE Brasil. Buscamos alunos de Engenharia Mecânica, Elétrica
            e Computação para as áreas de estrutura, eletrônica e sistemas embarcados. Alta dedicação exigida.
        </p>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i>4 vagas</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>20h/semana</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-calendar me-1"></i>Inscrições: até 07/04/2026</span>
            </div>
            <button class="btn btn-sm btn-primary px-4">Candidatar-se</button>
        </div>
    </div>

    <!-- Extensão voluntária -->
    <div class="seletivo-card ext">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="badge" style="background:#d1fae5;color:#065f46;">Empresa Jr</span>
                    <span class="prazo-badge prazo-normal">Encerra em 25 dias</span>
                </div>
                <h5 class="fw-bold mb-1">Monitoria de Programação para Alunos do Ensino Médio</h5>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    <i class="bi bi-person me-1"></i>Profª Dra. Silvia Ramos &nbsp;·&nbsp;
                    <i class="bi bi-building me-1"></i>PROEXAE / Computação
                </p>
            </div>
        </div>
        <p style="font-size:0.88rem;color:#475569;margin-bottom:12px;">
            Ação de extensão para ensinar lógica e programação básica a estudantes do ensino médio de
            escolas públicas de São Luís. Atividade presencial às sextas-feiras.
        </p>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i>5 vagas</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>6h/semana</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-calendar me-1"></i>Inscrições: até 12/04/2026</span>
            </div>
            <button class="btn btn-sm btn-primary px-4">Candidatar-se</button>
        </div>
    </div>

</div>
