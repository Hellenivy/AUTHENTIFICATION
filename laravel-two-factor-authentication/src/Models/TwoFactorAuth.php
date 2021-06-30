<?php

namespace   Hellen\TwoFactorAuth\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TwoFactorAuth extends Model
{
    use HasFactory;

    /**
     * 
     *
     * @var string
     */
    private $model;

    /**
     * 
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id',
    ];

    /**
     * 
     *
     * @var array
     */
    protected $hidden = [
        'id',
    ];

    /**
     * 
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * 
     *
     * @param 
     */
    public function user(): BelongsTo
    {
        $model = $this->model();

        return $this->belongsTo($model, 'user_id', (new $model)->getKeyName());
    }

    private function model(): string
    {
        if (is_null($this->model)) {
            $this->model = config('twofactor-auth.model');
        }

        return $this->model;
    }

    protected static function newFactory(): Factory
    {
        return \Hellen\TwoFactorAuth\Factories\TwoFactorAuthFactory::new();
    }
}
