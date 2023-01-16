<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItdpCompanyUser extends Model
{
    use HasFactory;
    protected $table = 'itdp_company_users';
    protected $guarded = [];

    public function zoom_rooms(){
        return $this->belongsToMany(ZoomRoom::class, 'zoom_itdp_company_users','itdp_company_user_id','zoom_room_id');
    }
}
