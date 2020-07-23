<?php

namespace Tests\Feature\Brand;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

use App\Brand;

class BrandsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @test
    */
    public function can_get_name_from_brands(): void
    {
        $brand1 = factory(Brand::class)->create(['name' => "Brand1"]);
        $brand2 = factory(Brand::class)->create(['name' => "Brand2"]);
        $brand3 = factory(Brand::class)->create(['name' => "Brand3"]);

        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                brands {
                    id
                    name
                }
            }
        ');

        $names = $response->json("data.brands.*.name");

        $this->assertCount(3, $names);

        $this->assertSame(
            [
                "Brand1",
                "Brand2",
                "Brand3"
            ], $names
        );
    }

    /**
     * A basic feature test example.
     *
     * @test
    */
    public function can_create_brand()
    {
        $brand = $this->create_brand();

        $name = $brand->json("data.createBrand.name");

        $this->assertDatabaseHas('brands', [
            "name" => $name
        ]);
        $brand->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @test
    */
    public function can_update_brand()
    {
        $brand = $this->create_brand();

        $id = $brand->json("data.createBrand.id");

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation UpdateBrand($id: ID!, $brand: BrandInput!) {
                updateBrand(id: $id, brand: $brand) {
                    id
                    name
                }
            }
        ', [
            "id" => $id,
            "brand" => [
                'name' => 'Rissa Red',
            ]
        ]);

        
        $name = $response->json("data.updateBrand.name");

        $this->assertDatabaseHas('brands', [
            "name" => $name
        ]);
        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function can_delete_brand(): void
    {
        $brand = $this->create_brand();

        $id = $brand->json("data.createBrand.id");

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation DeleteBrand($id: ID!) {
                deleteBrand(id: $id) {
                    id
                    name
                }
            }
        ', [
            "id" => $id
        ]);

        $id = $response->json("data.deleteBrand.id");
        $name = $response->json("data.deleteBrand.name");

        $this->assertDatabaseMissing('brands', [
            'name' => $name,
        ]);
        
        $this->assertDeleted('brands', [
            'id' => $id
        ]);
    }

    private function create_brand()
    {
        $res = $this->multipartGraphQL(
            [
                'operations' => /** @lang JSON */
                    '
                    {
                        "query": "mutation CreateBrand($brand: BrandInput!) { createBrand(brand: $brand) {id name brand_image slug} }",
                        "variables" : {
                            "brand": {
                                "name": "Dunnis",
                                "description": "We design",
                                "brand_image": null
                            }
                        }
                    }
                ',
                'map' => /** @lang JSON */
                    '
                    {
                        "0": ["variables.brand.brand_image"]
                    }
                ',
            ],
            [
                '0' => UploadedFile::fake()->create('image.jpg', 500)
            ]
        );

        return $res;
    }
}
