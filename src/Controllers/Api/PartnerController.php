<?php

namespace Scaupize1123\JustOfficalPartner\Controllers\Api;

use Storage;
use Validator;
use App\Helpers;
use App\Traits\ImageTrait;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Scaupize1123\JustOfficalPartner\Partner;
use App\Exceptions\Api\BadRequestException;
use Scaupize1123\JustOfficalPartner\Resources\Partner as PartnerResources;
use Scaupize1123\JustOfficalPartner\Interfaces\PartnerRepositoryInterface;
use Scaupize1123\JustOfficalPartner\Resources\SignlePartner as SinglePartnerResources;

class PartnerController extends \App\Http\Controllers\Controller
{
    use ImageTrait;

    private $partnerRepo = null;

    public function __construct(PartnerRepositoryInterface $partnerRepo) 
    {
        $this->partnerRepo = $partnerRepo;
    }

    public function showPage()
    {
        $filter = [];
        $filter['size'] = Input::get('size') ?? 10;
        $filter['text'] = Input::get('text') ?? '';
        $filter['lang'] = Input::get('lang') ?? '';
        $filter['page'] = Input::get('page') ?? 1;

        if (Input::get('sort') === 'sort_date_desc') {
            $sort_name = 'created_at';
            $sort_type = 'desc';
        } else if (Input::get('sort') === 'sort_date_asc') {
            $sort_name = 'created_at';
            $sort_type = 'asc';
        } else if (Input::get('sort') === 'sort_title_desc') {
            $sort_name = 'partner_translation.name';
            $sort_type = 'desc';
        } else if (Input::get('sort') === 'sort_title_asc') {
            $sort_name = 'partner_translation.name';
            $sort_type = 'asc';
        } 

        $filter['sort_name'] = $sort_name ?? 'created_at';
        $filter['sort_type'] = $sort_type ?? 'desc';
        $result = $this->partnerRepo->getListPage($filter);

        return PartnerResources::collection($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  request()
     * @param  \App\partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function update($uuid, Request $request)
    {
        $message = [
            'name.required' => '名稱為必填',
            'lang.required' => '語言為必填',
        ];

        $validator = Validator::make(request()->all(), [
            'lang' => 'required',
            'name' => 'required',
        ],$message);

        if (!$validator->fails()) {
            $thisPartner = $this->partnerRepo->getByUUID($uuid, request()->input('lang'));
            if (empty($thisPartner)) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("partner not found");
            }
            $update = [];

            if (request()->hasFile('image')) {
                if(!empty($thisPartner->translation[0]->image)) {
                    $fileSplit = explode('/', $thisPartner->translation[0]->image);
                    $filename = $fileSplit[count($fileSplit)-1];
                    ImageTrait::deleteFile('/partner/'.$uuid.'/'.$filename);
                }
                $image = request()->file('image');
                $mimeType = $image->getMimeType();
                $filename = ImageTrait::saveFile($image, storage_path('app/public').'/partner/'.$uuid, ImageTrait::transMimeType($mimeType));
                $update['image'] = Storage::url('partner/'.$uuid.'/'.$filename);
                $update['image_name'] = $image->getClientOriginalName();
            } else {
                $update['image'] = $thisPartner->translation[0]->image ?? null;
                $update['image_name'] = $thisPartner->translation[0]->image_name ?? null;
            }

            $update['uuid'] = $uuid;
            $update['lang'] = request()->input('lang');
            $update['name'] = request()->input('name');
            $update['brief'] = request()->input('brief') ?? null;
            $update['link'] = request()->input('link') ?? null;
            $update['email'] = request()->input('email') ?? null;
            $update['phone'] = request()->input('phone') ?? null;
            $partner = $this->partnerRepo->update($update);
            return response()->json(['ReturnCode' => 0, 'partner' => $partner]);
            
        } else {
            throw new BadRequestException($validator->errors());
        }
    }

    public function create()
    {
        $message = [
            'name.required' => '名稱為必填',
            'lang' => '語言為必填',
        ];
        
        $validator = Validator::make(request()->all(), [
            'lang' => 'required',
            'name' => 'required',
        ],$message);

        if (!$validator->fails()) {
            $create = [];
            $uuid = Str::uuid();

            if (request()->hasFile('image')) {
                $image = request()->file('image');
                $mimeType = $image->getMimeType();
                $filename = ImageTrait::saveFile($image, storage_path('app/public').'/partner/'.$uuid,
                                                 ImageTrait::transMimeType($mimeType));
                $create['image'] = Storage::url('partner/'.$uuid.'/'.$filename);
                $create['image_name'] = $image->getClientOriginalName();
            }

            $create['uuid'] = $uuid;
            $create['lang'] = request()->input('lang');
            $create['name'] = request()->input('name');
            $create['brief'] = request()->input('brief') ?? null;
            $create['link'] = request()->input('link') ?? null;
            $create['email'] = request()->input('email') ?? null;
            $create['phone'] = request()->input('phone') ?? null;
            $partner = $this->partnerRepo->create($create);

            return response()->json(['ReturnCode' => 0, 'partner' => $partner]);
        } else {
            throw new BadRequestException($validator->errors());
        }
    }


    public function showSingle($uuid)
    {
        $filter = [];
        $filter['lang'] = Input::get('lang') ?? null;

        $data_array = array(
            "uuid" => $uuid,
        );

        $validator = Validator::make($data_array, [
            'uuid' => 'required',
        ]);

        if (!$validator->fails()) {
            $partner = $this->partnerRepo->getByUUID($uuid, $filter['lang']);
            if(empty($partner)) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("partner not found");
            }
            return new SinglePartnerResources($partner);
        } else {
            throw new BadRequestException($validator->errors());
        }
    }

    public function delete($uuid)
    {
        $data = [];
        $data['lang'] = request()->input("lang") ?? null;
        $data['uuid'] = $uuid;

        $validator = Validator::make($data, [
            'uuid' => 'required',
        ]);

        if (!$validator->fails()) {
            if ($this->partnerRepo->checkOneLangPartner($data['uuid'], $data['lang']) == 0) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("partner not found");
            }
            $this->partnerRepo->delete($data['uuid'], $data['lang']);
            
            return response()->json(['ReturnCode' => 0]);
        } else {
            throw new BadRequestException($validator->errors());
        }
    }
}