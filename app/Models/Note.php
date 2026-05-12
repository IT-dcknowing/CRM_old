<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'icon', 'user_id', 'noteable_id', 'noteable_type', 'is_private', 'parent_id', 'is_folder'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function noteable()
    {
        return $this->morphTo();
    }

    public function blocks()
    {
        return $this->hasMany(NoteBlock::class)->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(Note::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Note::class, 'parent_id')->orderBy('updated_at', 'desc');
    }
}
