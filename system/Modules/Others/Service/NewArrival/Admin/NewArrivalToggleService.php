<?php

namespace Modules\Others\Service\NewArrival\Admin;

use Modules\Category\App\Models\Category;

class NewArrivalToggleService
{
    function toggle($id)
    {
        $category = Category::findOrFail($id);

        $category->show_in_new_arrivals = !$category->show_in_new_arrivals;
        $category->save();

        return $category->show_in_new_arrivals;
    }
}