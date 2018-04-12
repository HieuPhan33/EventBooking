<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersQueue extends Model
{
    protected $table="queue";
    protected $primaryKey = "userID";
}
