<?php

namespace Modules\Media\Service\File;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Modules\Media\App\Models\File;

class FileIndexService
{
    function index($data)
    {
        if (isset($data['grouped']) || isset($data['fileCategoryId']) || isset($data['fileName']) || isset($data['sortBy']) || isset($data['sortDirection'])) {
            $page = 1;
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        }

        $query = File::query();

        if (isset($data['grouped'])) {
            $query->where('is_grouped', false);
        }

        if (isset($data['fileCategoryId'])) {
            $query->where('file_category_id', $data['fileCategoryId']);
        }

        $query->when(isset($data['fileName']), function ($query) use ($data) {
            return $query->where('filename', 'like', '%' . $data['fileName'] . '%');
        });

        if (isset($data['sortBy'])) {
            $sortBy = $data['sortBy'];
            $sortDirection = $data['sortDirection'] ?? 'asc';

            switch ($sortBy) {
                case 'filename':
                case 'created_at':
                case 'size':
                    $query->orderBy($sortBy, $sortDirection);
                    break;
                default:
                    $query->orderBy('filename', 'asc');
                    break;
            }
        } else {
            $query->latest();
        }

        $result = $query->select('id',
            DB::raw("CONCAT(filename, '.', extension) AS fileName"),
            'width',
            'height',
            DB::raw("CONCAT(path, '/', temp_filename) AS imageUrl"),
            DB::raw("CONCAT(path, '/Thumbnail/', temp_filename) AS thumbnailUrl"))
            ->paginate(30);

        return $result ?? collect([]);
    }
}
