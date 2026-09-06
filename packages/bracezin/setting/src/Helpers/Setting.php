<?php

namespace Setting\Helpers;

use Str;
use Tji\Helpers\MainHelper;

class Setting extends MainHelper
{
    public function incrementValue($slug)
    {
        $setting = $this->settingRepository->findBySlug($slug);
        $input = [
            'value' => ($setting->type_name == 'number') ? $setting->value + 1 : $setting->value,
        ];
        if ($setting && $setting->id) {
            $this->settingRepository->update($setting->id, $input);
        }
    }

    public function findByCategory($category)
    {
        $category = strtolower($category);

        return $this->settingRepository->index(['category' => $category])->get();
    }

    public function findBySlug($slug)
    {
        $slug = Str::slug($slug, '-');

        return $this->settingRepository->index(['slug' => $slug])->first();
    }

    public function getCodeBySlug($prefix, $code)
    {
        $prefixValue = strtoupper($this->settingRepository->findBySlug($prefix)->value);
        $codeValue = $this->settingRepository->findBySlug($code)->value;

        return $prefixValue.'-'.$codeValue;
    }

    public function update($id, $input)
    {
        $setting = $this->settingRepository->update($id, $input);

        return $setting;
    }

    public function getCodeWithTimestamp($type, $length = 3)
    {
        $initials = $this->getPrefixByString($type, $length);

        return $initials.'-'.date('YmdHis');
    }

    public function getPrefixByString($string, $length = 3)
    {
        $wordCount = Str::of($string)->explode(' ')->count();
        $initials = Str::of($string)
            ->explode(' ')
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');

        if (strlen($initials) < $length) {
            $words = Str::of($string)->explode(' ');
            for ($i = 0; $i < $words->count(); $i++) {
                if ($i === $words->count() - 1) {
                    $initials .= Str::substr($words[$i], 1, $length - strlen($initials));
                } else {
                    $initials .= Str::substr($words[$i], 1, 1);
                }
            }
        } elseif (strlen($initials) > $length) {
            $initials = substr($initials, 0, $length);
        }

        return $initials;
    }
}
