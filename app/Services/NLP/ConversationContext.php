<?php

namespace App\Services\NLP;

use Illuminate\Support\Facades\Cache;

class ConversationContext
{
    private string $userId;
    private string $cacheKey;
    private array $context = [];

    public function __construct(string $userId)
    {
        $this->userId = $userId;
        $this->cacheKey = "deby_context_{$userId}";
        $this->load();
    }

    /**
     * Load context from cache
     */
    private function load(): void
    {
        $this->context = Cache::get($this->cacheKey, [
            'conversation_id' => uniqid('conv_'),
            'started_at' => now()->toIso8601String(),
            'current_step' => null,
            'current_intent' => null,
            'collected_entities' => [],
            'pending_slots' => [],
            'conversation_history' => [],
            'state' => 'idle', // idle, onboarding, form_filling, waiting_confirmation
            'metadata' => [],
        ]);
    }

    /**
     * Save context to cache
     */
    private function save(): void
    {
        Cache::put($this->cacheKey, $this->context, now()->addHours(24));
    }

    /**
     * Start onboarding flow
     */
    public function startOnboarding(): void
    {
        $this->context['state'] = 'onboarding';
        $this->context['current_step'] = 1;
        $this->context['pending_slots'] = [
            'store_name',
            'store_category',
            'first_product_name',
            'first_product_price',
            'first_product_stock',
        ];
        $this->save();
    }

    /**
     * Start form filling for an intent
     */
    public function startFormFilling(string $intent, array $requiredSlots): void
    {
        $this->context['state'] = 'form_filling';
        $this->context['current_intent'] = $intent;
        $this->context['pending_slots'] = $requiredSlots;
        $this->context['collected_entities'] = [];
        $this->save();
    }

    /**
     * Add entity to collected entities
     */
    public function addEntity(string $slotName, $value): void
    {
        $this->context['collected_entities'][$slotName] = $value;
        
        // Remove from pending slots
        $key = array_search($slotName, $this->context['pending_slots']);
        if ($key !== false) {
            unset($this->context['pending_slots'][$key]);
            $this->context['pending_slots'] = array_values($this->context['pending_slots']);
        }

        $this->save();
    }

    /**
     * Check if form is complete
     */
    public function isFormComplete(): bool
    {
        return empty($this->context['pending_slots']);
    }

    /**
     * Get next pending slot
     */
    public function getNextPendingSlot(): ?string
    {
        return $this->context['pending_slots'][0] ?? null;
    }

    /**
     * Get all collected entities
     */
    public function getCollectedEntities(): array
    {
        return $this->context['collected_entities'];
    }

    /**
     * Add to conversation history
     */
    public function addToHistory(string $role, string $message): void
    {
        $this->context['conversation_history'][] = [
            'role' => $role, // 'user' or 'assistant'
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
        ];

        // Keep only last 50 messages
        if (count($this->context['conversation_history']) > 50) {
            $this->context['conversation_history'] = array_slice($this->context['conversation_history'], -50);
        }

        $this->save();
    }

    /**
     * Get conversation history
     */
    public function getHistory(int $limit = 10): array
    {
        return array_slice($this->context['conversation_history'], -$limit);
    }

    /**
     * Set state
     */
    public function setState(string $state): void
    {
        $this->context['state'] = $state;
        $this->save();
    }

    /**
     * Get state
     */
    public function getState(): string
    {
        return $this->context['state'];
    }

    /**
     * Set metadata
     */
    public function setMetadata(string $key, $value): void
    {
        $this->context['metadata'][$key] = $value;
        $this->save();
    }

    /**
     * Get metadata
     */
    public function getMetadata(string $key, $default = null)
    {
        return $this->context['metadata'][$key] ?? $default;
    }

    /**
     * Reset context
     */
    public function reset(): void
    {
        Cache::forget($this->cacheKey);
        $this->context = [
            'conversation_id' => uniqid('conv_'),
            'started_at' => now()->toIso8601String(),
            'current_step' => null,
            'current_intent' => null,
            'collected_entities' => [],
            'pending_slots' => [],
            'conversation_history' => [],
            'state' => 'idle',
            'metadata' => [],
        ];
    }

    /**
     * Get full context
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get conversation summary
     */
    public function getSummary(): array
    {
        return [
            'conversation_id' => $this->context['conversation_id'],
            'state' => $this->context['state'],
            'current_intent' => $this->context['current_intent'],
            'progress' => $this->getProgress(),
            'message_count' => count($this->context['conversation_history']),
            'duration' => $this->getDuration(),
        ];
    }

    /**
     * Get progress percentage
     */
    public function getProgress(): int
    {
        if ($this->context['state'] === 'onboarding') {
            $totalSteps = 5;
            $completedSteps = $totalSteps - count($this->context['pending_slots']);
            return (int) (($completedSteps / $totalSteps) * 100);
        }

        if ($this->context['state'] === 'form_filling') {
            $totalSlots = count($this->context['pending_slots']) + count($this->context['collected_entities']);
            if ($totalSlots === 0) return 0;
            return (int) ((count($this->context['collected_entities']) / $totalSlots) * 100);
        }

        return 0;
    }

    /**
     * Get conversation duration
     */
    public function getDuration(): string
    {
        $start = \Carbon\Carbon::parse($this->context['started_at']);
        $duration = $start->diffInMinutes(now());

        if ($duration < 1) {
            return 'Baru saja';
        } elseif ($duration < 60) {
            return "{$duration} menit lalu";
        } else {
            $hours = (int) ($duration / 60);
            return "{$hours} jam lalu";
        }
    }

    /**
     * Check if waiting for confirmation
     */
    public function isWaitingForConfirmation(): bool
    {
        return $this->context['state'] === 'waiting_confirmation';
    }

    /**
     * Set waiting for confirmation
     */
    public function setWaitingForConfirmation(string $action, array $data): void
    {
        $this->context['state'] = 'waiting_confirmation';
        $this->context['metadata']['pending_action'] = $action;
        $this->context['metadata']['pending_data'] = $data;
        $this->save();
    }

    /**
     * Get pending action
     */
    public function getPendingAction(): ?array
    {
        if ($this->context['state'] !== 'waiting_confirmation') {
            return null;
        }

        return [
            'action' => $this->context['metadata']['pending_action'] ?? null,
            'data' => $this->context['metadata']['pending_data'] ?? [],
        ];
    }

    /**
     * Clear pending action
     */
    public function clearPendingAction(): void
    {
        $this->context['state'] = 'idle';
        unset($this->context['metadata']['pending_action']);
        unset($this->context['metadata']['pending_data']);
        $this->save();
    }
}
