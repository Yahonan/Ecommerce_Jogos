document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("cadastroForm");
    const email = document.getElementById("email");
    const senha = document.getElementById("senha");
    const confirmarSenha = document.getElementById("confirmar_senha"); 
    const mensagem = document.getElementById("js-mensagem"); 
    
    if (form && email && senha && confirmarSenha && mensagem) {
        form.addEventListener("submit", function(event) {
            event.preventDefault();
            
        
            mensagem.textContent = "";
            mensagem.className = "text-center mb-4 text-xs italic";
            mensagem.classList.remove('text-red-500', 'font-bold');

            let temErro = false; 

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
            const emailValue = email.value.trim();
            if (!emailValue) {
                mensagem.textContent = "Por favor, insira um endereço de email.";
                mensagem.classList.add('text-red-500', 'font-bold');
                temErro = true;
            } else if (!emailRegex.test(emailValue)) {
                mensagem.textContent = "Por favor, insira um endereço de email válido.";
                mensagem.classList.add('text-red-500', 'font-bold');
                temErro = true;
            }
            else if (!senha.value) {
                mensagem.textContent = "Por favor, insira uma senha.";
                mensagem.classList.add('text-red-500', 'font-bold');
                temErro = true;
            } else if (senha.value.length < 5) {
                mensagem.textContent = "A senha deve ter no mínimo 5 caracteres.";
                mensagem.classList.add('text-red-500', 'font-bold');
                temErro = true;
            }
            else if (!confirmarSenha.value) {
                mensagem.textContent = "Por favor, confirme sua senha.";
                mensagem.classList.add('text-red-500', 'font-bold');
                temErro = true;
            } else if (senha.value !== confirmarSenha.value) {
                mensagem.textContent = "As senhas não coincidem.";
                mensagem.classList.add('text-red-500', 'font-bold');
                temErro = true;
            }

            if (!temErro) {
                form.submit();
            }
        });
    } else {
        console.error("Erro: Elementos do formulário não encontrados. Verifique os IDs no HTML.");
    }
});