<?php

namespace App\GraphQL\Mutations;

use App\Category;
use Illuminate\Support\Facades\Validator;

class CategoryMutator
{
    
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        $validator = Validator::make($args, [
            'name' => 'required|unique:categories|max:255',
            'category_image' => 'image',
        ]);
        // TODO implement the resolver
        if($validator->fails()) {
            return $category = $validator->errors();
        }

        $category = new Category;

        $category->name = $args['name'];
        $category->slug = $category->name;
        $category->description = $args['description'];
        $category->parent_id = $args['parent_id'];
        $category->is_published = $args['is_published'];

        $fileName = time() . '.' . $args['name'] . '.' . $args['category_image']->extension();        
        $path = $args['category_image']->storeAs('/public/category', $fileName);

        $category->category_image = $path;

        $category->save();
        return $category;
    }
}
