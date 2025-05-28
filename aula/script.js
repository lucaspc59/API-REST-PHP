document.addEventListener("DOMContentLoaded", carregarUsuarios);

document.getElementById("atualizarLista").addEventListener("click", carregarUsuarios);
document.getElementById("formCadastro").addEventListener("submit", async function (event) {
    event.preventDefault();

    const formData = {
        nome: document.getElementById("nome").value,
        email: document.getElementById("email").value,
        endereco: document.getElementById("enderco").value,
        telefone: document.getElementById("telefone").valeu,
        senha: document.getElementById("senha").value
    };

    const response = await fetch('usuarios.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    });

    const data = await response.json();
    alert(data.message);
    carregarUsuarios();
});

async function carregarUsuarios() {
    const response = await fetch('usuarios.php');
    const usuarios = await response.json();
    const lista = document.getElementById("listaUsuarios");
    lista.innerHTML = "";

    usuarios.forEach(usuario => {
        const linha = document.createElement("tr");
        linha.innerHTML = `
        <td>${usuario.id}</td>
        <td>${usuario.nome}</td>
        <td>${usuario.email}</td>
        <td>
            <button onclick="excluirUsuario('${usuario.id}')">Excluir</button>
            <button onclick="atualizarUsuario('${usuario.id}')">Atualizar</button>
        </td>
    `;
        lista.appendChild(linha);
    });
}

async function excluirUsuario(id) {
    if (!confirm("Deseja excluir este usuário?")) return;

    const response = await fetch('usuarios.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    });

    const data = await response.json();
    alert(data.message);
    carregarUsuarios();
}

async function atualizarUsuario(id) {
    const nome = prompt("Novo nome:");
    const email = prompt("Novo e-mail:");
    const endereco = prompt("Novo endereco:");
    const telefone = prompt("Novo telefone:");

    const response = await fetch('usuarios.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, nome, email, endereco, telefone })
    });

    const data = await response.json();
    alert(data.message);
    carregarUsuarios();

}