<?php

namespace Scaupize1123\JustOfficalPartner\Repositories;

use Illuminate\Support\Str;
use Scaupize1123\JustOfficalPartner\Partner;
use Scaupize1123\JustOfficalPartner\Helpers;
use Scaupize1123\JustOfficalPartner\PartnerCategory;
use Scaupize1123\JustOfficalPartner\PartnerTranslation;
use Scaupize1123\JustOfficalPartner\Interfaces\PartnerRepositoryInterface;

class PartnerRepository implements PartnerRepositoryInterface
{
    public function translationExists($filter) {
        return function($q) use ($filter) {
            if(!empty($filter['lang'])){
                $q->where('language_id', $filter['lang']);
            }
            if(!empty($filter['text'])) {
                $q->where(function($query) use ($filter) {
                   $query->where('name', 'like', '%'.$filter['text'].'%');
               });
           }
           $q->where('status', 1);
           
        };
    }

    public function getTranslation($filter) {
        return function($q) use ($filter) {
            if(!empty($filter['lang'])){
                $q->where('language_id', $filter['lang']);
            }
            if(!empty($filter['text'])) {
                $q->where(function($query) use ($filter) {
                   $query->where('name', 'like', '%'.$filter['text'].'%');
               });
           }
           $q->where('status', 1);
        };
    }

    public function getListPage($filter) {
        $query_builder = Partner::where('status', 1)
            ->whereHas('translation', $this->translationExists($filter))
            ->with(['translation' => $this->getTranslation($filter),
                    'translation.language']);

        $partnerList = $query_builder->paginate($filter['size']);
        return $partnerList;
    }

    public function getByUUID($uuid, $lang = null) {
        $filter = ['lang' => $lang];
        $queryBuilder = Partner::where('status', 1)
            ->whereHas('translation', $this->translationExists($filter))
            ->where('uuid', $uuid)
            ->with(['translation' => function($q) use ($lang) {
                if(!empty($lang)){
                    $q->where('language_id', $lang);
                }
                $q->where('status', 1);
            },  'translation.language']);
        return $queryBuilder->first();
    }

    public function delete($uuid, $lang = null) {
        if(empty($lang)) {
            $partner = Partner::where('uuid', $uuid)->first();
            $partner->translation()->where('partner_id', $partner->id)->update(['status' => 0]);
            Partner::where('uuid', $uuid)
                ->update(['status' => 0]);
        } else {
            $partner = Partner::where('uuid', $uuid)->first();
            $partner->translation()
                ->where('language_id', $lang)
                ->update(['status' => 0]);
        }
    }

    public function checkOneLangPartner($uuid, $lang) {
        $data = Partner::where('uuid', $uuid)
            ->whereHas('translation', function($q) use ($lang) {
                if (!empty($lang)) {
                    $q->where('language_id', $lang);
                }
            })->get();

        if($data->isEmpty()) {
            return false;
        }
        return true;
    }

    public function checkPartner($uuid) {
        $isExisted =  Partner::where('uuid', $uuid)
            ->get()->count();

        if(empty($isExisted)) {
            return false;
        }
        return true;
    }

    public function update($update) {
        $partner = Partner::where('uuid', $update['uuid'])->first();

        $partner->translation()
            ->updateOrCreate([
                'partner_id' => $partner->id,
                'language_id' => $update['lang']
            ],[
                'name' => $update['name'],
                'brief' => $update['brief'],
                'link' => $update['link'],
                'email' => $update['email'],
                'phone' => $update['phone'],
                'image_name' => $update['image_name'] ?? null,
                'image' => $update['image'] ?? null,
                'status' => 1
            ]);
        return $partner;
    }

    public function create($create) {
        $uuid =  $create['uuid'];
        $partner = Partner::create([
            'uuid' => $uuid,
            'status' => 1,
        ]);

        $partner->translation()->create([
            'name' => $create['name'],
            'brief' => $create['brief'],
            'link' => $create['link'],
            'email' => $create['email'],
            'phone' => $create['phone'],
            'language_id' => $create['lang'],
            'image_name' => $create['image_name'] ?? null,
            'image' => $create['image'] ?? null,
            'status' => 1,
            'partner_id' => $partner->id
        ]);

        return $partner;
    }
}
