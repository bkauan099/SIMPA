document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');
    const menuItems = document.querySelectorAll('.menu-item');
    const telas = {
        dashboard: document.getElementById('tela-dashboard'),
        projetos: document.getElementById('tela-projetos'),
        atividades: document.getElementById('tela-atividades'),
        documentos: document.getElementById('tela-documentos'),
        certificados: document.getElementById('tela-certificados'),
        admin: document.getElementById('tela-admin'),
        estudante: document.getElementById('tela-estudante'),
        professor: document.getElementById('tela-professor')
    };

    // Toggle do sidebar (sempre colapsa/expande no desktop)
    if (menuToggle) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            
            if (window.innerWidth <= 768) {
                // Mobile: abre/fecha sidebar
                sidebar.classList.toggle('mobile-visible');
            } else {
                // Desktop: colapsa/expande
                sidebar.classList.toggle('collapsed');
            }
        });
    }

    // Navegação entre telas (menu principal)
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            const target = this.getAttribute('data-target');
            
            menuItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            
            Object.values(telas).forEach(tela => {
                if (tela) tela.classList.remove('ativa');
            });
            
            if (telas[target]) {
                telas[target].classList.add('ativa');
            }
            
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('mobile-visible');
            }
        });
    });

    // Toggle do submenu PERFIL
    const perfilToggle = document.getElementById('perfilToggle');
    const menuPerfil = document.querySelector('.menu-perfil');

    if (perfilToggle) {
        perfilToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            menuPerfil.classList.toggle('open');
        });
    }

    // Navegação pelos subitens de perfil
    document.querySelectorAll('.submenu-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            const perfil = this.getAttribute('data-perfil');
            
            document.querySelectorAll('.tela').forEach(tela => tela.classList.remove('ativa'));
            
            const telaId = 'tela-' + perfil;
            if (document.getElementById(telaId)) {
                document.getElementById(telaId).classList.add('ativa');
            }
            
            // Remove active do menu principal
            document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
            
            // Fecha o submenu
            menuPerfil.classList.remove('open');
            
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('mobile-visible');
            }
        });
    });

    // Filtro da tabela
    const filtroTabela = document.getElementById('filtroTabela');
    if (filtroTabela) {
        filtroTabela.addEventListener('keyup', function() {
            const busca = this.value.toLowerCase();
            const linhas = document.querySelectorAll('#tabelaProjetos tbody tr');
            
            linhas.forEach(linha => {
                const conteudo = linha.innerText.toLowerCase();
                linha.style.display = conteudo.includes(busca) ? '' : 'none';
            });
        });
    }

    // Toggle das notificações
    const notificacoesBtn = document.getElementById('notificacoesBtn');
    const notificacoesDropdown = document.getElementById('notificacoesDropdown');

    if (notificacoesBtn && notificacoesDropdown) {
        notificacoesBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificacoesDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
            if (!notificacoesBtn.contains(e.target) && !notificacoesDropdown.contains(e.target)) {
                notificacoesDropdown.classList.remove('show');
            }
        });
    }

    // Marcar todas como lidas
    document.querySelector('.marcar-lidas')?.addEventListener('click', function() {
        const badge = document.querySelector('.badge-notificacao');
        if (badge) badge.textContent = '0';
    });

    // Fechar sidebar ao clicar fora (mobile)
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                sidebar.classList.remove('mobile-visible');
            }
        }
    });
});