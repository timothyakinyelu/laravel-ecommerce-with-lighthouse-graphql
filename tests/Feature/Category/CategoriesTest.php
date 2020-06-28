<?php

namespace Tests\Feature\Category;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Category;

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function can_get_name_from_categories(): void
    {
        $category1 = factory(Category::class)->create(['name' => "Cat1"]);
        $category2 = factory(Category::class)->create(['name' => "Cat2"]);
        $category3 = factory(Category::class)->create(['name' => "Cat3"]);

        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                categories {
                    id
                    name
                }
            }
        ');

        $names = $response->json("data.categories.*.name");

        $this->assertCount(3, $names);

        $this->assertSame(
            [
                "Cat1",
                "Cat2",
                "Cat3"
            ], $names
        );
    }

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function can_get_categories_with_parent_id_null(): void
    {
        $category1 = factory(Category::class)->create(['parent_id' => null]);
        $category2 = factory(Category::class)->create(['parent_id' => $category1->id]);
        $category3 = factory(Category::class)->create(['parent_id' => null]);

        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                parents(where: { column: PARENT, operator: IS_NULL }) {
                    parent_id
                }
            }
        ');

        $id = $response->json("data.parents.*.parent_id");
    

        $this->assertEquals([null, null], $id);
    }

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function can_get_categories_with_parent_id_not_null(): void
    {
        $category1 = factory(Category::class)->create(['parent_id' => null]);
        $category2 = factory(Category::class)->create(['parent_id' => $category1->id]);
        $category3 = factory(Category::class)->create(['parent_id' => null]);

        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                parents(where: { column: PARENT, operator: IS_NOT_NULL }) {
                    parent_id
                }
            }
        ');

        $id = $response->json("data.parents.*.parent_id");
        // dd($id);
    
        $this->assertNotNull($id);
    }
}
