// Seleciona o formulário (no seu caso é o único da página)
const form = document.querySelector('form');

form.addEventListener('submit', function(event) {
    // 1. Captura os inputs e os spans de erro
    const email = document.getElementById('email');
    const senha = document.getElementById('senha');
    const erroEmail = document.getElementById('erro-email');
    const erroSenha = document.getElementById('erro-senha');

    let valido = true;

    // 2. Validação do Usuário (E-mail)
    if (email.value.trim() === "") {
        email.style.border = "2px solid #e74c3c";
        erroEmail.style.display = "block";
        valido = false;
    } else {
        email.style.border = "1px solid #ccc";
        erroEmail.style.display = "none";
    }

    // 3. Validação da Senha
    if (senha.value.trim() === "") {
        senha.style.border = "2px solid #e74c3c";
        erroSenha.style.display = "block";
        valido = false;
    } else {
        senha.style.border = "1px solid #ccc";
        erroSenha.style.display = "none";
    }

    // 4. Se algum campo estiver vazio, cancela o evento de envio
    if (!valido) {
        event.preventDefault();
    } else {
        // Opcional: Se os dados baterem com o seu PHP (admin@gmail.com / 123456)
        // O formulário seguirá naturalmente para o arquivo definido no 'action'
        console.log("Validado com sucesso. Enviando...");
    }
});