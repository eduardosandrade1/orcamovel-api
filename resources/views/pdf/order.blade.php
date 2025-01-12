<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orçamento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        .container {
            width: 90%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th, .table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f4f4f4;
        }
        .images {
            margin-top: 10px;
        }
        .images img {
            max-width: 100px;
            margin-right: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Orçamento #{{ $id }}</h1>
            <p>Data: {{ \Carbon\Carbon::parse($created_at)->format('d/m/Y') }}</p>
        </div>

        <div class="section">
            <div class="section-title">Informações do Cliente</div>
            <p><strong>Nome:</strong> {{ $client['name'] }}</p>
            <p><strong>Veículo:</strong> {{ $vehicle_infos['brand'] }} {{ $vehicle_infos['model'] }} ({{ $vehicle_infos['model_year'] }})</p>
            <p><strong>Placa:</strong> {{ $plate }}</p>
        </div>

        <div class="section">
            <div class="section-title">Serviços Solicitados</div>
            <ul>
                @foreach($type_service as $service)
                    <li>{{ $service['name'] }}</li>
                @endforeach
            </ul>
        </div>

        <div class="section">
            <div class="section-title">Peças, Avarias e Imagens</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Peça</th>
                        <th>Avarias</th>
                        <th>Imagens</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($type_parts as $part)
                        <tr>
                            <td>{{ $part['label'] }}</td>
                            <td>
                                @foreach($part['avarias'] as $avaria)
                                    {{ $avaria['name'] }}@if (!$loop->last), @endif
                                @endforeach
                            </td>
                            <td>
                                <div class="images">
                                    @foreach($part['images'] as $image)
                                        {{ $image['path'] }}
                                        <img src="{{ asset('storage/' . $image['path']) }}" alt="Imagem da peça">
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Resumo do Orçamento</div>
            <p><strong>Valor Total:</strong> R$ {{ number_format($total_price, 2, ',', '.') }}</p>
            <p><strong>Valor das Peças:</strong> R$ {{ number_format($price_parts, 2, ',', '.') }}</p>
        </div>
    </div>
</body>
</html>
