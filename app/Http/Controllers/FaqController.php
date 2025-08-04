<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Auth::user()->faqs()->latest()->get();
        return view('faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('faqs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $embedding = $this->generateEmbedding($request->question);

        Auth::user()->faqs()->create([
            'question' => $request->question,
            'answer' => $request->answer,
            'embedding' => $embedding ? json_encode($embedding) : null,
        ]);

        return redirect()->route('faqs.index')->with('success', 'FAQ criada com sucesso!');
    }

    public function edit(Faq $faq)
    {
        if ($faq->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado');
        }
        return view('faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        if ($faq->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado');
        }

        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $embedding = $this->generateEmbedding($request->question);

        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'embedding' => $embedding ? json_encode($embedding) : null,
        ]);

        return redirect()->route('faqs.index')->with('success', 'FAQ atualizada com sucesso!');
    }

    public function destroy(Faq $faq)
    {
        if ($faq->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado');
        }

        $faq->delete();
        return redirect()->route('faqs.index')->with('success', 'FAQ excluída com sucesso!');
    }

    public function apiIndex(Request $request)
    {
        $apiToken = $request->query('api_token');
        $user = User::where('api_token', $apiToken)->first();

        if (!$user) {
            return response()->json(['message' => 'Token inválido'], 401);
        }

        $faqs = $user->faqs()->select('question', 'answer')->get();
        return response()->json($faqs);
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