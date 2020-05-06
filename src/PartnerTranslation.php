<?php

namespace Scaupize1123\JustOfficalPartner;

use Illuminate\Database\Eloquent\Model;

class PartnerTranslation extends Model
{
    protected $fillable = [ 'name',
                            'brief',
                            'link',
                            'email',
                            'phone',
                            'language_id',
                            'status',
                            'image_name',
                            'image',
                            'partner_id'];

    protected $table = 'partners_translation';

    public function language() {
        return $this->hasOne('App\Language', 'id', 'language_id');
    }
}
