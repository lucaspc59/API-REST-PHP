// Event listener que executa quando todo conteúdo HTML foi carregado
document.addEventListener("DOMContentLoaded", carregarUsuarios);

// Adiciona evento de clique ao botão "Atualizar Lista" para recarregar os usuários
document.getElementById("atualizarLista").addEventListener("click", carregarUsuarios);

// Adiciona evento de submit ao formulário de cadastro (função assíncrona)
document.getElementById("formCadastro").addEventListener("submit", async function (event) {
    // Previne o comportamento padrão do formulário (não recarrega a página)
    event.preventDefault();

    // Cria objeto com dados coletados dos campos do formulário
    const formData = {
        nome: document.getElementById("nome").value,
        email: document.getElementById("email").value,
        endereco: document.getElementById("endereco").value,
        telefone: document.getElementById("telefone").value,
        senha: document.getElementById("senha").value
    };

    // Faz requisição POST para o servidor PHP enviando os dados em JSON
    const response = await fetch('usuarios.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },  // Define tipo de conteúdo como JSON
        body: JSON.stringify(formData)                    // Converte objeto para string JSON
    });

    // Converte resposta do servidor para objeto JavaScript
    const data = await response.json();
    // Exibe mensagem de retorno do servidor
    alert(data.message);

    // Limpa todos os campos do formulário após o envio bem-sucedido
    document.getElementById("formCadastro").reset();

    // Recarrega a lista de usuários para mostrar o novo cadastro
    carregarUsuarios();
});

// Função assíncrona para carregar e exibir lista de usuários
async function carregarUsuarios() {
    // Faz requisição GET para buscar todos os usuários
    const response = await fetch('usuarios.php');
    // Converte resposta JSON em array de objetos usuário
    const usuarios = await response.json();
    //Obtém referência do elemento HTML que contém a lista
    const lista = document.getElementById("listaUsuarios");
    // Limpa conteúdo anterior da lista
    lista.innerHTML = "";

    // Itera sobre cada usuário do array
    usuarios.forEach(usuario => {
        // Cria novo elemento de linha da tabela
        const linha = document.createElement("tr");
        // Define conteúdo HTML da linha com dados do usuário
        linha.innerHTML = `
        <td>${usuario.id}</td>
        <td>${usuario.nome}</td>
        <td>${usuario.email}</td>
        <td>
            <div class="botoes-acao">
                <button onclick="excluirUsuario('${usuario.id}')">Excluir</button>
                <button onclick="atualizarUsuario('${usuario.id}')">Atualizar</button>
            </div>
        </td>
    `;
        // Adiciona a linha criada à tabela
        lista.appendChild(linha);
    });
}

// Função assíncrona para excluir um usuário específico
async function excluirUsuario(id) {
    // Exibe confirmação; se usuário cancelar, interrompe execução
    if (!confirm("Deseja excluir este usuário?")) return;

    // Faz requisição DELETE para remover usuário do servidor
    const response = await fetch('usuarios.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })     // Envia ID do usuário a ser excluído
    });

    // Converte resposta do servidor para objeto JavaScript
    const data = await response.json();
    // Exibe mensagem de sucesso ou erro (com fallback)
    alert(data.message || "Erro ao excluir usuário");
    // Recarrega lista para refletir a exclusão
    carregarUsuarios();
}

// Função assíncrona para atualizar dados de um usuário
async function atualizarUsuario(id) {

    const nome = prompt("Novo nome:");
    const email = prompt("Novo e-mail:");
    const endereco = prompt("Novo endereco:");
    const telefone = prompt("Novo telefone:");

    // Faz requisição PUT para atualizar dados no servidor
    const response = await fetch('usuarios.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, nome, email, endereco, telefone }) // Envia dados atualizados
    });

    // Converte resposta do servidor para objeto JavaScript
    const data = await response.json();
    // Exibe mensagem de retorno do servidor
    alert(data.message);
    // Recarrega lista para mostrar dados atualizados
    carregarUsuarios();
}