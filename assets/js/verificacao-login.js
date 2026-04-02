document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("formLogin");
    const email = document.getElementById("email");
    const senha = document.getElementById("senha");
    const erroEmail = document.getElementById("erro-email");
    const erroSenha = document.getElementById("erro-senha");
    const btnEntrar = document.querySelector(".login");
    const toggleSenha = document.getElementById("toggleSenha");

    // Sempre começar com senha oculta e ícone do olho correto
    senha.type = "password";
    toggleSenha.innerHTML = '<i class="fa-solid fa-eye"></i>';
    toggleSenha.classList.remove("show");

    let enviando = false;

    // 🚀 SUBMIT
    form.addEventListener("submit", function (event) {

        event.preventDefault();

        if (enviando) return;

        let valido = true;

        // 🔴 valida email
        if (email.value.trim() === "") {
            erroEmail.classList.add("show");
            erroEmail.querySelector(".msg").innerText = "Digite o email";
            valido = false;
        }

        // 🔴 valida senha
        if (senha.value.trim() === "") {
            erroSenha.classList.add("show");
            erroSenha.querySelector(".msg").innerText = "Digite a senha";
            valido = false;
        }

        // 🚫 para tudo se inválido
        if (!valido) return;

        // 🔵 loader
        btnEntrar.disabled = true;
        btnEntrar.querySelector(".texto").style.display = "none";
        btnEntrar.querySelector(".loader").style.display = "inline-block";

        enviando = true;

        setTimeout(() => {

            const formData = new FormData(form);

            fetch("processa_login.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.json())
                .then(data => {

                    if (data.status === "ok") {
                        window.location.href = data.redirect;
                    } else {

                        // 🔴 erro de login
                        erroEmail.classList.add("show");
                        erroEmail.querySelector(".msg").innerText = data.mensagem;

                        // reset botão
                        btnEntrar.disabled = false;
                        btnEntrar.querySelector(".texto").style.display = "inline";
                        btnEntrar.querySelector(".loader").style.display = "none";

                        enviando = false;
                    }

                })
                .catch(() => {
                    alert("Erro na conexão");
                    enviando = false;
                });

        }, 500);

    });
    // 👁️ mostrar botão só quando digitar
    senha.addEventListener("input", function () {

        if (senha.value.length > 0) {
            toggleSenha.classList.add("show");
        } else {
            toggleSenha.classList.remove("show");
        }

    });
    // 👁️ mostrar/ocultar senha
    toggleSenha.addEventListener("click", function () {

        if (senha.type === "password") {
            senha.type = "text";
            toggleSenha.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
        } else {
            senha.type = "password";
            toggleSenha.innerHTML = '<i class="fa-solid fa-eye"></i>';
        }

    });

    // 🧹 limpar erro ao focar
    document.addEventListener("focusin", function (e) {

        if (e.target.id === "email" || e.target.id === "senha") {
            erroEmail.classList.remove("show");
            erroSenha.classList.remove("show");
        }

    });

    // 🔄 corrigir botão voltar (cache)
    window.addEventListener("pageshow", function (event) {

        if (event.persisted) {

            form.reset();

            btnEntrar.disabled = false;
            btnEntrar.querySelector(".texto").style.display = "inline";
            btnEntrar.querySelector(".loader").style.display = "none";

            erroEmail.classList.remove("show");
            erroSenha.classList.remove("show");

            // 🔴 ESCONDE O OLHO
            toggleSenha.classList.remove("show");
            senha.type = "password";
            toggleSenha.innerHTML = '<i class="fa-solid fa-eye"></i>';
            enviando = false;
        }

    });

    // 🧼 limpar URL
    window.history.replaceState({}, document.title, window.location.pathname);

    form.addEventListener("keydown", function (e) {

        if (e.key === "Enter") {

            // 🔴 impede comportamento estranho do Enter
            e.preventDefault();

            // 🔥 chama o submit normal (seu código já trata)
            form.dispatchEvent(new Event("submit", { cancelable: true }));

        }

    });

    toggleSenha.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
        }
    });

});