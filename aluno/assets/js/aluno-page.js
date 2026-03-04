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
        perfil: document.getElementById('tela-perfil')
    };

    // Toggle do sidebar
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

    // Navegação entre telas
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            const target = this.getAttribute('data-target');
            
            // Remove active de todos os itens
            menuItems.forEach(i => i.classList.remove('active'));
            
            // Adiciona active no item clicado
            this.classList.add('active');
            
            // Esconde todas as telas
            Object.values(telas).forEach(tela => {
                if (tela) tela.classList.remove('ativa');
            });
            
            // Mostra a tela selecionada
            if (telas[target]) {
                telas[target].classList.add('ativa');
            }
            
            // No mobile, fecha sidebar após clicar
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

    // Fechar sidebar ao clicar fora (mobile)
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                sidebar.classList.remove('mobile-visible');
            }
        }
    });
});