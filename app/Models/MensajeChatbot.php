<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MensajeChatbot extends Model
{
    protected $table = 'mensajes_chatbot';
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = null;

    protected $fillable = ['cnv_id', 'rem', 'txt_msj', 'int_det'];

    public function conversacion(): BelongsTo
    {
        return $this->belongsTo(ConversacionChatbot::class, 'cnv_id');
    }
}
