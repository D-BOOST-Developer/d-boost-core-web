<?php

namespace App\Http\Controllers;

use App\Services\NLP\DebyAIEngine;
use App\Models\DebyAIChatLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebyAIController extends Controller
{
    /**
     * Call Thinking API (RapidAPI)
     */
    public function callThinkingAPI(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:5000',
        ]);

        try {
            $url = 'https://chatgpt-42.p.rapidapi.com/chat';
            $apiKey = env('RAPIDAPI_KEY', '18c4b2fd16msh32d393319e95b02p1ebdb6jsncda25d8eb8d3');
            $apiHost = env('RAPIDAPI_HOST', 'chatgpt-42.p.rapidapi.com');

            $headers = [
                'x-rapidapi-key: ' . $apiKey,
                'x-rapidapi-host: ' . $apiHost,
                'Content-Type: application/json'
            ];

            $postData = json_encode(['text' => $request->text]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            \Log::info('Thinking API Call', [
                'url' => $url,
                'http_code' => $httpCode,
                'text_length' => strlen($request->text),
                'curl_error' => $curlError
            ]);

            if ($curlError) {
                \Log::error('Thinking API Curl Error', ['error' => $curlError]);
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghubungi API (Curl Error)',
                    'error' => $curlError
                ], 500);
            }

            // Parse response
            $responseData = json_decode($response, true);

            // Check if API returned error
            if ($httpCode !== 200) {
                \Log::error('Thinking API Error Response', [
                    'http_code' => $httpCode,
                    'response' => $response
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'API Error: ' . ($responseData['message'] ?? 'Unknown error'),
                    'http_code' => $httpCode
                ], 500);
            }

            // Success
            return response()->json([
                'success' => true,
                'result' => $response,
                'http_code' => $httpCode
            ]);
        } catch (\Exception $e) {
            \Log::error('Thinking API Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memanggil Thinking API',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process user message with NLP
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'thinkingMode' => 'sometimes|boolean',
        ]);

        $user = Auth::user();
        $engine = new DebyAIEngine($user);

        try {
            $response = $engine->process($request->message);

            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Chat Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process user message with ChatGPT API (Thinking Mode)
     */
    public function chatgpt(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'thinkingMode' => 'sometimes|boolean',
        ]);

        try {
            $message = $request->message;
            
            \Log::info('ChatGPT API Call', [
                'user_id' => Auth::id(),
                'message_length' => strlen($message),
                'thinking_mode' => true
            ]);

            // Call ChatGPT API via RapidAPI
            $response = $this->callChatGPTAPI($message);

            if ($response['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $response['result'],
                    'metadata' => [
                        'thinking_mode' => true,
                        'api' => 'chatgpt'
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $response['error'] ?? 'Gagal menghubungi ChatGPT API',
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('ChatGPT API Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghubungi ChatGPT. Silakan coba lagi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Call ChatGPT API via RapidAPI
     */
    private function callChatGPTAPI(string $message): array
    {
        try {
            $url = 'https://chatgpt-42.p.rapidapi.com/gpt4';
            $apiKey = env('RAPIDAPI_KEY', '18c4b2fd16msh32d393319e95b02p1ebdb6jsncda25d8eb8d3');
            $apiHost = env('RAPIDAPI_HOST', 'chatgpt-42.p.rapidapi.com');

            $headers = [
                'x-rapidapi-key: ' . $apiKey,
                'x-rapidapi-host: ' . $apiHost,
                'Content-Type: application/json'
            ];

            $postData = json_encode([
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $message
                    ]
                ]
            ]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 45);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            \Log::info('ChatGPT API Response', [
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'response_length' => strlen($response)
            ]);

            if ($curlError) {
                \Log::error('ChatGPT API Curl Error', ['error' => $curlError]);
                return [
                    'success' => false,
                    'error' => 'Gagal menghubungi ChatGPT API (Curl Error): ' . $curlError
                ];
            }

            // Parse response
            $responseData = json_decode($response, true);

            if ($httpCode !== 200) {
                \Log::error('ChatGPT API Error Response', [
                    'http_code' => $httpCode,
                    'response' => $response
                ]);
                
                return [
                    'success' => false,
                    'error' => 'ChatGPT API Error: ' . ($responseData['message'] ?? 'Unknown error')
                ];
            }

            // Extract message from response
            $result = '';
            if (isset($responseData['result'])) {
                $result = $responseData['result'];
            } elseif (isset($responseData['choices'][0]['message']['content'])) {
                $result = $responseData['choices'][0]['message']['content'];
            } elseif (is_string($responseData)) {
                $result = $responseData;
            }

            if (empty($result)) {
                return [
                    'success' => false,
                    'error' => 'ChatGPT API mengembalikan respons kosong'
                ];
            }

            return [
                'success' => true,
                'result' => $result
            ];
        } catch (\Exception $e) {
            \Log::error('ChatGPT API Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get conversation history
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $engine = new DebyAIEngine($user);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat percakapan dimuat.',
        ]);
    }

    /**
     * Get quick tips
     */
    public function quickTips()
    {
        $engine = new DebyAIEngine(Auth::user());
        $knowledgeBase = new \App\Services\NLP\KnowledgeBase();

        return response()->json([
            'success' => true,
            'tips' => $knowledgeBase->getQuickTips(),
        ]);
    }

    /**
     * Get onboarding tutorial
     */
    public function onboardingTutorial()
    {
        $knowledgeBase = new \App\Services\NLP\KnowledgeBase();

        return response()->json([
            'success' => true,
            'tutorial' => $knowledgeBase->getOnboardingTutorial(),
        ]);
    }

    /**
     * Search FAQ
     */
    public function searchFAQ(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:500',
        ]);

        $knowledgeBase = new \App\Services\NLP\KnowledgeBase();
        $results = $knowledgeBase->searchFAQ($request->query);

        return response()->json([
            'success' => true,
            'results' => $results,
        ]);
    }

    /**
     * Get FAQ by category
     */
    public function faqByCategory(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
        ]);

        $knowledgeBase = new \App\Services\NLP\KnowledgeBase();
        $faqs = $knowledgeBase->getFAQsByCategory($request->category);

        return response()->json([
            'success' => true,
            'category' => $request->category,
            'faqs' => $faqs,
        ]);
    }

    /**
     * Confirm action
     */
    public function confirmAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'confirmed' => 'required|boolean',
        ]);

        $user = Auth::user();
        $engine = new DebyAIEngine($user);

        if ($request->confirmed) {
            // Execute the action
            $response = $this->executeAction($request->action, []);
            
            return response()->json([
                'success' => true,
                'message' => 'Aksi berhasil dijalankan.',
                'result' => $response,
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Aksi dibatalkan.',
            ]);
        }
    }

    /**
     * Execute action based on intent
     */
    private function executeAction(string $intent, array $data): array
    {
        return match ($intent) {
            'crm.send_broadcast' => $this->executeBroadcast($data),
            'finance.log_expense' => $this->executeLogExpense($data),
            'inventory.add_product' => $this->executeAddProduct($data),
            default => ['status' => 'unknown_action'],
        };
    }

    /**
     * Execute broadcast action
     */
    private function executeBroadcast(array $data): array
    {
        // This would integrate with your actual broadcast system
        return [
            'status' => 'success',
            'message' => 'Broadcast berhasil dikirim.',
            'recipients' => 0, // Would be actual count
        ];
    }

    /**
     * Execute log expense action
     */
    private function executeLogExpense(array $data): array
    {
        // This would integrate with your actual expense logging system
        return [
            'status' => 'success',
            'message' => 'Pengeluaran berhasil dicatat.',
            'amount' => $data['price'] ?? 0,
        ];
    }

    /**
     * Execute add product action
     */
    private function executeAddProduct(array $data): array
    {
        // This would integrate with your actual product creation system
        return [
            'status' => 'success',
            'message' => 'Produk berhasil ditambahkan.',
            'product_name' => $data['product_name'] ?? '',
        ];
    }

    /**
     * Reset conversation
     */
    public function resetConversation()
    {
        $user = Auth::user();
        $engine = new DebyAIEngine($user);

        return response()->json([
            'success' => true,
            'message' => 'Percakapan direset.',
        ]);
    }

    /**
     * Save chat message to database
     */
    public function saveChatLog(Request $request)
    {
        $request->validate([
            'role' => 'required|in:user,assistant',
            'message' => 'required|string',
            'thinking_mode' => 'sometimes|boolean',
            'metadata' => 'sometimes|array',
        ]);

        try {
            $user = Auth::user();

            DebyAIChatLog::create([
                'user_id' => $user->id,
                'role' => $request->role,
                'message' => $request->message,
                'thinking_mode' => $request->thinking_mode ?? false,
                'metadata' => $request->metadata ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chat log berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Save Chat Log Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan chat log.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get chat history for current user
     */
    public function getChatHistory()
    {
        try {
            $user = Auth::user();

            $chatLogs = DebyAIChatLog::where('user_id', $user->id)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'role' => $log->role,
                        'message' => $log->message,
                        'thinking_mode' => $log->thinking_mode,
                        'metadata' => $log->metadata, // Include metadata with table_data, action_link, etc
                        'created_at' => $log->created_at->toIso8601String(),
                    ];
                });

            return response()->json([
                'success' => true,
                'history' => $chatLogs,
                'count' => $chatLogs->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Get Chat History Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat chat.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete all chat logs for current user
     */
    public function deleteChatHistory()
    {
        try {
            $user = Auth::user();

            $deletedCount = DebyAIChatLog::where('user_id', $user->id)->delete();

            \Log::info('Chat History Deleted', [
                'user_id' => $user->id,
                'deleted_count' => $deletedCount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Riwayat chat berhasil dihapus.',
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            \Log::error('Delete Chat History Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus riwayat chat.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
