<?php

namespace App\Models;

use App\Traits\HasCustomFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasCustomFields;

    protected $fillable = [
        'union_id', 'resident_id', 'type', 'amount', 'currency', 'date',
        'description_bn', 'description_en', 'donor_or_recipient_name',
        'category', 'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function union(): BelongsTo
    {
        return $this->belongsTo(UnionProfile::class, 'union_id');
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
