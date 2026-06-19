// C:\xampp\htdocs\filmix\js\funcoes.js

function toggleSenha(idCampo, idOlhoAberto, idOlhoFechado) {
    const campoSenha = document.getElementById(idCampo);
    const olhoAberto = document.getElementById(idOlhoAberto);
    const olhoFechado = document.getElementById(idOlhoFechado);

    if (campoSenha.type === "password") {
        campoSenha.type = "text";
        olhoAberto.classList.add('d-none');     // Esconde o olho aberto
        olhoFechado.classList.remove('d-none'); // Mostra o olho fechado
    } else {
        campoSenha.type = "password";
        olhoFechado.classList.add('d-none');    // Esconde o olho fechado
        olhoAberto.classList.remove('d-none');  // Mostra o olho aberto
    }
}