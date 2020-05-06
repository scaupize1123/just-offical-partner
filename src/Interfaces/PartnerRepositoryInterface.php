<?php

namespace Scaupize1123\JustOfficalPartner\Interfaces;

interface PartnerRepositoryInterface
{
    public function getListPage($filter);

    public function delete($uuid, $lang = null);

    public function create($create);

    public function update($update);

    public function getByUUID($uuid, $lang = null);

    //check lang slider exist
    public function checkOneLangPartner($uuid, $lang);

    public function checkPartner($uuid);
}
