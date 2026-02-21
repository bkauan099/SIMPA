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
        erroEmail.classList.add('show');
        valido = false;
    } else {
        erroEmail.classList.remove('show');
    }

    // 3. Validação da Senha
    if (senha.value.trim() === "") {
        erroSenha.classList.add('show');
        valido = false;
    } else {
        erroSenha.classList.remove('show');
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