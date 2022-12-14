<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Fish extends Authenticatable
{
    protected $table = 'fish';

    protected $fillable = [
        "name",
        "scientific_name",
        "ph_min",
        "ph_max",
        "common_name",
        "family",
        "size_max",
        "size_min",
        "temperature_min",
        "temperature_max"
    ];

    public function getFish($request)
    {
        $query = $this::query();

        if ($request->name) $query->where("name", "like", "%{$request->name}%");

        return $query->get();
    }
}
