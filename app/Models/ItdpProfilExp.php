<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItdpProfilExp extends Model
{
    use HasFactory;
    protected $table = 'itdp_profil_eks';
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(ItdpCompanyUser::class, 'id_profil', 'id');
    }
}
