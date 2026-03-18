<div class="container-fluid mt-4">
    <div class="row">

        <!-- CENTRO -->
        <div class="col-lg-8">

            <div class="card card-custom p-4">
                <h3 class="mb-4">Tarefas e Eventos</h3>

                <!-- TAREFAS -->
                <h5><i class="bi bi-list-check"></i> Tarefas</h5>
                <table class="table table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>Título</th>
                            <th>Data</th>
                            <th>Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Revisão do Artigo</td>
                            <td>20/11/2023</td>
                            <td>10:00</td>
                        </tr>
                        <tr>
                            <td>Entrega Relatório</td>
                            <td>22/11/2023</td>
                            <td>14:00</td>
                        </tr>
                    </tbody>
                </table>

                <!-- EVENTOS -->
                <h5 class="mt-4"><i class="bi bi-calendar-event"></i> Eventos</h5>
                <table class="table table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>Título</th>
                            <th>Data</th>
                            <th>Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Apresentação Final</td>
                            <td>25/11/2023</td>
                            <td>10:00</td>
                        </tr>
                        <tr>
                            <td>Workshop Engenharia</td>
                            <td>28/11/2023</td>
                            <td>19:00</td>
                        </tr>
                    </tbody>
                </table>

            </div>

        </div>

        <!-- DIREITA -->
        <div class="col-lg-4">

            <!-- CALENDÁRIO -->
            <div class="card card-custom p-3 mb-3">
                <h6 class="text-center" id="mesAtual"></h6>

                <div class="d-flex text-center fw-bold mb-2">
                    <div class="w-100">Dom</div>
                    <div class="w-100">Seg</div>
                    <div class="w-100">Ter</div>
                    <div class="w-100">Qua</div>
                    <div class="w-100">Qui</div>
                    <div class="w-100">Sex</div>
                    <div class="w-100">Sáb</div>
                </div>

                <div id="calendar" class="d-flex flex-wrap"></div>
            </div>

            <!-- CERTIFICADOS -->
            <div class="card card-custom p-3 mb-3 text-center">
                <h6><i class="bi bi-award"></i> Certificados</h6>
                <h2>32</h2>
                <small>Conquistados</small>
            </div>

            <!-- CARGA HORÁRIA -->
            <div class="card card-custom p-3 text-center">
                <h6><i class="bi bi-clock"></i> Carga Horária</h6>
                <h2>285h</h2>
                <small>Acumuladas</small>
            </div>

        </div>

    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="dayModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Atividades do Dia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalContent"></div>
        </div>
    </div>
</div>

<style>
.card-custom {
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.calendar-day {
    width: 14.28%;
    text-align: center;
    padding: 10px 0;
    cursor: pointer;
    border-radius: 8px;
}

.calendar-day:hover {
    background-color: #0d6efd;
    color: white;
}

.today {
    background-color: #0d6efd;
    color: white;
    font-weight: bold;
}
</style>

<script>
function gerarCalendario() {

    const calendar = document.getElementById("calendar");
    const mesAtualTexto = document.getElementById("mesAtual");

    const hoje = new Date();
    const ano = hoje.getFullYear();
    const mes = hoje.getMonth();

    const primeiroDia = new Date(ano, mes, 1).getDay();
    const diasNoMes = new Date(ano, mes + 1, 0).getDate();

    const nomesMeses = [
        "Janeiro","Fevereiro","Março","Abril","Maio","Junho",
        "Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"
    ];

    mesAtualTexto.innerText = nomesMeses[mes] + " " + ano;

    calendar.innerHTML = "";

    for (let i = 0; i < primeiroDia; i++) {
        calendar.innerHTML += `<div class="calendar-day"></div>`;
    }

    for (let dia = 1; dia <= diasNoMes; dia++) {

        let classe = "calendar-day";

        if (dia === hoje.getDate()) {
            classe += " today";
        }

        calendar.innerHTML += `
            <div class="${classe}" onclick="showDay(${dia})">
                ${dia}
            </div>
        `;
    }
}

function showDay(dia) {

    let conteudo = "";

    if(dia == 20){
        conteudo = "📌 Revisão do Artigo - 10:00";
    } 
    else if(dia == 22){
        conteudo = "📌 Entrega Relatório - 14:00";
    }
    else if(dia == 25){
        conteudo = "🎤 Apresentação Final - 10:00";
    } 
    else if(dia == 28){
        conteudo = "🎤 Workshop Engenharia - 19:00";
    }
    else {
        conteudo = "Nenhuma atividade neste dia.";
    }

    document.getElementById("modalContent").innerHTML =
        `<strong>Dia ${dia}</strong><br><br>${conteudo}`;

    let modal = new bootstrap.Modal(document.getElementById('dayModal'));
    modal.show();
}

// IMPORTANTE: evita conflito com carregamento dinâmico
setTimeout(gerarCalendario, 100);
</script>