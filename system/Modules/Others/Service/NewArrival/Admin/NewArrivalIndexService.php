<?php
namespace Modules\Others\Service\NewArrival\Admin;
use Illuminate\Http\Request;
use Modules\Category\App\Models\Category;
use Carbon\Carbon;

class NewArrivalIndexService
{
    public function getAll(Request $request)
    {
        $now = Carbon::now();

        $categories = Category::query()
            ->whereHas('products', function ($query) use ($now) {
                $query->where('status', true)
                    ->where('new_from', '<=', $now)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('new_to')
                            ->orWhere('new_to', '>=', $now);
                    });
            })
            ->orderBy('sort_order', 'asc')
            ->with(['files' => function ($query) {
                $query->wherePivot('zone', 'logo');
            }])
            ->get()
            ->map(function ($category) {
                $logoFile = $category->files->first();

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'show_in_new_arrivals' => $category->show_in_new_arrivals,
                    'sort_order' => $category->sort_order,
                    'image' => $logoFile ? $logoFile->url : null,
                ];
            });

        return $categories;
    }
}
