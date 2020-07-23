<?php

namespace App\GraphQL\Mutations;

use App\Brand;
use Illuminate\Support\Facades\Validator;

class BrandMutator
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        // TODO implement the resolver
        $validator = Validator::make($args, [
            'name' => 'required|unique:categories|max:255',
            'brand_image' => 'image'
        ]);

        if($validator->fails()) {
            return $brand = $validator->errors();
        }

        $brand = new Brand;

        $brand->name = $args['name'];
        $brand->slug = $brand->name;
        $brand->description = $args['description'];

        $filename = time() . '.' . $args['name'] . '.' . $args['brand_image']->extension();
        $path = $args['brand_image']->storeAs('/public/brand', $filename);

        $brand->brand_image = $path;

        $brand->save();
        return $brand;
    }
}
