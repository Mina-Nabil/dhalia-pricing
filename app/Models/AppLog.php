<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

class AppLog extends Model
{
    use HasFactory;

    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';

    const LEVELS = [
        self::LEVEL_INFO, self::LEVEL_WARNING, self::LEVEL_ERROR
    ];

    protected $table = 'app_logs';
    protected $fillable = [
        'title', 'desc', 'level', 'user_id'
    ];

    //static functions
    public static function info($title, $desc = null, ?Model $loggable = null)
    {
        $id = Auth::id();
        self::addLog(self::LEVEL_INFO, $title, $desc, $id, $loggable);
    }

    public static function warning($title, $desc = null, ?Model $loggable = null)
    {
        $id = Auth::id();
        self::addLog(self::LEVEL_WARNING, $title, $desc, $id, $loggable);
    }

    public static function error($title, $desc = null, ?Model $loggable = null)
    {
        $id = Auth::id();
        self::addLog(self::LEVEL_ERROR, $title, $desc, $id, $loggable);
    }

    private static function addLog($level, $title, $desc, $user_id = null, ?Model $loggable = null)
    {
        $newLog = new self([
            "title"     =>  $title,
            "level"     =>  $level,
            "desc"      =>  $desc,
            "user_id"   =>  $user_id
        ]);
        try {
            $newLog->save();
            if ($loggable) {
                $newLog->loggable()->associate($loggable);
                $newLog->save();
            }
        } catch (Exception $e) {
            report($e);
        }
    }


    public static function byDates(?Carbon $start_date = null, ?Carbon $end_date = null)
    {
        return self::fromTo($start_date, $end_date)->get();
    }

    //scopes
    public function scopeFromTo(Builder $query, ?Carbon $start_date = null, ?Carbon $end_date = null)
    {
        $query->when($start_date, function ($query, $start_date) {
            $query->whereDate('created_at', '>=', $start_date->format('Y-m-d'));
        })->when($end_date, function ($query, $end_date) {
            $query->whereDate('created_at', '<=', $end_date->format('Y-m-d'));
        });
    }
    public function scopeExpired(Builder $query)
    {
        $expired_date = Carbon::now()->subMonths(2);
        $query->whereDate('created_at', '<=', $expired_date->format('Y-m-d'));
    }

    ///relations
    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
