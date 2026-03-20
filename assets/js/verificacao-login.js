document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("formLogin");

    const email = document.getElementById("email");
    const senha = document.getElementById("senha");

    const erroEmail = document.getElementById("erro-email");
    const erroSenha = document.getElementById("erro-senha");

    const btnEntrar = document.querySelector(".login");


    // VALIDAR AO ENVIAR
    let enviando = false;

    form.addEventListener("submit", function (event) {

        if (enviando) return;

        let valido = true;

        if (email.value.trim() === "") {
            erroEmail.classList.add("show");
            valido = false;
        }

        if (senha.value.trim() === "") {
            erroSenha.classList.add("show");
            valido = false;
        }

        if (!valido) {
            event.preventDefault();
            return;
        }

        // ⛔ impede envio imediato
        event.preventDefault();

        // 🔵 ativa loader
        btnEntrar.disabled = true;

        btnEntrar.querySelector(".texto").style.display = "none";
        btnEntrar.querySelector(".loader").style.display = "inline-block";

        enviando = true;

        // ⏳ espera 2 segundos
        setTimeout(() => {
            form.submit();
        }, 2000);

    });


    // REMOVER ERROS AO CLICAR
    document.addEventListener("focusin", function (e) {

        if (e.target.id === "email" || e.target.id === "senha") {

            document.getElementById("erro-email").classList.remove("show");
            document.getElementById("erro-senha").classList.remove("show");

        }

    });

});