<?php
namespace Modules\Others\Service\NewArrival\User;

use Illuminate\Support\Collection;
use Modules\Category\App\Models\Category;
use Carbon\Carbon;

class NewArrivalIndexService
{
    public function getAll(): Collection
    {
        $now = Carbon::now();

        return Category::query()
            ->where('is_active', true)
            ->where('show_in_new_arrivals', true)
            ->whereHas('products', function($query) use ($now) {
                $query->where('status', true)
                    ->where('new_from', '<=', $now)
                    ->where(function($q) use ($now) {
                        $q->whereNull('new_to')
                            ->orWhere('new_to', '>=', $now);
                    });
            })
            ->orderBy('sort_order', 'asc')
            ->with(['files' => function($query) {
                $query->wherePivot('zone', 'logo');
            }])
            ->get()
            ->map(function($category) {
                $logoFile = $category->files->first();

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'sortOrder' => $category->sort_order,
                    'image' => $logoFile ? $logoFile->url : null,
                ];
            });
    }
}
