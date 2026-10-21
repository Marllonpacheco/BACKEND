<?php

function salvarFotoAluno(array $arquivo): array
{
    $maxBytes = 2 * 1024 * 1024;

    $permitidos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    // 0. Nenhum dado de arquivo recebido (foto é opcional)
    if (empty($arquivo)) {
        return ['ok' => true, 'arquivo' => null];
    }

    // 1. Verifica a estrutura do upload
    if (!isset($arquivo['error']) || is_array($arquivo['error'])) {
        return ['ok' => false, 'erro' => 'Dados de upload inválidos.'];
    }

    // Nenhuma foto enviada: não é erro
    if ($arquivo['error'] === UPLOAD_ERR_NO_FILE) {
        return ['ok' => true, 'arquivo' => null];
    }

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        $erros = [
            UPLOAD_ERR_INI_SIZE   => 'A imagem excede o limite permitido pelo PHP.',
            UPLOAD_ERR_FORM_SIZE  => 'A imagem excede o limite do formulário.',
            UPLOAD_ERR_PARTIAL    => 'A imagem foi enviada parcialmente.',
            UPLOAD_ERR_NO_TMP_DIR => 'A pasta temporária do servidor não existe.',
            UPLOAD_ERR_CANT_WRITE => 'O servidor não conseguiu gravar o arquivo.',
            UPLOAD_ERR_EXTENSION  => 'Uma extensão do PHP bloqueou o upload.',
        ];

        return [
            'ok'   => false,
            'erro' => $erros[$arquivo['error']] ?? 'Falha no envio da imagem.',
        ];
    }

    // 2. Verifica o arquivo temporário
    if (empty($arquivo['tmp_name']) || !is_uploaded_file($arquivo['tmp_name'])) {
        return ['ok' => false, 'erro' => 'Arquivo temporário inválido.'];
    }

    // 3. Valida o tamanho
    if ($arquivo['size'] > $maxBytes) {
        return ['ok' => false, 'erro' => 'A imagem deve ter no máximo 2 MB.'];
    }

    // 4. Identifica o tipo real do arquivo (não confia na extensão enviada)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($arquivo['tmp_name']);

    if (!isset($permitidos[$mime])) {
        return ['ok' => false, 'erro' => 'Envie uma imagem JPG, PNG ou WebP.'];
    }

    // 4.1 Confirma que o conteúdo é realmente uma imagem
    if (@getimagesize($arquivo['tmp_name']) === false) {
        return ['ok' => false, 'erro' => 'O arquivo enviado não é uma imagem válida.'];
    }

    // 5. Define a pasta de destino
    $pasta = __DIR__ . '/uploads/alunos/';

    if (!is_dir($pasta)) {
        if (!mkdir($pasta, 0755, true) && !is_dir($pasta)) {
            return ['ok' => false, 'erro' => 'Não foi possível criar a pasta de fotos.'];
        }
    }

    // 6. Verifica a permissão de escrita
    if (!is_writable($pasta)) {
        return ['ok' => false, 'erro' => 'A pasta de fotos não permite escrita.'];
    }

    // 6.1 Impede execução de scripts dentro da pasta de uploads (Apache)
    $htaccess = $pasta . '.htaccess';
    if (!file_exists($htaccess)) {
        @file_put_contents(
            $htaccess,
            "php_flag engine off\nRemoveHandler .php .phtml .php5\nOptions -ExecCGI\n"
        );
    }

    // 7. Gera nome único
    $extensao  = $permitidos[$mime];
    $nomeFinal = bin2hex(random_bytes(16)) . '.' . $extensao;
    $destino   = $pasta . $nomeFinal;

    // 8. Salva a imagem
    if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
        $ultimoErro = error_get_last();
        error_log('Falha no upload: ' . ($ultimoErro['message'] ?? 'erro desconhecido'));

        return ['ok' => false, 'erro' => 'Falha ao salvar a imagem no servidor.'];
    }

    return ['ok' => true, 'arquivo' => $nomeFinal];
}