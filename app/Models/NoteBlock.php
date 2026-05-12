<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteBlock extends Model
{
    use HasFactory;

    protected $fillable = ['note_id', 'type', 'content', 'data', 'order', 'reminder_at'];

    protected $casts = [
        'data' => 'json',
        'reminder_at' => 'datetime'
    ];

    public function note()
    {
        return $this->belongsTo(Note::class);
    }
}
