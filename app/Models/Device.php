<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
    ];

    /** @return list<string> */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function pings(): HasMany
    {
        return $this->hasMany(Ping::class);
    }
}
