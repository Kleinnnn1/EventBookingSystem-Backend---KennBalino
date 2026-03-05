<?php

namespace App\Traits;

trait CommonQueryScopes
{
    // Filter by date: ?date=2026-03-05
    public function scopeFilterByDate($query, $date)
    {
        if ($date) {
            return $query->whereDate('date', $date);
        }
        return $query;
    }

    // Search by title: ?title=concert
    public function scopeSearchByTitle($query, $title)
    {
        if ($title) {
            return $query->where('title', 'like', '%' . $title . '%');
        }
        return $query;
    }
}
