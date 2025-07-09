<?php
namespace App\Models\System\Attribute;

trait CodeValueAttribute
{
    public function getActiveAttribute()
    {
        if ($this->is_active()){
            return __('label.yes');
        }else {
            return  __('label.no');
        }

    }

    /**
     * @return string
     * is Mandatory attribute
     */
    public function getMandatoryAttribute()
    {
        if ($this->is_mandatory()){
            return __('label.yes');
        }else {
            return  __('label.no');
        }

    }

    /*
 *SYstem defined label
 *
 */
    public function getSystemDefinedAttribute()
    {
        if ($this->systemDefined()){
            return __('label.yes');
        }else {
            return  __('label.no');
        }
    }


//    Flags
    public function systemDefined() {
        return $this->is_system_defined == 1;
    }

    public function getEditButtonAttribute() {
        if (!$this->systemDefined()) {
            return '<a href="' . route('code.value.edit', $this->id) . '"  class="btn btn-xs btn-primary" ><i class="icon fa fa-edit" data-toggle="tooltip" data-placement="top" title="' . trans('buttons.general.crud.edit') . '"></i>'.' '. trans('buttons.general.crud.edit').'</a> ';
        }
    }

    /**
     * @return string
     */
    public function getActionButtonsAttribute() {
        return  $this->getEditButtonAttribute();

    }

    /*Mandatory*/
    public function is_mandatory(){
        return $this->is_mandatory == 1;
    }

    /*Is Active flag*/
    public function is_active(){
        return $this->isactive == 1;
    }

    public static function getCodeValueByCodeId($codeId)
    {
        return self::query()->where('code_id', $codeId)->get();
    }
}
