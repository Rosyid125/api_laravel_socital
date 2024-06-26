<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $primaryKey = 'notificationid';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userid', 'userid');

    }

    protected $fillable = ['userid', 'trigerrerid', 'notification', 'datetime', 'status'];
}
