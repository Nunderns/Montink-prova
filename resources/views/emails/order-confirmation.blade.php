<!DOCTYPE html>
<html>
<head>
    <title>Confirmação de Pedido</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; padding: 20px 0; border-bottom: 1px solid #eee; }
        .footer { text-align: center; padding: 20px 0; border-top: 1px solid #eee; font-size: 12px; color: #777; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f5f5f5; }
        .text-center { text-align: center; }
        .mt-4 { margin-top: 1.5rem; }
        .mb-4 { margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>

        <div class="content">
            <h2>Obrigado pelo seu pedido!</h2>
            <p>Seu pedido <strong>#{{ $pedido->codigo }}</strong> foi recebido e está sendo processado.</p>

            <h3>Resumo do Pedido</h3>
            <ul>
                <li><strong>Número do Pedido:</strong> {{ $pedido->codigo }}</li>
                <li><strong>Data do Pedido:</strong> {{ $pedido->created_at->format('d/m/Y H:i') }}</li>
                <li><strong>Status do Pedido:</strong> {{ ucfirst($pedido->status) }}</li>
                <li><strong>Forma de Pagamento:</strong> {{ ucfirst(str_replace('_', ' ', $pedido->forma_pagamento)) }}</li>
            </ul>

            <h3>Itens do Pedido</h3>
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedido->itens as $item)
                    <tr>
                        <td>{{ $item->produto->nome }}</td>
                        <td>{{ $item->quantidade }}</td>
                        <td>R$ {{ number_format((float)$item->preco, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format((float)$item->preco * (int)$item->quantidade, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                        <td><strong>R$ {{ number_format((float)$pedido->valor_total, 2, ',', '.') }}</strong></td>
                    </tr>
                    @if(isset($pedido->desconto) && $pedido->desconto > 0)
                    <tr>
                        <td colspan="3" class="text-right"><strong>Desconto:</strong></td>
                        <td><strong>- R$ {{ number_format((float)$pedido->desconto, 2, ',', '.') }}</strong></td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="text-right"><strong>Frete:</strong></td>
                        <td><strong>R$ {{ number_format((float)$pedido->frete, 2, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                        <td><strong>R$ {{ number_format((float)$pedido->valor_final, 2, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>

            @if($endereco)
            <div class="mt-4">
                <h3>Endereço de Entrega</h3>
                <p>
                    {{ $endereco->nome ?? 'Não informado' }}<br>
                    {{ $endereco->logradouro ?? '' }}, {{ $endereco->numero ?? 'S/N' }}<br>
                    @if(!empty($endereco->complemento))
                        {{ $endereco->complemento }}<br>
                    @endif
                    {{ $endereco->bairro ?? '' }}<br>
                    @if($endereco->cidade && $endereco->estado)
                        {{ $endereco->cidade }}/{{ $endereco->estado }}<br>
                    @endif
                    @if($endereco->cep)
                        CEP: {{ $endereco->cep }}
                    @endif
                </p>
            </div>
            @else
            <div class="mt-4">
                <h3>Endereço de Entrega</h3>
                <p>Endereço de entrega não disponível.</p>
            </div>
            @endif
        </div>

        <div class="footer">
            <p> {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
            <p>Se você tiver alguma dúvida, entre em contato conosco pelo e-mail {{ config('mail.from.address') }}.</p>
        </div>
    </div>
</body>
</html>
