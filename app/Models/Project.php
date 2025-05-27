<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    // 一括代入を許可するカラムを列挙
    protected $fillable = [
        'name',
        'description',
        'project_key',
    ];
}
