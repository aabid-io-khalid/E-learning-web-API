<?php

test("can delete course", function () {
    $course = \App\Models\Course::factory()->create();
    $response = $this->delete("api/v1/courses/{$course->id}");

    $response->assertStatus(200);

    $this->assertDatabaseMissing('courses', ['id' => $course->id]);
});

test("delete returns 404 if course not found", function () {

    $response = $this->delete("api/v1/courses/99999");
    $response->assertStatus(404);
});

test("can create course", function () {
    $category = \App\Models\Category::factory()->create();
    $tags = \App\Models\Tag::factory()->count(2)->create();

    $data = [
        'name' => 'Introduction to PHP',
        'description' => 'Learn the basics of PHP',
        'duration' => 120,
        'level' => 'Beginner',
        'status' => 'open',
        'category_id' => $category->id,
        'sub_category_id' => null,
        'tags' => $tags->pluck('id')->toArray(),
    ];

    $response = $this->post("api/v1/courses", $data);

    $response->assertStatus(201);

    $responseData = $response->json('data');
    $this->assertDatabaseHas('courses', ['name' => $responseData['name']]);

    $course = \App\Models\Course::find($responseData['id']);
    $this->assertCount(2, $course->tags);
});