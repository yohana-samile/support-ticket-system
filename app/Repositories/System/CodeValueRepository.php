<?php

namespace App\Repositories\System;

use App\Models\Access\User;
use App\Models\Operation\Client\Client;
use App\Models\Operation\FixedAsset\DepreciationAllowanceRate;
use App\Models\Operation\Station\Station;
use App\Models\Operation\Station\StationLocation;
use App\Models\System\Designation;
use App\Models\System\Unit;
use App\Models\System\Bank;
use App\Models\System\CodeValue;
use App\Models\System\Traits\OrganizationLogoTrait;
use App\Models\System\UnitGroup;
use App\Repositories\BaseRepository;
use App\Repositories\Hr\Employee\EmployeeRepository;
use App\Repositories\Operation\Project\ProjectTaskTemplateRepository;
use App\Repositories\Operation\Purchase\PurchaseOrderRepository;
use App\Repositories\Operation\Station\StationLocationRepository;
use App\Repositories\Operation\Stock\OfferingRepository;
use App\Repositories\Setting\OrganizationRepository;
use App\Repositories\Standard\StandardCategoryRepository;
use App\Repositories\Standard\StandardSectorRepository;
use App\Repositories\System\CountryRepository;
use App\Repositories\System\Traits\CodeValue\ContactResourceRepoTrait;
use App\Repositories\System\Traits\CodeValue\CurrencyCvRepoTrait;
use App\Repositories\System\Traits\CodeValue\ResourceRepoTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\This;

/**
 * Class CodeValueRepository
 * @package App\Repositories\Sysdef
 * @description Use this class with care, could break the system.
 * Controls all the data dictionaries of the system.
 * @author Erick M. Chrysostom <e.chrysostom@nextbyte.co.tz>
 */
class CodeValueRepository extends BaseRepository
{

    const MODEL = CodeValue::class;
    protected $code_repo;

    /*
     * CodeValueRepository Constructor
     */
    public function __construct(){
        $this->code_repo = new CodeRepository();
    }

    /*
     * Translate CodeValues Entries using lang>code_value
     */

    public function mapIdsForLang($query)
    {
//        $return = $query->map(function ($item, $key) {
//            return ['id' => $item['id'], 'name' => __("code_value." . $item['id'])];
//        });
        $return = $query;
        return $return;
    }

    /*Query active only*/
    public function queryActiveOnly()
    {
        return $this->query()->where('isactive', 1);
    }

    /**
     * Get code value name for translation
     * @param $id
     * @return array|null|string
     */
    public function name($id)
    {
        return  $this->find($id)->name;

    }


    public function nameByReference($reference)
    {
        return  $this->query()->where('reference',$reference)->first()->name;

    }


    /*Get reference*/
    public function reference($cv_id)
    {
        return $this->find($cv_id)->reference ?? null;

    }

    /*no translation*/
    public function nameWithNoLang($id)
    {
        return $this->find($id)->name;
    }
    public function getIdByReferenceRaw($reference){
        $cv = $this->query()->where("reference", $reference)->withoutGlobalScopes()->first();
        return ($cv) ? $cv->id : null;
    }

    /*Get id by reference*/
    public function getIdByReference($reference){
        $cv = $this->getCodeValueByReference($reference);
        return ($cv) ? $cv->id : null;
    }

    public function getCvByCodeAndName($name, $code_id){
        $cv = $this->query()->where('name', $name)->where('code_id', $code_id)->first();
        return ($cv) ? $cv : null;
    }
    public function getCvByCodeAndLowerName($name, $code_id){
        $cv = $this->query()->whereRaw("LOWER(name) = ?", [strtolower($name)])->where('code_id', $code_id)->first();
        return ($cv) ? $cv : null;
    }

    /*
     *
     */
    public function getUserLogTypeLogIn()
    {
        $return = $this->query()->select(['id'])->where("code_id", 1)->where("reference", "ULLGI")->first();
        return $return->id;
    }

    /*
     *
     */
    public function getUserLogTypeLogOut()
    {
        $return = $this->query()->select(['id'])->where("code_id", 1)->where("reference", "ULLGO")->first();
        return $return->id;
    }

    /*
     *
     */
    public function getUserLogTypeFailedLogin()
    {
        $return = $this->query()->select(['id'])->where("code_id", 1)->where("reference", "ULFLI")->first();
        return $return->id;
    }

    /*
     *
     */
    public function getUserLogTypePasswordReset()
    {
        $return = $this->query()->select(['id'])->where("code_id", 1)->where("reference", "ULPRS")->first();
        return $return->id;
    }

    /*
     *
     */
    public function getUserLogTypeUserLockout()
    {
        $return = $this->query()->select(['id'])->where("code_id", 1)->where("reference", "ULULC")->first();
        return $return->id;
    }


    /*
     *
     */
    public function getCodeForSelectFiltered($code_id, array $filter)
    {
        $query = $this->queryActiveOnly()->select(['id'])->where("code_id", $code_id)->whereIn("id", $filter)->get();
        $return = $this->mapIdsForLang($query)->pluck('name', 'id');
        return $return;
    }


    public function getCodeForSelectFilteredReferences($code_id, array $filter_references)
    {
        $query = $this->queryActiveOnly()->select(['id', 'name'])->where("code_id", $code_id)->whereIn("reference", $filter_references)->orderBy('sort')->get();
        $return = $this->mapIdsForLang($query)->pluck('name', 'id');
        return $return;
    }

    public function getCodeForSelectNotInFilteredReferences($code_id, array $filter_references)
    {
        $query = $this->queryActiveOnly()->select(['id', 'name'])->where("code_id", $code_id)->whereNotIn("reference", $filter_references)->orderBy('sort')->get();
        $return = $this->mapIdsForLang($query)->pluck('name', 'id');
        return $return;
    }

    public function getCodeReferenceForSelectFilteredReferences($code_id, array $filter_references)
    {
        $query = $this->queryActiveOnly()->select(['reference', 'name'])->where("code_id", $code_id)->whereIn("reference", $filter_references)->orderBy('sort')->get();
        $return = $this->mapIdsForLang($query)->pluck('name', 'reference');
        return $return;
    }

    public function getCodeReferenceForSelectNotInFilteredReferences($code_id, array $filter_references)
    {
        $query = $this->queryActiveOnly()->select(['reference', 'name'])->where("code_id", $code_id)->whereNotIn("reference", $filter_references)->orderBy('sort')->get();
        $return = $this->mapIdsForLang($query)->pluck('name', 'reference');
        return $return;
    }
    /*

    /*
     * Get all code values by code_id
     * For initiating chained selects
     */
    public function getAllByCode($code_id)
    {
        return $this->query()->select(['id', 'name', 'code_id'])->where("code_id", $code_id)->get();
    }


    public function getCodeValuesForSelect($code_id)
    {
        $query = $this->queryActiveOnly()
            ->where("code_id", $code_id)
            ->orderBy('id', 'asc')
            ->pluck('name','id');
        $return = $query;
        return $return;
    }
    /*Get code values for select with no lang*/
    public function getCodeValuesForSelectWithNoLang($code_id)
    {
        $query = $this->queryActiveOnly()
            ->where("code_id", $code_id)
            ->orderBy('id', 'asc')
            ->pluck('name','id');
        $return = $query;
        return $return;
    }

    /*Get code values by reference for select*/
    public function getCodeValuesReferenceForSelect($code_id)
    {
        $query = $this->queryActiveOnly()->select(['name', 'reference'])->where("code_id", $code_id)->orderBy('sort')->get();
        $return = $query->pluck("name", "reference");
        return $return;
    }

    /**
     * Get CV by reference
     * @param $reference
     * @return mixed
     */
    public function getCodeValueByReference($reference){
        return $return = $this->query()->where("reference", $reference)->first();
    }

    /*
     * Get code values instances by code_id
     */
    public function getCodeValues($code_id)
    {
        return $this->query()->where("code_id", $code_id)->get();
    }
    public function getCodeValuesActive($code_id)
    {
        return $this->query()->where("code_id", $code_id)->where('isactive',1)->orderBy('name')->get();
    }
    /*
     * Get instances of  code values not in specified ids
     */
    public function getCodeValuesNotIn($code_id, array $ids)
    {
        return $this->query()->where("code_id", $code_id)->whereNotIn('id', $ids)->get();
    }

    public function getCodeValuesInRefs($code_id, array $refs)
    {
        return $this->query()->where("code_id", $code_id)->whereIn('reference', $refs)->get();
    }

    /*
     *
     */
    public function getCodeValuesPaginate($code_id, $count = 10)
    {
        return $this->query()->where("code_id", $code_id)->paginate($count);
    }




    /**
     * @return array
     * Get days for select
     */
    public function getDaysForSelect()
    {
        return [
            '1' => 'MONDAY',
            '2' => 'TUESDAY',
            '3' => 'WEDNESDAY',
            '4' => 'THURSDAY',
            '5' => 'FRIDAY',
            '6' => 'SATURDAY',
            '7' => 'SUNDAY',
        ];
    }



    /*
     * Get Code instance from code_id
     */
    public function getCodeInstanceById($code_id)
    {
        return $this->code_repo->find($code_id);
    }

    /*
     * Get code values by code for data table
     */
    public function getCodeValuesByCodeForDataTable($code_id){
        return $this->query()->where('code_id', $code_id);
    }



    /*Get boolean for select*/
    public function getBooleanForSelect()
    {
        return ['0' => 'No', '1' => 'Yes'];
    }


    /**
     * Get standard categories
     */
    public function getStandardCategoriesForSelect($isactive = 1)
    {
        return  (new StandardCategoryRepository())->getIsActiveForSelectBase($isactive);
    }
    /**
     * Get standard categories
     */
    public function getStandardSectorsForSelect($isactive = 1)
    {
        return  (new StandardSectorRepository())->getIsActiveForSelectBase($isactive);
    }


    public function getTopFileUrlAttribute($model,$document_id)
    {
        $document_resource = $model->documents()->where('document_id', $document_id)->first();
        $document_resource_repo = new DocumentResourceRepository();
        $image = isset($document_resource) ? $document_resource_repo->getTopDocFullPathUrl($document_resource->pivot->id) : null;
        return $image;
    }

    /**
     * get the maximum number of sort of a given code value
     * @param $code_id
     * @return mixed
     */
    public function getMaxSort($code_id){
        $code_values = $this->query()->select('sort')
            ->where('code_id', $code_id)
            ->orderBy('sort', 'desc')
            ->first();
        return $code_values->sort ?? 1;
    }

    /**
     * generate references for CodeValue
     * @param $initials
     * @return string
     */
    public function generateReference($initials){
        do
        {
            $token =(randomString(5));
            $reference = $initials.$token;
            $reference = strtoupper($reference);
            $available = $this->query()
                ->select('reference')
                ->where('reference', $reference)->get();
        }
        while(!$available->isEmpty());
        return $reference;
    }



    /**
     * Store Code Value
     * @param array $input
     * @param $code_id
     */
    public function store(array $input, $code){

        $sort = $this->getMaxSort($code->id);
        DB::transaction(function () use ($input, $sort, $code) {
            $query = $this->query()->create([
                'name' => $input['code_name'],
                'description' => $input['description'],
                'code_id' => $code->id,
                'reference' => $this->generateReference('CV'),
                'isactive' => ($input['status'] == 'yes') ? 1 : 0,
                'sort' => ++$sort,
                'lang' =>  $input['code_name_sw'] ?? $input['code_name']
            ]);
            return $query;
        });
    }

    /**
     * @param array $input
     * @param $reference
     */
    public function update(array $input,$code_value){
        DB::transaction(function () use ($input, $code_value) {

            $code_value->update([
                'name' => $input['code_name'],
                'description' => $input['description'],
                'isactive' => ($input['status'] == 'yes') ? 1 : 0,
                'lang' =>  $input['code_name_sw'] ?? $input['code_name']
            ]);
        });

        return $code_value;
    }

    /*Activate / Deactivate i.e. status = 1 activate, = 0 Deactivate*/
    public function activateDeactivate($code_value, $status){
        $code_value->update([
            'isactive' => $status
        ]);
        return $code_value;
    }


    public  function getDiscountTypes() {
        return CodeValue::whereIn('reference', ["DCTPC", "DCTFXD"])->where('isactive', 1)->get();
    }

    public function getStandardSLanguagesForSelect()
    {
        return CodeValue::whereIn('reference', ["STDLANGEN", "STDLANGSW"])->where('isactive', 1)->get();
    }

    public  function getStandardStatus() {
        return CodeValue::whereIn('reference', ["STATUSCRT", "STATUSWD", "STATUSO"])->where('isactive', 1)->get();
    }

}
