<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description', 'status',
    ];

    /**
     * Get the message for the room.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the results for the room.
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Determine if the room can be joined.
     */
    public function canJoin()
    {
        return $this->status == config('room.status.empty') || $this->status == config('room.status.waiting');
    }
}
