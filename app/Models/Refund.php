<?php

namespace App\Models;

use App\Events\RefundRequestCreated;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;


class Refund extends Model implements Auditable
{
    use HasFactory, HasUuids;
    use \OwenIt\Auditing\Auditable;
    use Notifiable;


    // protected $dispatchesEvents = [
    //     'created' => RefundRequestCreated::class,
    // ];

    // Define the relationship to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Make sure 'user_id' is the foreign key
    }

    protected $fillable = [
        'refund_status',
        'refund_notes',
    ];
}
