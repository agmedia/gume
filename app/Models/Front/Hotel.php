<?php

namespace App\Models\Front;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Hotel extends Model
{

    /**
     * @var string
     */
    protected $table = 'hotel';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

}
