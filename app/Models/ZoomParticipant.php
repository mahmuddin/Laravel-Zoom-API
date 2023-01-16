<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoomItdpCompanyUser extends Model
{
    use HasFactory;
    protected $table = 'zoom_participants';
    protected $guarded = [];
}
