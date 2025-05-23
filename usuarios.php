<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET, POST');

    // Inclui as funções externas
    require_once 'funcoes.php';

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
            "id" => count($usuarios) + 1,
            "nome" => $input['nome'],
            "email" => $input['email'],
            "endereco" => $input['endereco'],
            "telefone" => $input['telefone'],
            "senha" => password_hash($input['senha'], PASSWORD_BCRYPT),
        ];

        $usuarios[] = $novoUsuario;
        salvarUsuarios($usuarios);

        echo json_encode([
            'message' => 'Usuário cadastrado com sucesso',
            'usuario' => $novoUsuario
        ]);



    } elseif ($method === 'GET') {

        $usuarios = carregarUsuarios();
        $usuariosFiltrados = removerDadosSensiveis($usuarios, ["senha"]);
        echo json_encode($usuariosFiltrados);

    } else {
        responderErro('Método não permitido', 405);
    }
?>
