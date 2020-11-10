<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Languages extends Model
{
    const EN = 1;

    protected $table = "language";
    protected $primaryKey = "language_id";
    public $timestamps = false;

    protected $guarded = ['language_id'];
    /**
     * Set Backend Language
     */

    static function setBackLang()
    {
        $lang = self::getBackLang();
        App::setLocale($lang);
    }
    /**
     * Get Backend Language
     *
     * @return string
     */
    static function getBackLang()
    {
        $lang = session('back-lang');
        $defaultLang = 'en';
        if ($lang) {
            $languageData = self::where('language_id', $lang)->first();
            if (!empty($languageData)) {
                $defaultLang = $languageData->code;
            }
        }
        return $defaultLang;
    }
    static function getBackLanguageId()
    {
        $languageId = session('back-lang');
        if (empty($languageId)) {
            $languageId = 1;
        }
        return $languageId;
    }

    static function getCurrentLangData()
    {
        $languageId = session('back-lang');
        if (empty($languageId)) {
            $languageId = 1;
        }
        $languageData = self::where('language_id', $languageId)->first();
        return $languageData;
    }

}
