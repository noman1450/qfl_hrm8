<?php

namespace App\Models\AccountsIntegration;

use Illuminate\Database\Eloquent\Model;

class AccCostCenterEntry extends Model
{

    protected $connection = 'mysql2';
    protected $table = 'acc_cost_center_entrys';
    protected $guarded = ['id'];
    public $timestamps = false;
    
}
