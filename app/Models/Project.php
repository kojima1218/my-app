<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    // 一括代入を許可するカラムを列挙
    protected $fillable = [
        'user_id',  
        'name',
        'description',
        'project_key',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
