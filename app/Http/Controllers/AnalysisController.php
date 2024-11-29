<?php

namespace App\Http\Controllers;

use App\Models\Api\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    public function ordersAnalysis(Request $request)
    {
        return response()->json([
            'servicesQuantitys' => $this->getTypeServices(),
            'invoicings' => $this->getInvoicingsLastMonths(),
            'statusComparison' => $this->getStatusComparisonLastMonths(),
            'frequentVehicleColor' => $this->getMostFrequentVehicleColors(),
            'dayMostInvoicing' => $this->getDayWithMostInvoicings(),
        ]);
    }

    private function getTypeServices()
    {
        $typeServices = Order::select('type_service')
            ->whereBetween('created_at', [
                Carbon::now()->subMonths(3)->startOfMonth(), // Início do mês 3 meses atrás
                Carbon::now()->endOfMonth(),                // Fim do mês atual
            ])
            ->get();
    
        // Decodifica o JSON de cada registro e transforma em array
        $typeServices = $typeServices->map(function($item) {
            return json_decode($item->type_service, true); // Retorna como array
        })->toArray();
    
        // Achata o array e conta as ocorrências
        $flatArray = array_merge(...$typeServices);
    
        // Formata os labels com as primeiras letras maiúsculas
        $formattedValues = collect($flatArray)
            ->unique()
            ->map(function ($service) {
                return ucfirst($service);
            })
            ->toArray();
    
        // Prepara o array de resposta
        return [
            'values' => array_values(array_count_values($flatArray)),
            'labels' => $formattedValues,
            'colors' => [$this->generateRandomColor(), $this->generateRandomColor()],
            'type' => 'lines',
        ];
    }

    private function generateRandomColor() 
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    public function getInvoicingsLastMonths()
    {
        // Consultar as ordens no banco de dados nos últimos 3 meses
        $typeServices = Order::select('total_price', 'created_at')
            ->whereBetween('created_at', [
                Carbon::now()->subMonths(3)->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->get();

        // Mapeamento dos meses em números (1-12) para nomes em português
        $monthNames = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        ];

        // Inicializar os arrays para os dados de resposta
        $labels = [];
        $values = [];

        // Agrupar e somar os valores por mês
        foreach ($typeServices as $order) {
            $month = Carbon::parse($order->created_at)->month;
            $monthName = $monthNames[$month]; // Obter o nome do mês em português

            // Se o mês não estiver nos labels, adicionar
            if (!in_array($monthName, $labels)) {
                $labels[] = $monthName;
            }

            // Soma do preço total por mês
            if (!isset($values[$monthName])) {
                $values[$monthName] = 0;
            }

            $values[$monthName] += $order->total_price;
        }

        // Ordenar os labels para que apareçam de Janeiro a Dezembro
        $sortedLabels = [];
        $sortedValues = [];
        foreach ($monthNames as $monthNum => $monthName) {
            if (in_array($monthName, $labels)) {
                $sortedLabels[] = $monthName;
                $sortedValues[] = $values[$monthName] ?? 0;
            }
        }

        return [
            'labels' => $sortedLabels,
            'values' => $sortedValues,
            'colors' => [$this->generateRandomColor(), $this->generateRandomColor()],
            'type' => 'lines',
        ];
    }

    public function getStatusComparisonLastMonths()
    {
        // Filtrando os pedidos nos últimos 3 meses
        $orders = Order::select('status', 'created_at')
            ->whereBetween('created_at', [
                Carbon::now()->subMonths(3)->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->whereIn('status', ['only-budget', 'input', 'output']) // Filtro pelos status de interesse
            ->get();
    
        // Inicializando os contadores de cada status
        $statusCount = [
            'only-budget' => 0,
            'entrada_saida' => 0, // Contagem combinada de entrada e saída
        ];
    
        // Contando os status para os pedidos
        foreach ($orders as $order) {
            if ($order->status === 'only-budget') {
                $statusCount['only-budget']++;
            } elseif ($order->status === 'input' || $order->status === 'output') {
                $statusCount['entrada_saida']++;
            }
        }
    
        // Labels para os status
        $labels = [
            'only-budget' => 'Somente Orçamento',
            'entrada_saida' => 'Entrada e Saída',
        ];
    
        // Cores aleatórias para cada status
        $colors = [
            $this->generateRandomColor(),
            $this->generateRandomColor(),
        ];
    
        // Retornando o formato esperado
        return [
            'labels' => array_values($labels),
            'values' => array_values($statusCount),
            'colors' => $colors,
            'type' => 'lines',
        ];
    }
    
    public function getMostFrequentVehicleColors()
    {
        // Consultando as cores dos veículos nos últimos 3 meses
        $vehicleColors = Order::select('vehicle_color')
            ->whereBetween('created_at', [
                Carbon::now()->subMonths(3)->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->whereNotNull('vehicle_color') // Certificando que a cor do veículo não está nula
            ->get();

        // Contando a quantidade de cada cor
        $colorCount = $vehicleColors->countBy('vehicle_color');

        // Ordenando as cores pelo número de ocorrências em ordem decrescente
        $colorCount = $colorCount->sortDesc();

        // Pegando as cores mais frequentes e os seus contadores
        $colors = $colorCount->keys()->toArray();
        $values = $colorCount->values()->toArray();

        // Gerando cores aleatórias para as barras do gráfico
        $randomColors = [];
        foreach ($colors as $color) {
            $randomColors[] = $this->generateRandomColor();  // Gerando uma cor aleatória para cada cor de veículo
        }

        // Retornando os resultados no formato esperado
        return [
            'labels' => $colors,  // Cores mais frequentes
            'values' => $values,  // Quantidade de vezes que cada cor apareceu
            'colors' => $randomColors, // Cores para o gráfico
            'type' => 'doughnut',
        ];
    }

    public function getDayWithMostInvoicings()
    {
        // Inicializando um array para armazenar os resultados
        $result = [
            'labels' => [],  // Dias mais frequentes (rótulos)
            'values' => [],  // Quantidade de orçamentos nesses dias
            'colors' => [],   // Cores aleatórias
            'type' => 'lines',
        ];
    
        // Iterando sobre os últimos 3 meses
        for ($i = 0; $i < 3; $i++) {
            $startDate = Carbon::now()->subMonths($i)->startOfMonth(); // Primeiro dia do mês
            $endDate = Carbon::now()->subMonths($i)->endOfMonth();   // Último dia do mês
    
            // Consultando os pedidos por dia dentro do mês
            $ordersPerDay = Order::selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupByRaw('DATE(created_at)')
                ->orderByDesc('total')  // Ordenando para pegar o maior número de orçamentos
                ->limit(1)  // Pegando apenas o dia com o maior número de orçamentos
                ->get();
    
            // Se houver pelo menos um resultado
            if ($ordersPerDay->isNotEmpty()) {
                $result['labels'][] = Carbon::parse($ordersPerDay->first()->date)->format('d/m/Y'); // Dia mais frequente
                $result['values'][] = $ordersPerDay->first()->total; // Quantidade de orçamentos nesse dia
                $result['colors'][] = $this->generateRandomColor(); // Cor aleatória para o gráfico
            }
        }

        return $result;
    }
    
}
