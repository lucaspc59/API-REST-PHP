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

    // Função para gerar UUID para cada usuário cadastrado
    function gerarUUID() : string {
    $data= random_bytes(16);

    // Ajusta os bits para a versão 4
    $data[6] = chr((ord($data[6]) & 0x0F) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3F) | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
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
        echo json_encode(['error' => $mensagem], JSON_UNESCAPED_UNICODE);
        exit;
    }

    ?>