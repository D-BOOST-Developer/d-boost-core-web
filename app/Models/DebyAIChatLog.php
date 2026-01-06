<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DebyAIChatLog extends Model
{
    use HasUuids;
    protected $table = 'deby_a_i_chat_logs';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'role',
        'message',
        'metadata',
        'thinking_mode',
    ];

    protected $casts = [
        'metadata' => 'array',
        'thinking_mode' => 'boolean',
    ];

    /**
     * Get the user that owns this chat log
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
