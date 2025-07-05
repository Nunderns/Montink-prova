# Configuração de E-mail

Para configurar o envio de e-mails de confirmação de pedidos, siga estas etapas:

1. Configure as variáveis de ambiente no arquivo `.env` com as credenciais do seu servidor de e-mail:

```ini
MAIL_MAILER=smtp
MAIL_HOST=seu_servidor_smtp
MAIL_PORT=587
MAIL_USERNAME=seu_email@dominio.com
MAIL_PASSWORD=sua_senha
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="seu_email@dominio.com"
MAIL_FROM_NAME="${APP_NAME}"
```

2. Teste o envio de e-mail com o comando:

```bash
php artisan tinker
```

No console do Tinker, execute:

```php
Mail::raw('Teste de e-mail', function($message) {
    $message->to('seu_email@dominio.com')
            ->subject('Teste de Envio');
});
```

3. Se estiver em ambiente de desenvolvimento, você pode usar o Mailpit para visualizar os e-mails enviados localmente. Instale com:

```bash
composer require --dev beyondcode/laravel-mailbox
```

4. Configure o `.env` para usar o Mailpit:

```ini
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

5. Inicie o servidor Mailpit:

```bash
./vendor/bin/sail up -d
```

Acesse a interface do Mailpit em: http://localhost:8025

## Solução de Problemas

- **E-mails não estão sendo enviados**: Verifique o arquivo de log em `storage/logs/laravel.log` para erros.
- **Verifique as credenciais**: Certifique-se de que as credenciais SMTP estão corretas.
- **Verifique a porta**: A porta padrão para TLS é 587 e para SSL é 465.
- **Verifique o firewall**: Certifique-se de que o firewall não está bloqueando a conexão SMTP.

## Personalização

Você pode personalizar o template do e-mail editando o arquivo:
`resources/views/emails/order-confirmation.blade.php`
