<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class ScrapeTecnideiaSite extends Command
{
    protected $signature = 'scrape:tecnideia';
    protected $description = 'Extrai FAQs do site https://tecnideia.ao e armazena no banco de dados';

    public function handle()
    {
        $this->info('Iniciando scraping do site https://tecnideia.ao...');

        $urls = [
            'https://tecnideia.ao',
            'https://tecnideia.ao/services',
            'https://tecnideia.ao/prices',
            'https://tecnideia.ao/contacts',
            'https://tecnideia.ao/partnerships/about',
        ];

        $user = User::first(); // Ajuste para o usuário correto
        if (!$user) {
            $this->error('Nenhum usuário encontrado para associar FAQs.');
            return;
        }

        $client = new HttpBrowser(HttpClient::create());

        foreach ($urls as $url) {
            try {
                $crawler = $client->request('GET', $url);
                $this->info("Acessando $url");

                // Extrair FAQs
                $this->extractFaqs($crawler, $user->id, $url);
            } catch (\Exception $e) {
                Log::error("Erro ao fazer scraping de $url: " . $e->getMessage());
                $this->error("Erro ao processar $url: " . $e->getMessage());
            }
        }

        $this->info('Scraping concluído.');
    }

    private function extractFaqs(Crawler $crawler, $userId, $url)
    {
        $crawler->filter('section.faq, .accordion, .faq-item, h2, h3')->each(function (Crawler $node) use ($userId, $url) {
            $question = null;
            $answer = '';

            // Identificar pergunta
            if (in_array($node->nodeName(), ['h2', 'h3']) || $node->filter('.question')->count() > 0) {
                $question = trim($node->text());
            }

            // Identificar resposta
            try {
                $nextNodes = $node->nextAll();
                if ($nextNodes->count()) {
                    $nextNode = $nextNodes->first();
                    if (in_array($nextNode->nodeName(), ['p', 'div']) && $nextNode->filter('script, style')->count() === 0) {
                        $answer = trim($nextNode->text());
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Nenhuma resposta válida encontrada para pergunta em $url: " . $e->getMessage());
            }

            // Validar pergunta e resposta
            if ($question && $answer && strlen($question) > 10 && strlen($answer) > 20) {
                Faq::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'question' => $question,
                    ],
                    [
                        'answer' => $answer,
                        'source_url' => $url,
                        'embedding' => json_encode($this->generateEmbedding($question)),
                    ]
                );
                $this->info("FAQ salva: $question (de $url)");
            } else {
                Log::info("FAQ ignorada (inválida ou curta): Pergunta: $question, Resposta: $answer, URL: $url");
            }
        });
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
            return [];
        } catch (\Exception $e) {
            Log::error('Erro ao chamar API de embeddings: ' . $e->getMessage());
            return [];
        }
    }
}