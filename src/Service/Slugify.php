<?php


namespace App\Service;



class Slugify
{
    public function generate(?string $input) : string
    {
        $slug = mb_strtolower($input);
        $slug = strtolower($slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        $slug = trim($slug);

        return $slug;
    }
}
