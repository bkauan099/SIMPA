<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Seletivos em Aberto</h3>
        <p class="text-muted mb-0">Oportunidades de participação em projetos e pesquisas da UEMA</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <span class="badge bg-danger" style="font-size:0.8rem;">7 abertos</span>
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
            <select class="form-select">
                <option value="">Status</option>
                <option>Aberto</option>
                <option>Encerra em breve</option>
            </select>
        </div>
        <div class="col-12 col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
    </div>
</div>

<!-- Cards de seletivos -->
<div id="listaSeletivos">

    <!-- Liga de Bovinos -->
    <div class="seletivo-card ic">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="badge" style="background:#ede9fe;color:#7c3aed;">Ligas Acadêmicas</span>
                    <span class="prazo-badge prazo-urgente">Encerra em 3 dias</span>
                </div>
                <h5 class="fw-bold mb-1">Liga de Bovinos — UEMA</h5>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    <i class="bi bi-person me-1"></i>Prof. Dr. Carlos Mendes &nbsp;·&nbsp;
                    <i class="bi bi-building me-1"></i>Dep. de Medicina Veterinária
                </p>
            </div>
        </div>
        <p style="font-size:0.88rem;color:#475569;margin-bottom:12px;">
            Liga acadêmica voltada ao estudo e pesquisa em bovinos, com foco em saúde animal, 
            produção e bem-estar. Atividades práticas em campo e laboratório.
        </p>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i>5 vagas</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>10h/semana</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-calendar me-1"></i>Inscrições: até 22/03/2026</span>
            </div>
            <button class="btn btn-sm btn-primary px-4">Candidatar-se</button>
        </div>
    </div>

    <!-- Liga de Suínos -->
    <div class="seletivo-card ic">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="badge" style="background:#ede9fe;color:#7c3aed;">Ligas Acadêmicas</span>
                    <span class="prazo-badge prazo-urgente">Encerra em 5 dias</span>
                </div>
                <h5 class="fw-bold mb-1">Liga de Suínos — UEMA</h5>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    <i class="bi bi-person me-1"></i>Profª Dra. Ana Paula Ferreira &nbsp;·&nbsp;
                    <i class="bi bi-building me-1"></i>Dep. de Zootecnia
                </p>
            </div>
        </div>
        <p style="font-size:0.88rem;color:#475569;margin-bottom:12px;">
            Liga dedicada ao estudo da suinocultura, abrangendo nutrição, sanidade, reprodução 
            e manejo de suínos. Voltada para alunos de Zootecnia e Medicina Veterinária.
        </p>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i>4 vagas</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>10h/semana</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-calendar me-1"></i>Inscrições: até 24/03/2026</span>
            </div>
            <button class="btn btn-sm btn-primary px-4">Candidatar-se</button>
        </div>
    </div>

    <!-- Bumba Meu Baja -->
    <div class="seletivo-card esp">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="badge" style="background:#fef3c7;color:#92400e;">Projeto Especial</span>
                    <span class="prazo-badge prazo-breve">Encerra em 10 dias</span>
                </div>
                <h5 class="fw-bold mb-1">Bumba Meu Baja — UEMA</h5>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    <i class="bi bi-person me-1"></i>Prof. Dr. Fábio Costa &nbsp;·&nbsp;
                    <i class="bi bi-building me-1"></i>Dep. de Engenharia Mecânica
                </p>
            </div>
        </div>
        <p style="font-size:0.88rem;color:#475569;margin-bottom:12px;">
            Equipe de competição Baja SAE Brasil com identidade cultural maranhense. Buscamos alunos 
            de Engenharia Mecânica, Elétrica e Computação para estrutura, eletrônica e sistemas embarcados.
        </p>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i>6 vagas</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>20h/semana</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-calendar me-1"></i>Inscrições: até 29/03/2026</span>
            </div>
            <button class="btn btn-sm btn-primary px-4">Candidatar-se</button>
        </div>
    </div>

    <!-- Vortex -->
    <div class="seletivo-card esp">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="badge" style="background:#fef3c7;color:#92400e;">Projeto Especial</span>
                    <span class="prazo-badge prazo-normal">Encerra em 15 dias</span>
                </div>
                <h5 class="fw-bold mb-1">Vortex — UEMA</h5>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    <i class="bi bi-person me-1"></i>Prof. Dr. Ricardo Alves &nbsp;·&nbsp;
                    <i class="bi bi-building me-1"></i>Dep. de Engenharia Elétrica
                </p>
            </div>
        </div>
        <p style="font-size:0.88rem;color:#475569;margin-bottom:12px;">
            Equipe de robótica e automação da UEMA. Desenvolve projetos em eletrônica embarcada, 
            programação e robôs autônomos. Participação em competições nacionais.
        </p>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i>5 vagas</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>15h/semana</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-calendar me-1"></i>Inscrições: até 03/04/2026</span>
            </div>
            <button class="btn btn-sm btn-primary px-4">Candidatar-se</button>
        </div>
    </div>

    <!-- Odisseia -->
    <div class="seletivo-card ext">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="badge" style="background:#d1fae5;color:#065f46;">Empresa Jr</span>
                    <span class="prazo-badge prazo-normal">Encerra em 18 dias</span>
                </div>
                <h5 class="fw-bold mb-1">Odisseia — UEMA</h5>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    <i class="bi bi-person me-1"></i>Profª Dra. Silvia Ramos &nbsp;·&nbsp;
                    <i class="bi bi-building me-1"></i>Dep. de Computação
                </p>
            </div>
        </div>
        <p style="font-size:0.88rem;color:#475569;margin-bottom:12px;">
            Empresa júnior de tecnologia da UEMA focada no desenvolvimento de soluções digitais 
            para clientes reais. Oportunidade de vivência profissional ainda na graduação.
        </p>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i>4 vagas</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>12h/semana</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-calendar me-1"></i>Inscrições: até 06/04/2026</span>
            </div>
            <button class="btn btn-sm btn-primary px-4">Candidatar-se</button>
        </div>
    </div>

    <!-- Ágora -->
    <div class="seletivo-card ext">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="badge" style="background:#d1fae5;color:#065f46;">Empresa Jr</span>
                    <span class="prazo-badge prazo-normal">Encerra em 20 dias</span>
                </div>
                <h5 class="fw-bold mb-1">Ágora — UEMA</h5>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    <i class="bi bi-person me-1"></i>Prof. Dr. André Nunes &nbsp;·&nbsp;
                    <i class="bi bi-building me-1"></i>Dep. de Direito / Ciências Sociais
                </p>
            </div>
        </div>
        <p style="font-size:0.88rem;color:#475569;margin-bottom:12px;">
            Projeto de extensão voltado ao debate político, cidadania e participação social. 
            Promove fóruns, simulações de assembleia e formação em liderança estudantil.
        </p>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i>8 vagas</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>6h/semana</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-calendar me-1"></i>Inscrições: até 08/04/2026</span>
            </div>
            <button class="btn btn-sm btn-primary px-4">Candidatar-se</button>
        </div>
    </div>

    <!-- Ágil -->
    <div class="seletivo-card ic">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="badge" style="background:#ede9fe;color:#7c3aed;">Projeto Especial</span>
                    <span class="prazo-badge prazo-normal">Encerra em 25 dias</span>
                </div>
                <h5 class="fw-bold mb-1">Ágil — UEMA</h5>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    <i class="bi bi-person me-1"></i>Prof. Dr. Bruno Kauan &nbsp;·&nbsp;
                    <i class="bi bi-building me-1"></i>PROEXAE / Computação
                </p>
            </div>
        </div>
        <p style="font-size:0.88rem;color:#475569;margin-bottom:12px;">
            Projeto focado em metodologias ágeis, gestão de projetos e desenvolvimento de software. 
            Ideal para alunos de Computação, Sistemas de Informação e Engenharia de Software.
        </p>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-light text-dark border"><i class="bi bi-people me-1"></i>5 vagas</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i>8h/semana</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-calendar me-1"></i>Inscrições: até 13/04/2026</span>
            </div>
            <button class="btn btn-sm btn-primary px-4">Candidatar-se</button>
        </div>
    </div>

</div>
