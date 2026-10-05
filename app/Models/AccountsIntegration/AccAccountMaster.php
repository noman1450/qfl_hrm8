<?php

namespace App\Models\AccountsIntegration;

use Illuminate\Database\Eloquent\Model;

class AccAccountMaster extends Model
{

    protected $connection = 'mysql2';
    protected $table = 'acc_account_masters';
    protected $guarded = ['id'];
}
