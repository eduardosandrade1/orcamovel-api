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
        $formattedData = $this->formatChartData([
            'servicesQuantitys' => $this->getTypeServices(),
            'invoicings' => $this->getInvoicingsLastMonths(),
            'statusComparison' => $this->getStatusComparisonLastMonths(),
            'frequentVehicleColor' => $this->getMostFrequentVehicleColors(),
            'dayMostInvoicing' => $this->getDayWithMostInvoicings(),
            'revenueByValueRange' => $this->getRevenueByValueRange(),
        ]);
    
        return response()->json($formattedData);
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

    public function getRevenueByValueRange()
    {
        // Define the value ranges
        $ranges = [
            'Menos de 500' => [0, 500],
            '500 a 1000' => [500, 1000],
            '1000 a 2000' => [1000, 2000],
            'Acima de 2000' => [2000, PHP_INT_MAX]
        ];
    
        $results = [
            'labels' => [],
            'values' => [],
            'colors' => [],
            'type' => 'lines',
        ];
    
        // Iterate over each value range
        foreach ($ranges as $range => $limits) {
            // Using Eloquent to sum the total_price within the range
            $total = DB::table('orders')
                ->whereBetween('total_price', [$limits[0], $limits[1]])
                ->whereBetween('created_at', [Carbon::now()->subMonths(3)->startOfMonth(), Carbon::now()->endOfMonth()])
                ->sum('total_price');
    
            // Storing the results
            $results['labels'][] = $range;
            $results['values'][] = $total ?: 0;  // Use 0 if no results
            $results['colors'][] = $this->generateRandomColor();  // Random color for the chart
        }
    
        return $results;
    }
    

    public function formatChartData($apiData)
    {
        // Services Chart
        $servicesDatasets = array_map(function ($label, $index) use ($apiData) {
            return [
                'id' => $label,
                'label' => $label,
                'data' => [$apiData['servicesQuantitys']['values'][$index]],
                'borderColor' => '#ccc',
                'backgroundColor' => $apiData['servicesQuantitys']['colors'][$index],
                'borderWidth' => 0,
            ];
        }, $apiData['servicesQuantitys']['labels'], array_keys($apiData['servicesQuantitys']['labels']));

        $servicesChart = [
            'title' => 'Tipos de Serviços',
            'subtitle' => 'Tipos de serviços feitos nos últimos 3 meses',
            'labels' => $this->getLabelsFromTypeGraphs($apiData['servicesQuantitys']),
            'datasets' => $servicesDatasets,
            'type' => $apiData['servicesQuantitys']['type'],
        ];

        // Invoicing Chart
        $invoicingsDatasets = array_map(function ($label, $index) use ($apiData) {
            return [
                'id' => $label,
                'label' => $label,
                'data' => [$apiData['invoicings']['values'][$index]],
                'borderColor' => '#1a1a1a',
                'backgroundColor' => $apiData['invoicings']['colors'][$index],
                'borderWidth' => 0,
            ];
        }, $apiData['invoicings']['labels'], array_keys($apiData['invoicings']['labels']));

        $invoicingChart = [
            'title' => 'Últimos Faturamentos',
            'subtitle' => 'Faturamento dos últimos 3 meses',
            'labels' => $this->getLabelsFromTypeGraphs($apiData['invoicings']),
            'datasets' => $invoicingsDatasets,
            'type' => $apiData['invoicings']['type'],
        ];

        // Status Comparison Chart
        $statusComparisonDatasets = array_map(function ($label, $index) use ($apiData) {
            return [
                'id' => $label,
                'label' => $label,
                'data' => [$apiData['statusComparison']['values'][$index]],
                'borderColor' => '#1a1a1a',
                'backgroundColor' => $apiData['statusComparison']['colors'][$index],
                'borderWidth' => 0,
            ];
        }, $apiData['statusComparison']['labels'], array_keys($apiData['statusComparison']['labels']));

        $statusComparisonChart = [
            'title' => 'Fluxo de Veículos',
            'subtitle' => 'Fluxo de veículos dos últimos 3 meses',
            'labels' => $this->getLabelsFromTypeGraphs($apiData['statusComparison']),
            'datasets' => $statusComparisonDatasets,
            'type' => $apiData['statusComparison']['type'],
        ];

        // Frequent Vehicle Color Chart
        $frequentVehicleColorDatasets = array_map(function ($label, $index) use ($apiData) {
            return [
                'id' => $label,
                'label' => $label,
                'data' => $apiData['frequentVehicleColor']['values'],
                'borderColor' => '#1a1a1a',
                'backgroundColor' => $apiData['frequentVehicleColor']['colors'],
                'borderWidth' => 0,
            ];
        }, $apiData['frequentVehicleColor']['labels'], array_keys($apiData['frequentVehicleColor']['labels']));

        $frequentVehicleColorChart = [
            'title' => 'Cores Mais Usadas',
            'subtitle' => 'Cores mais usadas nos veículos dos últimos 3 meses',
            'labels' => $this->getLabelsFromTypeGraphs($apiData['frequentVehicleColor']),
            'datasets' => $frequentVehicleColorDatasets,
            'type' => $apiData['frequentVehicleColor']['type'],
        ];

        // Day Most Invoicing Chart
        $dayMostInvoicingDatasets = array_map(function ($label, $index) use ($apiData) {
            return [
                'id' => $label,
                'label' => $label,
                'data' => [$apiData['dayMostInvoicing']['values'][$index]],
                'borderColor' => '#1a1a1a',
                'backgroundColor' => $apiData['dayMostInvoicing']['colors'][$index],
                'borderWidth' => 0,
            ];
        }, $apiData['dayMostInvoicing']['labels'], array_keys($apiData['dayMostInvoicing']['labels']));

        $dayMostInvoicingChart = [
            'title' => 'Dias com Mais Orçamentos',
            'subtitle' => 'Dias dos últimos 3 meses com mais orçamentos',
            'labels' => $this->getLabelsFromTypeGraphs($apiData['dayMostInvoicing']),
            'datasets' => $dayMostInvoicingDatasets,
            'type' => $apiData['dayMostInvoicing']['type'],
        ];

        // Return all formatted charts
        return [
            $servicesChart,
            $invoicingChart,
            $statusComparisonChart,
            $frequentVehicleColorChart,
            $dayMostInvoicingChart,
        ];
    }

    private function getLabelsFromTypeGraphs($apiDataObject)
    {
        $graphs = ['doughnut', 'pie'];
        return in_array($apiDataObject['type'], $graphs) ? $apiDataObject['labels'] : [''];
    }

}
