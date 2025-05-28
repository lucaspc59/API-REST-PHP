<?php
// Define cabeçalhos para permitir requisições de diferentes origens (CORS)
header('Access-Control-Allow-Origin: *');

// Define que a resposta será no formato JSON
header('Content-Type: application/json');

//Especifica quais métodos HTTP são permitidos na API
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

// Importa funções externas do arquivo 'funcoes.php'
require_once 'funcoes.php';

// Obtém o método HTTP da requisição (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// Lógica para processar requisições do tipo POST (Cadastro de usuários)
if ($method === 'POST') {
    // Captura e converte dados recebidos em JSON para um array associativo
    $input = json_decode(file_get_contents('php://input'), true);

    // Lista dos campos obrigatórios no cadastro de usuários
    $camposObrigatorios = ['nome', 'email', 'senha', 'endereco', 'telefone'];

    // Verifica sae algum campo obrigatório está ausente
    foreach ($camposObrigatorios as $campo) {
        if (empty($input[$campo])) {
            responderErro("Campo obrigatório ausente: $campo");
        }
    }

    // Carrega a lista de usuários cadastrados
    $usuarios = carregarUsuarios();

    // Verifica se o e-mail informado já está cadastrado
    if (emailJaCadastrado($usuarios, $input['email'])) {
        responderErro('E-mail já cadastrado');
    }

    // Cria um novo usuário com ID único (UUID)
    $novoUsuario = [
        "id" => gerarUUID(),
        "nome" => $input['nome'],
        "email" => $input['email'],
        "endereco" => $input['endereco'],
        "telefone" => $input['telefone'],
        "senha" => password_hash($input['senha'], PASSWORD_BCRYPT),
    ];

    // Adiciona o novo usuário à lista
    $usuarios[] = $novoUsuario;

    // Salva a lista de usuários atualizada no arquivo
    salvarUsuarios($usuarios);

    // Remove a senha do retorno JSON para segurança
    unset($novoUsuario['senha']);

    // Retorna mensagem de sucesso e os dados do usuário cadastrado
    echo json_encode([
        'message' => 'Usuário cadastrado com sucesso',
        'usuario' => $novoUsuario
    ], JSON_UNESCAPED_UNICODE);
}

// Lógica para processar requisições do tipo GET (Listar usuários)
elseif ($method === 'GET') {
    // Carrega a lista de usuários do arquivo JSON
    $usuarios = carregarUsuarios();

    // Retorna a lista de usuáros cadastrados
    echo json_encode($usuarios, JSON_UNESCAPED_UNICODE);
}

// Lógica para processar requisições do tipo PUT (Atualizar usuários)
elseif ($method === 'PUT') {
    // Captura os dados recebidos e transforma em array associativo
    $input = json_decode(file_get_contents('php://input'), true);

    // Verifica se um ID foi enviado para identificar o usuário
    if (empty($input['id'])) {
        responderErro('ID do usuário é obrigatório.');
    }

    // Carrega a lista de usuários
    $usuarios = carregarUsuarios();
    $usuarioEncontrado = false;

    //Percorre a lista de usuários para encontrar o usuário pelo ID
    foreach ($usuarios as &$usuario) {
        if ($usuario['id'] == $input['id']) {
            $usuario['nome'] = $input['nome'] ?? $usuario['nome'];
            $usuario['email'] = $input['email'] ?? $usuario['email'];
            $usuario['endereco'] = $input['endereco'] ?? $usuario['endereco'];
            $usuario['telefone'] = $input['telefone'] ?? $usuario['telefone'];
            $usuarioEncontrado = true;
            break;
        }
    }

    // Se o usuário não for encontrado, retorna erro
    if (!$usuarioEncontrado) {
        responderErro('Usuário não encontrado.');
    }

    // Salva a lista de usuários atualizada
    salvarUsuarios($usuarios);

    // Retorna mensagem de sucesso e os dados atualizados do usuário
    echo json_encode([
        'message' => 'Usuário atualizado com sucesso',
        'usuario' => $usuario
    ], JSON_UNESCAPED_UNICODE);
}

// Lógica para processar requisições do tipo DELETE (Excluir usuário)
elseif ($method === 'DELETE') {
    // Captura os dados enviados e os transforma em array associativo
    $input = json_decode(file_get_contents('php://input'), true);

    // Verifica se um ID foi enviado
    if (empty($input['id'])) {
        responderErro('ID do usuário é obrigatório.');
    }

    // Carrega a lista de usuários
    $usuarios = carregarUsuarios();

    // Filtra a lista, removendo o usuário com o ID enviado
    $usuariosAtualizados = array_filter($usuarios, function ($usuario) use ($input) {
        return $usuario['id'] !== $input['id'];
    });

    // Se a lista de usuários não mudou, significa que o ID não foi encontrado
    if (count($usuariosAtualizados) === count($usuarios)) {
        responderErro('Usuário não encontrado.');
    }

    // Salva a lista atualizada sem o usuário excluído
    salvarUsuarios(array_values($usuariosAtualizados));

    // Retorna mensagem de sucesso
    echo json_encode(['message' => 'Usuário excluído com sucesso'], JSON_UNESCAPED_UNICODE);
}

// Caso o método HTTP enviado não seja permitido
else {
    responderErro('Método não permitido', 405);
}
?>
