<?php

namespace App\Models;

class Topic extends BaseModel
{
    public function saasApp()
    {
        return $this->belongsTo(SaasApp::class, 'saas_app_id');
    }
}
