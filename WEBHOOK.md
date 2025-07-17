# Webhook de Atualização de Pedidos

Este documento descreve como usar o webhook para atualizar o status de pedidos no sistema.

## Configuração

1. Adicione as seguintes variáveis de ambiente ao seu arquivo `.env`:

```ini
# Configurações do Webhook
WEBHOOK_API_KEY=sua_chave_secreta_aqui
# Lista de IPs permitidos (opcional, separados por vírgula)
# WEBHOOK_ALLOWED_IPS=192.168.1.1,192.168.1.2
# Habilitar restrição por IP (true/false)
WEBHOOK_ENABLE_IP_RESTRICTION=false
```

## Endpoint

```
POST /api/webhook/order-update
```

## Autenticação

Inclua a chave da API no cabeçalho ou no corpo da requisição:

- **Cabeçalho**: `X-API-Key: sua_chave_aqui`
- **Corpo**: `{ "api_key": "sua_chave_aqui", ... }`

## Parâmetros da Requisição

| Parâmetro | Tipo   | Obrigatório | Descrição                          |
|-----------|--------|-------------|-----------------------------------|
| id        | int    | Sim         | ID do pedido a ser atualizado     |
| status    | string | Sim         | Novo status do pedido             |
| api_key   | string | Não*        | Chave de API (se não enviar no header) |

*Obrigatório se não for enviado no cabeçalho.

## Exemplos de Uso

### Atualizar status de um pedido

```bash
curl -X POST http://seu-dominio.com/api/webhook/order-update \
  -H "Content-Type: application/json" \
  -H "X-API-Key: sua_chave_aqui" \
  -d '{
    "id": 123,
    "status": "em_processamento"
  }'
```

### Cancelar um pedido

```bash
curl -X POST http://seu-dominio.com/api/webhook/order-update \
  -H "Content-Type: application/json" \
  -d '{
    "id": 123,
    "status": "cancelado",
    "api_key": "sua_chave_aqui"
  }'
```

## Respostas

### Sucesso (200 OK)

```json
{
  "success": true,
  "message": "Status do pedido atualizado com sucesso",
  "pedido_id": 123,
  "novo_status": "em_processamento"
}
```

### Erro de Validação (422 Unprocessable Entity)

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "id": ["O campo id é obrigatório."],
    "status": ["O campo status é obrigatório."]
  }
}
```

### Não Autorizado (401 Unauthorized)

```json
{
  "success": false,
  "message": "Unauthorized"
}
```

### Erro Interno do Servidor (500 Internal Server Error)

```json
{
  "success": false,
  "message": "Erro ao processar a requisição",
  "error": "Mensagem de erro detalhada"
}
```

## Segurança

1. **Chave de API**: Sempre use uma chave de API forte e mantenha-a em segredo.
2. **Restrição de IP**: Para maior segurança, habilite a restrição por IP e especifique os IPs permitidos.
3. **HTTPS**: Certifique-se de que seu servidor use HTTPS para todas as comunicações com o webhook.

## Logs

Todas as tentativas de acesso ao webhook são registradas nos logs do Laravel (`storage/logs/laravel.log`), incluindo:
- Endereços IP dos clientes
- Cargas úteis das requisições
- Erros de validação
- Tentativas de acesso não autorizado

## Testando Localmente

Você pode testar o webhook localmente usando o comando `curl` ou ferramentas como o Postman. Certifique-se de que seu ambiente local esteja configurado corretamente com as variáveis de ambiente necessárias.
