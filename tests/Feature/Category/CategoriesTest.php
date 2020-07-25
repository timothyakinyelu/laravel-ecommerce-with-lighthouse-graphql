<?php

namespace Tests\Feature\Category;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

use App\Category;
use App\User;

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
    
        $this->assertNotNull($id);
    }

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function can_get_categories_where_published_is_true(): void
    {
        $category1 = factory(Category::class)->create(['is_published' => 0]);
        $category2 = factory(Category::class)->create(['is_published' => 1]);
        $category3 = factory(Category::class)->create(['is_published' => 0]);

        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                published(where: { column: PUBLISHED, operator: EQ, value:true }) {
                    is_published
                }
            }
        ');

        $published = $response->json("data.published.*.is_published");
    
        $this->assertSame(
            [
                true
            ],
            $published
        );
    }

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function can_create_category(): void
    {
        $user = $this->loginWithPermission('create-category');

        if($user->can('create-category')) {
            $res = $this->create_category();
    
            $name = $res->json("data.createCategory.name");
    
            $this->assertDatabaseHas('categories', [
                "name" => $name,
            ]);
            $res->assertStatus(200);
        }
        // $this->assertStatus(403);
        
    }

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function cannot_create_category(): void
    {
        $user = factory(User::class)->create();

        $res = $user->can('create-category');
        $this->assertFalse($res);
        // $this->assertStatus(403);
        
    }
    
    /**
     * A basic feature test example.
     *
     * @test
     */
    public function can_update_category(): void
    {

        $user = $this->loginWithPermission('update-category');
        
        $category = $this->create_category();

        $id = $category->json("data.createCategory.id");

        if($user->can('update-category')) {
            $response = $this->graphQL(/** @lang GraphQL */ '
                mutation UpdateCategory($id: ID!, $category: CategoryInput!) {
                    updateCategory(id: $id, category: $category) {
                        id
                        name
                        is_published
                    }
                }
            ', [
                "id" => $id,
                "category" => [
                    'name' => 'Shirts',
                    'parent_id' => null,
                    'slug' => 'shirts',
                    'description' => 'Good Shirts',
                    'is_published' => 1,
                ]
            ]);
    
            $data = $response->json("data.updateCategory.is_published");
    
            $this->assertTrue($data);
        }
    }

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function can_delete_category(): void
    {
        $user = $this->loginWithPermission('delete-category');

        $category = $this->create_category();

        $id = $category->json("data.createCategory.id");

        if($user->can('delete-category')) {
            $response = $this->graphQL(/** @lang GraphQL */ '
                mutation DeleteCategory($id: ID!) {
                    deleteCategory(id: $id) {
                        id
                        name
                    }
                }
            ', [
                "id" => $id
            ]);
    
            $id = $response->json("data.deleteCategory.id");
            $name = $response->json("data.deleteCategory.name");
    
            $this->assertDatabaseMissing('categories', [
                'name' => $name,
            ]);
            
            $this->assertDeleted('categories', [
                'id' => $id
            ]);
        }
    }

    private function create_category()
    {
        $res = $this->multipartGraphQL(
            [
                'operations' => /** @lang JSON */
                    '
                    {
                        "query": "mutation CreateCategory($category: CategoryInput!) { createCategory(category: $category) {id name category_image} }",
                        "variables" : {
                            "category": {
                                "name": "Shirts",
                                "parent_id": null,
                                "description": "Good Shirts",
                                "is_published": 0,
                                "category_image": null
                            }
                        }
                    }
                ',
                'map' => /** @lang JSON */
                    '
                    {
                        "0": ["variables.category.category_image"]
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

