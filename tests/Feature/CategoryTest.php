<?php

test("can list categories",function(){

    $response = $this->get("api/v1/categories");
    $response->assertStatus(200);
    $response->assertJsonStructure([
        
            "*" => [
                'name' , 'parent_id'
            ],
       
    ]);

});

test("can add category", function(){
    $parentCategory = \App\Models\Category::create([
        'name' => 'Parent Category',
        'parent_id' => null, 
    ]);

    $category = [
        "name" => "saly",
        "parent_id" => $parentCategory->id, 
    ];

    $response = $this->post("api/v1/categories", $category);
    $response->assertStatus(201);

    $responseData = $response->json('data');
    $this->assertDatabaseHas('categories', ['name' => $responseData['name']]); 
});