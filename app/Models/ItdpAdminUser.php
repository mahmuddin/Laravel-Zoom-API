<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItdpAdminUser extends Model
{
    use HasFactory;
    protected $table = 'itdp_admin_users';
    protected $guarded = [];

    public function zoom_rooms()
    {
        return $this->belongsToMany(ZoomRoom::class, 'zoom_participants', 'itdp_admin_user_id', 'zoom_room_id');
    }

    public function profile()
    {
        return $this->hasOne(ItdpProfilExp::class, 'id', 'id_profil');
    }
}
