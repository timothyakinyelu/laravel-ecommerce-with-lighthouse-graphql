<?php

namespace App\GraphQL\Queries;

use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use App\Category;

class CategoriesResolver
{
     /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function categories($_, array $args)
    {
        $categories = Category::orderBy('parent_id', 'DESC')
        ->get();
        
        $results = [];
        foreach($categories as $category) {
            $parentID = $category->parent_id;

            if ($parentID !== null) {
                $results[][$parentID] = [
                    'id' => $category->id,
                    'name' => $category->parent->name . ' >> ' . $category->name,
                    'published' => $category->is_published
                ];
            } else {
                $results[] = [
                    $parentID => [
                        'id' => $category->id,
                        'name' => $category->name,
                        'published' => $category->is_published
                    ]
                ];
            }
        }

        $temp = [];
        foreach($results as $xKey => $xData) {
            foreach($xData as $yKey => $yData) {
                $temp[] = $yData;
            }
        }

        $data = $temp;
        
        if(count($data) > 0) {
            // $items = $categories->toArray($args);
            $perPage = 3;
            $total = count($data);
            // dd($total);

            if ($args['page'] !== null) {
                $currentPage = $args['page']; 
            } else {
                $currentPage = Paginator::resolveCurrentPage($args);
            }

            $currentItems = array_slice($data, $perPage * ($currentPage - 1), $perPage);
            $paginator= new Paginator($currentItems, $total, $perPage, $currentPage);
            $paginator->withPath('http://localhost:8000/graphql');

            $pageData = [
                'total' => $paginator->total(),
                'lastPage' => $paginator->lastPage(),
                'currentPage' => $paginator->currentPage(),
                'path' => $paginator->path(),
                'perPage' => $paginator->perPage(),
            ];

            return [
                'data' => $paginator->items(),
                'pageData' => $pageData
            ];
        }
    }
}
