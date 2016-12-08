<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Result extends Model
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
        'image',
        'answer',
        'is_correct',
        'room_id',
        'drawer_id',
        'word_id',
        'guesser_id',
    ];

     /**
     * Get the user that owns the result.
     */
    public function drawer()
    {
        return $this->belongsTo(User::class, 'drawer_id');
    }

    /**
     * Get the user that owns the result.
     */
    public function guesser()
    {
        return $this->belongsTo(User::class, 'guesser_id');
    }

    /**
     * Get the user that owns the result.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the user that owns the result.
     */
    public function word()
    {
        return $this->belongsTo(Word::class);
    }

    /**
     * Diffirentiating current user is either drawer or guesser each turn
     */
    public function isDrawer()
    {
        return $this->drawer_id == Auth::user()->id;
    }

    /**
     * Check if current user is joined
     */
    public function isJoined()
    {
        return Auth::user()->id == $this->drawer_id || Auth::user()->id == $this->guesser_id;
    }
}
