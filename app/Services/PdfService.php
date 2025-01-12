<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    /**
     * Gera um PDF a partir de um template e salva ou retorna o conteúdo.
     *
     * @param array $data Dados a serem passados para o template
     * @param string $template Nome do arquivo Blade (sem .blade.php)
     * @param string|null $savePath Caminho para salvar o arquivo (opcional)
     * @return string Caminho salvo ou conteúdo do PDF
     */
    public function generatePdf($data, string $template, string $savePath = null)
    {
        // Renderiza o template com os dados
        $pdf = Pdf::loadView($template, $data);

        if ($savePath) {
            // Salva o PDF no caminho especificado
            Storage::disk('public')->put($savePath, $pdf->output());
            return Storage::url($savePath); // Retorna a URL para o arquivo
        }

        // Retorna o conteúdo do PDF
        return $pdf->stream();
    }
}
