<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'uuid',
        'name',
    ];

    protected $with = ['lastPing'];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function pings(): HasMany
    {
        return $this->hasMany(Ping::class);
    }

    public function lastPing(): HasOne
    {
        return $this->pings()->one()->latestOfMany('created_at');
    }

}
