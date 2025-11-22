document.addEventListener("DOMContentLoaded", function(){
    const form = document.getElementById("cadastroForm");
    const senha = document.getElementById("senha");
    const confirmarSenha = document.getElementById("confirmarSenha");
    const mensagem = document.getElementsById("mensagem");

    form.addEventListener("submit", function(event){
        mensagem.textContent = "";
        mensagem.className = "";

    if(senha.value !== confirmarSenha.value){
        event.preventDefault();
        mensagem.textContent = "As senhas não coincidem"
        mensagem.classlist.add('text-red-500', 'font-bold', 'mt-2');
        return;
        }

    if(senha.value.length < 5){
        event.preventDefault();
        mensagem.textContent = "A senha deve ter no mínimo 5 caracteres";
        mensagem.classlist.add('text-red-500', 'font-bold', 'mt-2');
        return;
    }
    })
})