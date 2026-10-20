<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConversacionChatbot extends Model
{
    protected $table = 'conversaciones_chatbot';
    const CREATED_AT = 'ini_en';
    const UPDATED_AT = null;

    protected $fillable = ['usr_id', 'id_ses', 'can', 'der_hum', 'ini_en', 'fin_en'];

    protected function casts(): array
    {
        return ['der_hum' => 'boolean', 'ini_en' => 'datetime', 'fin_en' => 'datetime'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usr_id');
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(MensajeChatbot::class, 'cnv_id');
    }
}
