<?php

namespace App\Models\AccountsIntegration;

use Illuminate\Database\Eloquent\Model;

class AccAccountDetail extends Model
{

    protected $connection = 'mysql2';
    protected $table = 'acc_account_details';
    protected $guarded = ['id'];
    public $timestamps = false;

}
