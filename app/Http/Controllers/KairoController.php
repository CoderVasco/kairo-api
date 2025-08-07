<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Faq;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class KairoController extends Controller
{
    public function handleMessage(Request $request)
    {
        // Validação da requisição
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'api_token' => 'required|string',
            'user_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $user = User::where('api_token', $request->api_token)->first();
        if (!$user) {
            return response()->json(['response' => 'Token inválido'], 401);
        }

        $message = trim($request->message);
        $userName = $request->user_name ? trim($request->user_name) : null;
        $isNewSession = empty($request->history);

        // Registrar a solicitação
        $logId = $this->logRequest($user->id, $message, $userName ? $userName : 'Usuário');

        try {
            // Verificar se é uma nova sessão e sem nome
            if ($isNewSession && !$userName) {
                $response = "Olá! Eu sou o Kairo IA. Como te chamas?";
                $this->logResponse($logId, $response, 'assistant');
                return response()->json(['response' => $response]);
            }

            // Verificar cache para respostas frequentes
            $cacheKey = md5("faq_{$user->id}_{$message}");
            $cachedResponse = Cache::get($cacheKey);
            if ($cachedResponse) {
                $formattedResponse = $this->formatResponseWithLinks($cachedResponse);
                $this->logResponse($logId, $formattedResponse, 'cache');
                return response()->json(['response' => $formattedResponse]);
            }

            // Tentar obter resposta do assistente da OpenAI
            $response = $this->callAssistant($message, $request->history ? $request->history : [], $user, $userName ? $userName : 'Usuário');

            // Verificar se a resposta é válida
            if ($this->isValidResponse($response)) {
                $formattedResponse = $this->formatResponseWithLinks($response);
                Cache::put($cacheKey, $response, now()->addMinutes(60));
                $this->logResponse($logId, $formattedResponse, 'assistant');
                return response()->json(['response' => $formattedResponse]);
            }

            // Fallback para a base de dados (FAQs)
            $faqResponse = $this->getFaqResponse($message, $user);
            if ($faqResponse) {
                $formattedResponse = $this->formatResponseWithLinks($faqResponse);
                Cache::put($cacheKey, $faqResponse, now()->addMinutes(60));
                $this->logResponse($logId, $formattedResponse, 'faq');
                return response()->json(['response' => $formattedResponse]);
            }

            // Resposta padrão para perguntas fora do escopo
            $defaultResponse = "Essa é uma excelente pergunta, " . ($userName ? $userName : 'Usuário') . "! Para te dar uma resposta mais detalhada, sugiro que fales diretamente com a nossa equipa pelo WhatsApp: +244 974 444 060 ou pelo e-mail: geral@tecnideia.ao. Posso ajudar-te com mais alguma coisa?";
            $this->logResponse($logId, $defaultResponse, 'default');
            return response()->json(['response' => $defaultResponse]);
        } catch (\Exception $e) {
            Log::error('Erro ao processar mensagem: ' . $e->getMessage());
            $this->logResponse($logId, 'Erro interno', 'error');
            return response()->json(['response' => 'Desculpe, algo deu errado. Tente novamente mais tarde.'], 500);
        }
    }

    private function callAssistant($message, $history, $user, $userName)
    {
        try {
            // Criar uma nova thread com retry
            $threadResponse = $this->makeApiRequestWithRetry(function () {
                return Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type' => 'application/json',
                    'OpenAI-Beta' => 'assistants=v2',
                ])->timeout(10)->post('https://api.openai.com/v1/threads', []);
            });

            if (!$threadResponse->successful()) {
                Log::error('Erro ao criar thread: ' . $threadResponse->body());
                return '';
            }

            $threadId = $threadResponse->json()['id'];

            // Adicionar mensagens à thread
            $messages = array_merge(array_slice($history, -10), [
                ['role' => 'user', 'content' => $userName ? "Meu nome é {$userName}. {$message}" : $message],
            ]);

            foreach ($messages as $msg) {
                $messageResponse = $this->makeApiRequestWithRetry(function () use ($threadId, $msg) {
                    return Http::withHeaders([
                        'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                        'Content-Type' => 'application/json',
                        'OpenAI-Beta' => 'assistants=v2',
                    ])->timeout(10)->post("https://api.openai.com/v1/threads/$threadId/messages", [
                        'role' => $msg['role'],
                        'content' => $msg['content'],
                    ]);
                });

                if (!$messageResponse->successful()) {
                    Log::error('Erro ao adicionar mensagem à thread: ' . $messageResponse->body());
                    return '';
                }
            }

            // Instruções do sistema
            $systemInstructions = <<<EOT
Você é o Kairo IA, assistente virtual da Tecnideia, uma empresa angolana de soluções tecnológicas. Siga estas instruções rigorosamente:

- **Linguagem**: Use português de Portugal, com tom profissional, amigável e fluido, como um representante da Tecnideia.
- **Personalização**: Use o nome do utilizador (fornecido na mensagem ou 'Usuário' se desconhecido) de forma natural, no início ou fim da resposta, evitando uso excessivo ou forçado.
- **Fontes Primárias**: Baseie todas as respostas exclusivamente no site da Tecnideia (https://tecnideia.ao, https://tecnideia.ao/services, https://tecnideia.ao/prices, https://tecnideia.ao/contacts, https://tecnideia.ao/partnerships/about, https://tecnideia.ao/policies_and_terms) e no FAQ do vector store (vs_6890dc5a39888191973c68e4f59bb6f3). Apresente as respostas como conhecimento interno, sem mencionar consultas externas ou limitações.
- **Solicitação de Nome**: Se não souber o nome do utilizador, inicie a interação com: "Olá! Eu sou o Kairo IA. Como te chamas?" e use o nome fornecido nas próximas respostas. Não agradeça pelo nome a menos que seja a primeira interação.
- **Respostas Fora do Escopo**: Para perguntas não cobertas pelo site ou FAQ, responda: "Essa é uma excelente pergunta, [nome]! Para te dar uma resposta mais detalhada, sugiro que fales diretamente com a nossa equipa pelo WhatsApp: +244 974 444 060 ou pelo e-mail: geral@tecnideia.ao. Posso ajudar-te com mais alguma coisa?"
- **Políticas e Termos**: Para questões de privacidade ou termos, baseie-se em https://tecnideia.ao/policies_and_terms, reforçando o compromisso com a proteção de dados. Exemplo: "Os teus dados estão seguros connosco, [nome]. Não os partilhamos com terceiros, exceto quando exigido por lei. Vê mais em https://tecnideia.ao/policies_and_terms."
- **Formatação**: Apresente URLs, números de WhatsApp e e-mails em texto simples (ex.: WhatsApp: +244 974 444 060, E-mail: geral@tecnideia.ao, URL: https://tecnideia.ao). Não use formatação HTML.
- **Encerramento**: Encerre respostas com um convite personalizado, como: "Tens mais alguma dúvida, [nome]?" ou "Queres saber mais sobre algum serviço, [nome]?"
- **Preços e Afiliados**: Para preços, responda: "Para conhecer os nossos preços, [nome], visita https://tecnideia.ao/prices ou contacta-nos pelo WhatsApp: +244 974 444 060." Para afiliados: "O nosso programa de afiliados oferece até 30% de comissão, [nome]. Inscreve-te em https://tecnideia.ao/partnerships/about!"
- **Valores da Tecnideia**: Reflita inovação, qualidade e confiança, mantendo um tom acolhedor e profissional.
- **Exemplo de Resposta (Serviços)**: "Claro, [nome]! Na Tecnideia, oferecemos desenvolvimento de websites, lojas virtuais, marketing digital, SEO, entre outros. Podes explorar mais em https://tecnideia.ao/services. Queres saber mais sobre algum serviço?"
- **Exemplo de Resposta (Fora do Escopo)**: "Essa é uma excelente pergunta, [nome]! Para te dar uma resposta mais detalhada, sugiro que fales diretamente com a nossa equipa pelo WhatsApp: +244 974 444 060 ou pelo e-mail: geral@tecnideia.ao. Posso ajudar-te com mais alguma coisa?"
EOT;

            // Executar o run com o assistente
            $runResponse = $this->makeApiRequestWithRetry(function () use ($threadId, $systemInstructions) {
                return Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type' => 'application/json',
                    'OpenAI-Beta' => 'assistants=v2',
                ])->timeout(10)->post("https://api.openai.com/v1/threads/$threadId/runs", [
                    'assistant_id' => env('OPENAI_ASSISTENT_ID'),
                    'instructions' => $systemInstructions,
                    'tools' => [['type' => 'file_search']],
                ]);
            });

            if (!$runResponse->successful()) {
                Log::error('Erro ao executar run: ' . $runResponse->body());
                return '';
            }

            $runId = $runResponse->json()['id'];

            // Polling otimizado para aguardar a conclusão do run
            $maxAttempts = 5;
            $attempt = 0;
            $initialDelay = 500;
            $maxDelay = 1500;
            do {
                usleep($initialDelay * 1000);
                $statusResponse = $this->makeApiRequestWithRetry(function () use ($threadId, $runId) {
                    return Http::withHeaders([
                        'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                        'Content-Type' => 'application/json',
                        'OpenAI-Beta' => 'assistants=v2',
                    ])->timeout(10)->get("https://api.openai.com/v1/threads/$threadId/runs/$runId");
                });

                if (!$statusResponse->successful()) {
                    Log::error('Erro ao verificar status do run: ' . $statusResponse->body());
                    return '';
                }

                $status = $statusResponse->json()['status'];
                $attempt++;
                $initialDelay = min($initialDelay * 1.5, $maxDelay);
            } while ($status !== 'completed' && $status !== 'failed' && $attempt < $maxAttempts);

            if ($status !== 'completed') {
                Log::error('Run não foi concluído: ' . $statusResponse->body());
                return '';
            }

            // Recuperar as mensagens da thread
            $messagesResponse = $this->makeApiRequestWithRetry(function () use ($threadId) {
                return Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type' => 'application/json',
                    'OpenAI-Beta' => 'assistants=v2',
                ])->timeout(10)->get("https://api.openai.com/v1/threads/$threadId/messages");
            });

            if ($messagesResponse->successful()) {
                $messagesData = $messagesResponse->json()['data'];
                foreach ($messagesData as $msg) {
                    if ($msg['role'] === 'assistant') {
                        $content = $msg['content'][0]['text']['value'] ?? '';
                        Log::info('Resposta do assistente: ' . $content);
                        return $content;
                    }
                }
            }

            Log::error('Nenhuma resposta do assistente encontrada: ' . $messagesResponse->body());
            return '';
        } catch (\Exception $e) {
            Log::error('Erro ao chamar a API de assistente: ' . $e->getMessage());
            return '';
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
            usleep(500000);
        }
        return $requestCallback();
    }

    private function getFaqResponse($message, $user)
    {
        $messageEmbedding = $this->generateEmbedding($message);
        if (empty($messageEmbedding)) {
            return null;
        }

        $faqs = Faq::where('user_id', $user->id)
            ->whereNotNull('embedding')
            ->get()
            ->sortByDesc(function ($faq) use ($messageEmbedding) {
                if (!$faq->embedding) return 0;
                $faqEmbedding = json_decode($faq->embedding, true);
                if (!is_array($faqEmbedding) || empty($faqEmbedding)) return 0;
                try {
                    return $this->cosineSimilarity($messageEmbedding, $faqEmbedding);
                } catch (\Exception $e) {
                    Log::error('Erro ao calcular similaridade de cosseno: ' . $e->getMessage());
                    return 0;
                }
            })
            ->first();

        if ($faqs && $this->cosineSimilarity($messageEmbedding, json_decode($faqs->embedding, true)) > 0.8) {
            return $faqs->answer;
        }

        return null;
    }

    private function isValidResponse($response)
    {
        return !empty($response) && strlen($response) > 5 && !str_contains($response, 'não tenho informações');
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

    private function formatResponseWithLinks($content)
    {
        $content = trim(preg_replace('/Como mais posso ajudar\?/i', '', $content));

        $content = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank">$1</a>',
            $content
        );

        $content = preg_replace(
            '/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/',
            '<a href="mailto:$1">$1</a>',
            $content
        );

        $content = preg_replace(
            '/(\+\d{3}\s?\d{3}\s?\d{3}\s?\d{3})/',
            '<a href="https://wa.me/$1" target="_blank">WhatsApp: $1</a>',
            $content
        );

        return $content;
    }

    private function cosineSimilarity($vectorA, $vectorB)
    {
        if (count($vectorA) !== count($vectorB) || empty($vectorA) || empty($vectorB)) {
            return 0;
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < count($vectorA); $i++) {
            $dotProduct += $vectorA[$i] * $vectorB[$i];
            $normA += $vectorA[$i] * $vectorA[$i];
            $normB += $vectorB[$i] * $vectorB[$i];
        }

        $normA = sqrt($normA);
        $normB = sqrt($normB);

        if ($normA == 0 || $normB == 0) {
            return 0;
        }

        return $dotProduct / ($normA * $normB);
    }

    private function logRequest($userId, $message, $userName)
    {
        $logId = Str::uuid();
        Log::info('Kairo Request', [
            'log_id' => $logId,
            'user_id' => $userId,
            'user_name' => $userName,
            'message' => $message,
            'timestamp' => now()->toDateTimeString(),
        ]);
        return $logId;
    }

    private function logResponse($logId, $response, $source)
    {
        Log::info('Kairo Response', [
            'log_id' => $logId,
            'response' => $response,
            'source' => $source,
            'response_length' => strlen($response),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}