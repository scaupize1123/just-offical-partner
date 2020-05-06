<?php

namespace Scaupize1123\JustOfficalPartner;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $table = 'partners';

    protected $fillable = ['uuid', 'status'];

    public function translation() {
        return $this->hasMany('Scaupize1123\JustOfficalPartner\PartnerTranslation', 'partner_id', 'id');
    }
}
