document.addEventListener("DOMContentLoaded", function(){

    const form = document.querySelector("form");

    const email = document.getElementById("email");
    const senha = document.getElementById("senha");

    const erroEmail = document.getElementById("erro-email");
    const erroSenha = document.getElementById("erro-senha");


    // VALIDAR AO ENVIAR
    form.addEventListener("submit", function(event){

        let valido = true;

        if(email.value.trim() === ""){
            erroEmail.classList.add("show");
            valido = false;
        }

        if(senha.value.trim() === ""){
            erroSenha.classList.add("show");
            valido = false;
        }

        if(!valido){
            event.preventDefault();
        }

    });

});

// REMOVER ERROS AO DIGITAR

document.addEventListener("focusin", function(e){

    if(e.target.id === "email" || e.target.id === "senha"){

        document.getElementById("erro-email")
            .classList.remove("show");

        document.getElementById("erro-senha")
            .classList.remove("show");

    }

});