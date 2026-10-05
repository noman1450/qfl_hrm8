<?php

namespace App\Models\AccountsIntegration;

use Illuminate\Database\Eloquent\Model;

class HrmAccJournalType extends Model
{
    protected $table = 'hrm_acc_journal_type';

    protected $guarded = ['id'];

    public $timestamps = false;
}
