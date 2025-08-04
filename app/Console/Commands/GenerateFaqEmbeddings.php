<?php

namespace App\Console\Commands;

use App\Models\Faq;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateFaqEmbeddings extends Command
{
    protected $signature = 'faqs:generate-embeddings';
    protected $description = 'Gera embeddings para todas as FAQs existentes';

    public function handle()
    {
        $faqs = Faq::whereNull('embedding')->get();

        if ($faqs->isEmpty()) {
            $this->info('Nenhuma FAQ sem embedding encontrada.');
            return;
        }

        $this->info('Gerando embeddings para ' . $faqs->count() . ' FAQs...');

        foreach ($faqs as $faq) {
            $embedding = $this->generateEmbedding($faq->question);
            if ($embedding) {
                $faq->update(['embedding' => json_encode($embedding)]);
                $this->info("Embedding gerado para FAQ: {$faq->question}");
            } else {
                $this->error("Falha ao gerar embedding para FAQ: {$faq->question}");
            }
        }

        $this->info('Processo concluído.');
    }

    private function generateEmbedding($text)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/embeddings', [
                'model' => 'text-embedding-ada-002',
                'input' => $text,
            ]);

            if ($response->successful()) {
                return $response->json()['data'][0]['embedding'];
            }
            Log::error('Erro ao gerar embedding: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Erro ao chamar API de embeddings: ' . $e->getMessage());
            return null;
        }
    }
}