<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CloudinaryLabs\CloudinaryLaravel\MediaAlly;

class Events extends Model
{
    use HasFactory;
    use MediaAlly;
    protected $guarded = [];
}
