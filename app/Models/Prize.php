<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'probability'];

    public static function nextPrize()
    {
        $prizes = self::all();
        $random = mt_rand(0, 10000) / 100;
        $cumulativeProbability = 0;

        foreach ($prizes as $prize) {
            $cumulativeProbability += $prize->probability;
            if ($random <= $cumulativeProbability) {
                return $prize;
            }
        }

        return null;
    }


    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'probability' => 'required|numeric|min:0|max:1',
        ];
    }

}
