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
    protected $signature = 'scrape:tecnideia {--force : Forçar reprocessamento de URLs já existentes}';
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

        // Pegar o primeiro usuário ou configurar SCRAPE_USER_ID no .env
        $userId = env('SCRAPE_USER_ID', User::first()?->id);
        if (!$userId) {
            $this->error('Nenhum utilizador encontrado para associar FAQs. Configure SCRAPE_USER_ID no .env ou crie um utilizador.');
            return;
        }

        $client = new HttpBrowser(HttpClient::create(['timeout' => 10]));

        // Recuperando URLs já processadas para evitar redundância
        $processedUrls = Faq::whereIn('source_url', $urls)->pluck('source_url')->toArray();

        foreach ($urls as $url) {
            // Pular URLs já processadas, a menos que --force seja usado
            if (!$this->option('force') && in_array($url, $processedUrls)) {
                $this->info("URL $url já processada. Pulando...");
                continue;
            }

            try {
                $crawler = $client->request('GET', $url);
                $this->info("Acessando $url");
                $this->extractFaqs($crawler, $userId, $url);
            } catch (\Exception $e) {
                Log::error("Erro ao fazer scraping de $url: " . $e->getMessage());
                $this->error("Erro ao processar $url: " . $e->getMessage());
            }
        }

        $this->info('Scraping concluído.');
    }

    private function extractFaqs(Crawler $crawler, $userId, $url)
{
    $crawler->filter('.faq-item, .accordion-item, section.faq h2, section.faq h3')->each(function (Crawler $node) use ($userId, $url) {
        $question = null;
        $answer = '';

        // Tentar identificar a pergunta de forma mais flexível
        try {
            $questionNode = $node->filter('.question, h2, h3')->first();
            if ($questionNode->count()) {
                $question = trim($questionNode->text());
            }

            // Caso a pergunta não tenha sido encontrada, tentamos outras abordagens
            if (!$question) {
                $questionNode = $node->filter('strong, b, p')->first(); // Tentar encontrar a pergunta em outras tags
                if ($questionNode->count()) {
                    $question = trim($questionNode->text());
                }
            }
        } catch (\Exception $e) {
            Log::warning("Erro ao tentar extrair pergunta: " . $e->getMessage());
        }

        // Identificar a resposta
        try {
            $answerNode = $node->filter('.answer, p, div, ul')->first();
            if ($answerNode->count()) {
                $answer = trim($answerNode->text());
            }
        } catch (\Exception $e) {
            Log::warning("Erro ao tentar extrair resposta: " . $e->getMessage());
        }

        // Validar pergunta e resposta
        if ($question && $answer && strlen($question) > 5 && strlen($answer) > 10) {
            $embedding = $this->generateEmbedding($question);
            if (!empty($embedding)) {
                Faq::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'question' => $question,
                    ],
                    [
                        'answer' => $answer,
                        'source_url' => $url,
                        'embedding' => json_encode($embedding),
                    ]
                );
                $this->info("FAQ salva: $question (de $url)");
            } else {
                Log::warning("Embedding não gerado para pergunta: $question, URL: $url");
            }
        } else {
            Log::info("FAQ ignorada (inválida ou curta): Pergunta: $question, Resposta: $answer, URL: $url");
        }
    });
}


    private function generateEmbedding($text)
    {
        try {
            $response = $this->makeApiRequestWithRetry(function () use ($text) {
                return Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type' => 'application/json',
                ])->timeout(10)->post('https://api.openai.com/v1/embeddings', [
                    'model' => 'text-embedding-ada-002',
                    'input' => $text,
                ]);
            });

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

    private function makeApiRequestWithRetry($requestCallback, $maxRetries = 2)
    {
        $attempt = 0;
        while ($attempt < $maxRetries) {
            try {
                $response = $requestCallback();
                if ($response->successful()) {
                    return $response;
                }
                Log::warning('Tentativa ' . ($attempt + 1) . ' falhou: ' . $response->body());
            } catch (\Exception $e) {
                Log::warning('Tentativa ' . ($attempt + 1) . ' falhou com exceção: ' . $e->getMessage());
            }
            $attempt++;
            usleep(500000); // Espera 500 ms antes de tentar novamente
        }
        return $requestCallback(); // Última tentativa
    }
}
