<?php     $datafile = 'usuarios.json';

    // Garante que o arquivo de usuários exista
    if (!file_exists($datafile)) {
        file_put_contents($datafile, json_encode([]));
    }

    // Função para carregar usuários do arquivo
    function carregarUsuarios(): array {
        global $datafile;
        $json = file_get_contents($datafile);
        return json_decode($json, true) ?? [];
    }

    // Função para salvar usuários no arquivo
    function salvarUsuarios(array $usuarios): void{
        global $datafile;
        file_put_contents($datafile, json_encode($usuarios, JSON_PRETTY_PRINT));
    }

    // Verifica se o e-mail já está cadastrado
    function emailJaCadastrado(array $usuarios, string $email): bool {
        foreach ($usuarios as $usuario) {
            if (strtolower($usuario['email']) === strtolower($email)) {
                return true;
            }
        }
        return false;
    }

    // Função para retorna o erro e encerrar
    function responderErro(string $mensagem, int $codigo = 400): void {
        http_response_code($codigo);
        echo json_encode(['error' => $mensagem]);
        exit;
    }

    ?>