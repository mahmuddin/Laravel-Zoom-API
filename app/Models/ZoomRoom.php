<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class   ZoomRoom extends Model
{
    use HasFactory;

    protected $table = 'zoom_rooms';
    protected $guarded = [];

    public function itdp_company_user()
    {
        return $this->belongsToMany(ItdpCompanyUser::class, 'zoom_itdp_company_users', 'zoom_room_id', 'itdp_company_user_id')->withPivot('is_verified');
    }
}
