<?php

test("tags display",function(){

    $response = $this->get("api/v1/tags");
    $response->assertStatus(200);
    $response->assertJsonStructure([
        
            "*" => [
                'tag name' , 
            ],
       
    ]);

});

test("tag adding", function(){
    $tag = [
        "name" => "saly"
    ];

    $response = $this->post("api/v1/tags",$tag);
    $response->assertStatus(201);
    $tag = $response->json('data');

    $this->assertDatabaseHas('tags', ['name' => $tag['name']]);

});

test('create a tag and than delete it', function () {
    $tag = [
        'name' => 'developement',
    ];

    $res = $this->post('/api/v1/tags', $tag); 
    $res->assertStatus(201);

    $tag = $res->json('data');

    $this->assertDatabaseHas('tags', [
        'name' => $tag['name'],
    ]);

    $res = $this->delete("/api/v1/tags/{$tag['id']}"); 
    $res->assertStatus(200);

    $this->assertDatabaseMissing('tags', [
        'id' => $tag['id'],
    ]);
});

test('tag update', function () {
    $tag = [
        'name' => 'evelopement',
    ];


    $res = $this->post('/api/v1/tags', $tag); 
    $tag = $res->json('data');

    $update = [
        'name' => 'tagName1',
    ];


    $res = $this->put("/api/v1/tags/{$tag['id']}", $update); 
    $res->assertStatus(200);

});

test('validate tag existence', function () {
    $update = ['name' => 'newTagName'];

    $res = $this->put('/api/v1/tags/9999', $update);

    $res->assertStatus(404);
});