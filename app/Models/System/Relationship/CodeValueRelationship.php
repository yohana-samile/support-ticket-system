<?php

namespace App\Models\System\Relationship;

use App\Models\Business\Commodity;
use App\Models\Business\Tender;
use App\Models\Business\TenderTran;
use App\Models\Information\Legislation;
use App\Models\Information\News;
use App\Models\Stakeholder\Company;
use App\Models\System\Code;
use App\Models\System\RoleCharge;

trait CodeValueRelationship
{



    public function code()
    {
        return $this->belongsTo(Code::class);
    }


    public function codes(){
        return $this->belongsToMany(Code::class, 'code_value_code', 'code_value_id', 'code_id');
    }


}
