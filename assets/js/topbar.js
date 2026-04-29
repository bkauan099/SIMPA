document.addEventListener("DOMContentLoaded", function () {

    const btnNotif = document.getElementById("btnNotificacoes");
    const dropdownNotif = document.getElementById("dropdownNotificacoes");
    const contador = document.getElementById("contadorNotificacao");

    const btnPerfil = document.getElementById("btnPerfil");
    const dropdownPerfil = document.getElementById("dropdownPerfil");

    // =========================
    // MOCK DE NOTIFICAÇÕES
    // =========================
    let notificacoes = JSON.parse(localStorage.getItem("notificacoes")) || [
        { id: 1, mensagem: "Seu projeto foi aprovado.", status: "nao_lida" },
        { id: 2, mensagem: "Nova tarefa adicionada.", status: "nao_lida" },
        { id: 3, mensagem: "Atualização no cronograma.", status: "nao_lida" }
    ];

    function salvarLocal() {
        localStorage.setItem("notificacoes", JSON.stringify(notificacoes));
    }

    // =========================
    // RENDERIZAÇÃO
    // =========================
    function renderNotificacoes() {
        dropdownNotif.innerHTML = `
            <div class="dropdown-header d-flex justify-content-between">
                <span>Notificações</span>
                <button id="lerTodas" class="btn btn-sm btn-light">Ler todas</button>
            </div>
        `;

        notificacoes.forEach(n => {
            dropdownNotif.innerHTML += `
                <div class="notificacao-item ${n.status}" data-id="${n.id}">
                    <p>${n.mensagem}</p>
                    <button class="marcar-lida">Lida</button>
                    <button class="marcar-nao-lida">Não lida</button>
                </div>
            `;
        });

        addEventos();
        atualizarContador();
    }

    // =========================
    // EVENTOS DAS NOTIFICAÇÕES
    // =========================
    function addEventos() {

        document.querySelectorAll(".marcar-lida").forEach(btn => {
            btn.addEventListener("click", function (e) {
                e.stopPropagation();

                const item = this.closest(".notificacao-item");
                const id = parseInt(item.dataset.id);

                notificacoes = notificacoes.map(n =>
                    n.id === id ? { ...n, status: "lida" } : n
                );

                salvarLocal();
                renderNotificacoes();
            });
        });

        document.querySelectorAll(".marcar-nao-lida").forEach(btn => {
            btn.addEventListener("click", function (e) {
                e.stopPropagation();

                const item = this.closest(".notificacao-item");
                const id = parseInt(item.dataset.id);

                notificacoes = notificacoes.map(n =>
                    n.id === id ? { ...n, status: "nao_lida" } : n
                );

                salvarLocal();
                renderNotificacoes();
            });
        });

        const lerTodas = document.getElementById("lerTodas");
        if (lerTodas) {
            lerTodas.addEventListener("click", function (e) {
                e.stopPropagation();

                notificacoes = notificacoes.map(n => ({
                    ...n,
                    status: "lida"
                }));

                salvarLocal();
                renderNotificacoes();
            });
        }
    }

    // =========================
    // CONTADOR
    // =========================
    function atualizarContador() {
        const naoLidas = notificacoes.filter(n => n.status === "nao_lida").length;

        contador.innerText = naoLidas;
        contador.style.display = naoLidas === 0 ? "none" : "inline-block";
    }

    // =========================
    // TOGGLES
    // =========================
    btnNotif.addEventListener("click", function (e) {
        e.stopPropagation();
        dropdownNotif.style.display =
            dropdownNotif.style.display === "block" ? "none" : "block";

        dropdownPerfil.style.display = "none";
    });

    btnPerfil.addEventListener("click", function (e) {
        e.stopPropagation();
        dropdownPerfil.style.display =
            dropdownPerfil.style.display === "block" ? "none" : "block";

        dropdownNotif.style.display = "none";
    });

    document.addEventListener("click", function () {
        dropdownNotif.style.display = "none";
        dropdownPerfil.style.display = "none";
    });

    // =========================
    // INIT
    // =========================
    renderNotificacoes();
});