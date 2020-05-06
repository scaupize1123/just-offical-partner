<?php

namespace Scaupize1123\JustOfficalPartner\Tests\Controllers;

use Scaupize1123\JustOfficalPartner\Tests\TestCase;
use Scaupize1123\JustOfficalPartner\Partner;
use Scaupize1123\JustOfficalPartner\PartnerTranslation;
use Illuminate\Support\Str;
use JWTAuth;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;

class PartnerControllerTest extends TestCase
{
    protected $partnerRepo;

    public function setUp()
    {
        // 一定要先呼叫，建立 Laravel Service Container 以便測試
        parent::setUp();
        $this->partnerRepo = $this->app->make('Scaupize1123\JustOfficalPartner\Interfaces\PartnerRepositoryInterface');
        // 每次都要初始化資料庫
        Session::start();
        if (!defined('LARAVEL_START')) {
            define('LARAVEL_START', microtime(true));
        }
    }

    public function test_get_partner_page() {
        $filter = [];
        $filter['size'] = 10;
        $filter['text'] = '';
        $filter['lang'] = '';
        $filter['page'] = 1;
        $filter['sort_name'] = 'created_at';
        $filter['sort_type'] = 'desc';
        $list = $this->partnerRepo->getListPage($filter);
        $this->assertTrue($list->isEmpty());
        factory(Partner::class, 15)->create()->each(function($u) {
            $model = factory(PartnerTranslation::class)->make([
                'partner_id' => $u->id
            ]);
            $u->translation()->save($model);
        });
        $filter['lang'] = 2;
        $list = $this->partnerRepo->getListPage($filter);
        $this->assertTrue($list->isEmpty());
    }

    public function test_delete_partner() {
        $filter = [];
        $filter['partner_category_id'] = null;
        $filter['size'] = 10;
        $filter['text'] = '';
        $filter['lang'] = '';
        $filter['page'] = 1;
        $filter['sort_name'] = 'created_at';
        $filter['sort_type'] = 'desc';

        $uuid = '';
        factory(Partner::class)->create()->each(function($u) use (&$uuid) {
            $uuid = $u->uuid;
            $model = factory(PartnerTranslation::class)->make([
                'partner_id' => $u->id
            ]);
            $u->translation()->save($model);
        });
        $partner = $this->partnerRepo->getByUUID($uuid);
        $this->assertTrue($partner->count() == 1);
        $partner = $this->partnerRepo->delete($uuid);
        $list = $this->partnerRepo->getListPage($filter);
        $this->assertTrue($list->isEmpty());
    }

    public function test_update_partner() {
        $uuid = '';
        factory(Partner::class)->create()->each(function($u) use (&$uuid) {
            $uuid = $u->uuid;
            $model = factory(PartnerTranslation::class)->make([
                'partner_id' => $u->id
            ]);
            $u->translation()->save($model);
        });
        $partner = $this->partnerRepo->getByUUID($uuid);
        $this->assertTrue($partner->count() == 1);
        $partner = $this->partnerRepo->update([
            'uuid' => $uuid,
            'name' => 'test',
            'brief' => 'test2',
            'email' => 'a@b.com',
            'link' => 'test2',
            'phone' => 'eetett',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
            'lang' => 1,
        ]);
        $partner = $this->partnerRepo->getByUUID($uuid);
        $this->assertTrue($partner->translation[0]->name == 'test');
    }

    public function test_create_partner() {
        $partner = $this->partnerRepo->create([
            'uuid' => Str::uuid(),
            'name' => 'test',
            'brief' => 'test2',
            'email' => 'a@b.com',
            'link' => 'test2',
            'phone' => 'eetett',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
            'lang' => 1,
        ]);
        $this->assertTrue($partner->count() == 1);
        $partner = $this->partnerRepo->getByUUID($partner['uuid']);
        $this->assertTrue($partner->translation[0]->name == 'test');
    }

    public function test_check_partner() {
        $partner = $this->partnerRepo->create([
            'uuid' => Str::uuid(),
            'name' => 'test',
            'brief' => 'test2',
            'email' => 'a@b.com',
            'link' => 'test2',
            'phone' => 'eetett',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
            'lang' => 1
        ]);
        $this->assertTrue($partner->count() == 1);
        $this->assertTrue($this->partnerRepo->checkPartner($partner['uuid']));
    }

    public function test_check_one_lang_partner() {
        $partner = $this->partnerRepo->create([
            'uuid' => Str::uuid(),
            'name' => 'test',
            'brief' => 'test2',
            'email' => 'a@b.com',
            'link' => 'test2',
            'phone' => 'eetett',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
            'lang' => 1
        ]);
        $this->assertTrue($partner->count() == 1);
        $this->assertTrue($this->partnerRepo->checkOneLangPartner($partner['uuid'], 1));
    }
}