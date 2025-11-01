<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'disk',
        'directory',
        'filename',
        'extension',
        'mime_type',
        'size',
    ];

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->directory . '/' . $this->filename);
    }
}
