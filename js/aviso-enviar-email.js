const formCadastro = document.getElementById("FormCadastro");
const formRecuperarSenha = document.getElementById("FormRecuperarSenha");

function mostrarCarregamento(mensagem){
    document.getElementById("AvisarEnvioEmail").classList.remove("hidden");
    const botao = document.querySelector(".btn-cadastrar");
    botao.disabled = true;
    botao.classList.add("enviando-email-mensagem");
    botao.innerHTML = mensagem;
};

// FORM DO CADASTRO
if (formCadastro) {
    formCadastro.addEventListener("submit", function(){
        mostrarCarregamento("Enviando e-mail...");
    });
};

//FORM RECUPERAR SENHA
if (formRecuperarSenha) {
    formRecuperarSenha.addEventListener("submit", function(){
        mostrarCarregamento("Enviando e-mail de recuperação...");
    });
};