<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KairoController extends Controller
{
    public function handleMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'api_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $user = User::where('api_token', $request->api_token)->first();
        if (!$user) {
            return response()->json(['response' => 'Token inválido'], 401);
        }

        // Gerar embedding para a mensagem do usuário
        $message = $request->message;
        $messageEmbedding = $this->generateEmbedding($message);
        if (empty($messageEmbedding)) {
            Log::warning('Não foi possível gerar embedding para a mensagem: ' . $message);
            return response()->json(['response' => 'Desculpe, não consegui processar sua pergunta. Tente novamente.'], 500);
        }

        // Buscar FAQs mais relevantes usando similaridade de cosseno
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
            ->take(5);

        // Preparar o contexto das FAQs
        $faqContext = '';
        if ($faqs->isNotEmpty()) {
            $faqContext = "Base de conhecimento (extraída de https://tecnideia.ao):\n";
            foreach ($faqs as $faq) {
                $faqContext .= "Pergunta: {$faq->question}\nResposta: {$faq->answer}\nFonte: {$faq->source_url}\n\n";
            }
        } else {
            $faqContext = "Nenhuma FAQ diretamente relevante encontrada. Responda com base no conhecimento geral da Tecnideia, mas não invente informações. Se a pergunta estiver fora do domínio, sugira contato com o suporte.";
        }

        // Estruturar o prompt do sistema
        $systemPrompt = <<<EOD
Você é Kairo IA, um assistente virtual da Tecnideia, projetado para fornecer respostas precisas, naturais e elegantes em português, com base exclusivamente na base de conhecimento fornecida (FAQs extraídas de https://tecnideia.ao). Seu objetivo é responder às perguntas do usuário de forma clara, concisa e contextual, utilizando as FAQs como fonte primária.

Instruções:
1. Priorize as FAQs fornecidas na base de conhecimento. Use-as para formular respostas precisas, cruzando informações quando necessário. Cite a fonte (URL) se relevante.
2. Se a pergunta do usuário não corresponder a nenhuma FAQ ou estiver fora do domínio da Tecnideia, não invente informações. Responda: "Desculpe, não tenho informações sobre isso. Entre em contato pelo WhatsApp +244 974 444 060 ou e-mail geral@tecnideia.ao para mais ajuda."
3. Formule respostas naturais, como em uma conversa humana, evitando repetir as FAQs verbatim. Adapte o tom para ser amigável, profissional e envolvente.
4. Evite frases robóticas como "Como mais posso ajudar?". Finalize com um convite sutil para mais perguntas, apenas quando apropriado (ex.: "Alguma outra dúvida?").
5. Quando mencionar URLs, e-mails ou números de WhatsApp, mantenha em texto puro (ex.: https://tecnideia.ao, geral@tecnideia.ao, +244 974 444 060), pois serão formatados como hiperlinks posteriormente.
6. Sempre verifique a veracidade das informações com base nas FAQs fornecidas e evite generalizações ou suposições.

Base de conhecimento:
{$faqContext}
EOD;

        // Estruturar mensagens para a API
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Adicionar histórico, se disponível
        if (!empty($request->history)) {
            $messages = array_merge($messages, array_slice($request->history, -10));
        }

        // Adicionar mensagem do usuário
        $messages[] = ['role' => 'user', 'content' => $message];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
                'temperature' => 0.9, // Ajuste a temperatura para respostas mais criativas
                'max_tokens' => 500, // Limite de tokens para a resposta
                'top_p' => 1.0, // Limite de probabilidade acumulada 
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['choices'][0]['message']['content'])) {
                    $content = $data['choices'][0]['message']['content'];
                    $formattedContent = $this->formatResponseWithLinks($content);
                    return response()->json(['response' => $formattedContent]);
                }
                Log::error('Resposta inválida da API da OpenAI: ' . json_encode($data));
                return response()->json(['response' => 'Resposta inválida da API.'], 500);
            }

            Log::error('Erro na API da OpenAI: ' . $response->body());
            return response()->json(['response' => 'Desculpe, algo deu errado. Tente novamente mais tarde.'], 500);
        } catch (\Exception $e) {
            Log::error('Erro ao chamar a API da OpenAI: ' . $e->getMessage());
            return response()->json(['response' => 'Desculpe, algo deu errado. Tente novamente mais tarde.'], 500);
        }
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

    private function formatResponseWithLinks($content)
    {
        $content = preg_replace('/Como mais posso ajudar\?/i', '', $content);
        $content = trim($content);

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
}
