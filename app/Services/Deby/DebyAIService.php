<?php

namespace App\Services\Deby;

use App\Models\DebyAIChatLog;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Deby AI v2 — single entry point for AI chat with smart context injection
 * and conversation memory. Uses Groq (OpenAI-compatible) under the hood.
 */
class DebyAIService
{
    private const MAX_PROMPT_CHARS = 12000;

    public function chat(User $user, string $message): array
    {
        $message = trim($message);
        if ($message === '') {
            return $this->errorResponse('Pesan tidak boleh kosong.');
        }

        try {
            $context = (new BusinessContextBuilder($user))->build();
            $history = $this->loadHistory($user);
            $systemPrompt = $this->buildSystemPrompt($user, $context);

            $messages = $this->composeMessages($systemPrompt, $history, $message);

            $apiResponse = $this->callGroq($messages);
            if (!$apiResponse['success']) {
                Log::warning('Deby AI Groq call failed', [
                    'user_id' => $user->id,
                    'error' => $apiResponse['error'] ?? 'unknown',
                ]);
                return $this->errorResponse('Maaf, Deby sedang sibuk. Coba lagi sebentar lagi.');
            }

            $reply = $apiResponse['reply'];

            $this->persistConversation($user, $message, $reply, [
                'model' => $apiResponse['model'] ?? null,
                'tokens' => $apiResponse['tokens'] ?? null,
                'source' => 'ai_chat',
            ]);

            return [
                'success' => true,
                'message' => $reply,
                'metadata' => [
                    'source' => 'ai_chat',
                    'model' => $apiResponse['model'] ?? null,
                ],
            ];
        } catch (\Throwable $e) {
            Log::error('Deby AI service error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->errorResponse('Terjadi kesalahan internal. Silakan coba lagi.');
        }
    }

    public function logQuickAction(User $user, string $userQuery, string $assistantMessage, array $metadata = []): void
    {
        try {
            $this->persistConversation($user, $userQuery, $assistantMessage, array_merge([
                'source' => 'quick_action',
            ], $metadata));
        } catch (\Throwable $e) {
            Log::warning('Quick action log failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function loadHistory(User $user): array
    {
        $limit = (int) config('services.groq.history_limit', 10);

        $logs = DebyAIChatLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get(['role', 'message'])
            ->reverse()
            ->values();

        return $logs->map(fn ($log) => [
            'role' => $log->role === 'assistant' ? 'assistant' : 'user',
            'content' => (string) $log->message,
        ])->all();
    }

    private function buildSystemPrompt(User $user, string $context): string
    {
        $businessName = $user->businessProfile?->business_name ?: $user->name;
        $timestamp = Carbon::now()->format('d M Y H:i');

        return <<<PROMPT
Kamu adalah Deby AI, asisten bisnis personal untuk pemilik UMKM di platform D-BOOST.

PERAN KAMU:
- Memberi insight dan saran konkret untuk pengembangan bisnis user (bisnis: {$businessName}).
- Menjawab pertanyaan BERDASARKAN data yang diberikan di bawah ini.

FITUR AKTIF D-BOOST SAAT INI:
1. Dashboard (Analitik bisnis ringkas)
2. Inventory (Manajemen Produk dan Stok)
3. Finance/Sales (Pencatatan Transaksi Penjualan dan Pengeluaran)
4. Storefront (Pengaturan Link Toko Digital/Marketplace)

ATURAN KETAT (CRITICAL):
- HANYA rekomendasikan aksi yang bisa dilakukan di 4 fitur aktif di atas.
- JANGAN PERNAH menyarankan fitur yang tidak ada (seperti HR, Payroll, Marketing Campaign, CRM Advanced, dsb).
- HANYA gunakan data yang disediakan di bagian "DATA BISNIS USER" di bawah.
- JANGAN mengarang angka. Jika data tidak tersedia, katakan "data belum tersedia" dan sarankan user mengisi di modul yang relevan (Inventory/Sales/Storefront).
- JANGAN membahas user lain atau data di luar bisnis ini.
- Jika user bertanya hal yang di luar fitur aktif, jawab dengan ramah bahwa fitur tersebut belum tersedia di D-BOOST saat ini, lalu arahkan ke fitur yang ada.

FORMAT JAWABAN:
- Buka dengan ringkasan 1 kalimat yang langsung menjawab pertanyaan.
- Lanjut dengan poin/insight (gunakan bullet kalau lebih dari 2 poin).
- Tutup dengan saran actionable yang spesifik untuk bisnis ini (hanya yang masuk akal dilakukan di Inventory, Sales, atau Storefront).

=== DATA BISNIS USER (snapshot {$timestamp}) ===
{$context}
=== AKHIR DATA ===
PROMPT;
    }

    private function composeMessages(string $systemPrompt, array $history, string $userMessage): array
    {
        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        $totalChars = strlen($systemPrompt) + strlen($userMessage);
        $trimmed = [];

        foreach (array_reverse($history) as $entry) {
            $entryLen = strlen($entry['content']);
            if ($totalChars + $entryLen > self::MAX_PROMPT_CHARS) {
                break;
            }
            $trimmed[] = $entry;
            $totalChars += $entryLen;
        }

        $trimmed = array_reverse($trimmed);

        foreach ($trimmed as $entry) {
            $messages[] = $entry;
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];
        return $messages;
    }

    private function callGroq(array $messages): array
    {
        $apiKey = config('services.groq.api_key');
        if (empty($apiKey)) {
            return ['success' => false, 'error' => 'GROQ_API_KEY belum dikonfigurasi.'];
        }

        $baseUrl = rtrim((string) config('services.groq.base_url', 'https://api.groq.com/openai/v1'), '/');
        $model = (string) config('services.groq.model', 'llama-3.1-8b-instant');
        $timeout = (int) config('services.groq.timeout', 45);
        $maxTokens = (int) config('services.groq.max_tokens', 1024);
        $temperature = (float) config('services.groq.temperature', 0.6);

        $response = Http::withToken($apiKey)
            ->timeout($timeout)
            ->acceptJson()
            ->asJson()
            ->post($baseUrl . '/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => $temperature,
                'max_tokens' => $maxTokens,
            ]);

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => 'HTTP ' . $response->status() . ': ' . $response->body(),
            ];
        }

        $body = $response->json();
        $reply = trim((string) ($body['choices'][0]['message']['content'] ?? ''));

        if ($reply === '') {
            return ['success' => false, 'error' => 'Empty response from Groq.'];
        }

        return [
            'success' => true,
            'reply' => $reply,
            'model' => $body['model'] ?? $model,
            'tokens' => $body['usage'] ?? null,
        ];
    }

    private function persistConversation(User $user, string $userMessage, string $assistantMessage, array $metadata = []): void
    {
        DB::transaction(function () use ($user, $userMessage, $assistantMessage, $metadata) {
            DebyAIChatLog::create([
                'user_id' => $user->id,
                'role' => 'user',
                'message' => $userMessage,
                'thinking_mode' => false,
                'metadata' => null,
            ]);

            DebyAIChatLog::create([
                'user_id' => $user->id,
                'role' => 'assistant',
                'message' => $assistantMessage,
                'thinking_mode' => false,
                'metadata' => $metadata ?: null,
            ]);
        });
    }

    private function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'message' => $message,
            'metadata' => ['source' => 'ai_chat'],
        ];
    }
}
