<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET, POST');

    // Inclui as funções externas
    require_once 'funcoes.php';
    require_once 'uuid.php';

    $method = $_SERVER['REQUEST_METHOD'];

    
    if($method === 'POST'){
        $input = json_decode(file_get_contents('php://input'), true);

        // Verifica campos obrigatórios 
        $camposObrigatorios = ['nome', 'email', 'senha', 'endereco', 'telefone'];
        foreach ($camposObrigatorios as $campo) {
            if (empty($input[$campo])) {
                responderErro("Campo obrigatório ausente: $campo");
            }
        }

        $usuarios = carregarUsuarios();
        

        // Verifica duplicidade de e-mail
        if (emailJaCadastrado($usuarios, $input['email'])){
            responderErro('E-mail já cadastrado');
        
        }

        // Cria novo usuário
        $novoUsuario = [
            "id" => gerarUUID(),
            "nome" => $input['nome'],
            "email" => $input['email'],
            "endereco" => $input['endereco'],
            "telefone" => $input['telefone'],
            "senha" => password_hash($input['senha'], PASSWORD_BCRYPT),
            
        ];

        $usuarios[] = $novoUsuario;
        salvarUsuarios($usuarios);

        // Remove a senha do retorno JSON
        unset($novoUsuario['senha']);

        echo json_encode([
            'message' => 'Usuário cadastrado com sucesso',
            'usuario' => $novoUsuario
        ], JSON_UNESCAPED_UNICODE);



    } elseif ($method === 'GET') {

        $usuarios = carregarUsuarios();
        echo json_encode($usuarios, JSON_UNESCAPED_UNICODE);

    }
    
    elseif ($method === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);

        // Verifica se o ID foi enviado
        if (empty($input['id'])) {
            responderErro('ID do usuário é obrigatório.');

        }

        // Carrega usuários
        $usuarios = carregarUsuarios();
        $usuarioEncontrado = false;

        // Percorre usuários para localizar o correto pelo ID
        foreach ($usuarios as &$usuario) {
            if ($usuario['id'] == $input ['id']) {
                // Atualiza os campos permitidos
                $usuario['nome'] = $input['nome'] ?? $usuario['nome'];
                $usuario['email'] = $input['email'] ?? $usuario['email'];
                $usuario['endereco'] = $input['endereco'] ?? $usuario['endereco'];
                $usuario['telefone'] = $input['telefone'] ?? $usuario ['telefone'];
                $usuarioEncontrado = true;
                break;
            }
        }
    
    if (!$usuarioEncontrado) {
        responderErro('Usuário não encontrado.');
    }
    
    // Salva a atualização
    salvarUsuarios($usuarios);

    echo json_encode([
        'message' => 'Usuário atualizado com sucesso',
        'usuario' => $usuario
    ], JSON_UNESCAPED_UNICODE);

    }
    
    
    else {
        responderErro('Método não permitido', 405);
    }
?>
