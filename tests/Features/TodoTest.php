<?php

namespace Tests;

use App\Models\Todo;
use Database\Factories\TodoFactory;
use Illuminate\Testing\Fluent\AssertableJson;



class TodoTest extends TestCase
{
    public function test_that_base_endpoint_returns_a_successful_response()
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }

    public function test_user_can_see_your_todos()
    {
        $response = $this->get('/todo');

        $response->assertResponseStatus(200);
    }

    public function test_user_can_see_one_todo()
    {
        $response = $this->get('/todo/2');

        $response->assertResponseStatus(200);

        $response->seeJsonStructure(
            [
                "id",
                "title",
                "description",
                "done",
                "done_at",
                "created_at",
                "updated_at",
            ]
        );
    }

    public function test_on_show_user_should_receive_404_erro_when_search_something_that_dont_exist()
    {
        $response = $this->get('/todo/554');

        $response->assertResponseStatus(404);
        $response->seeJson(['data' => 'not found']);
    }

    public function test_user_can_create_todo()
    {
        $todo = Todo::factory()->create();

        $payload = [
            'title' => $todo['title'],
            'description' => $todo['description'],
        ];

        $response = $this->post('/todo', $payload);

        $response->seeStatusCode(201);
        $response->seeInDatabase('todos', $todo->toArray());

    }

    public function test_user_can_update_data_todo()
    {
        $payload = [
            'title' => 'Lavar a entrada da casa e a varanda',
            'description' => 'Não esquecer de lavar a entrada da casa e a varanda amanhã cedo',
        ];

        $response = $this->put('/todo/2', $payload);

        $response->seeStatusCode(201);
        $response->seeJsonStructure([
            "title",
            "description",
            "updated_at",
            "created_at",
            "id"
        ]);
    }

    public function test_user_can_delete_data_todo()
    {
        $todo = Todo::factory()->create();

        $response = $this->delete('/todo/' . $todo->id);

        $response->seeStatusCode(204);
    }

    public function test_user_cant_delete_if_todo_not_exist()
    {
        $response = $this->delete('/todo/fake_value');

        $response->seeStatusCode(404);
    }

    public function test_user_can_set_status_todo_done()
    {
        $todo = Todo::factory()->create();

        $response = $this->post('/todo/' . $todo->id . '/status/done');

        $response->seeStatusCode(200);
    }

    public function test_user_can_set_status_todo_undone()
    {
        $todo = Todo::factory()->create(['done' => '1']);

        $response = $this->post('/todo/' . $todo->id . '/status/undone');

        $response->seeStatusCode(200);
    }

    public function test_on_set_status_user_should_receive_status_422_at_change_todo_not_exist()
    {
        $todo = Todo::factory()->create();

        $response = $this->post('/todo/' . $todo->id . '/status/fake_value');

        $response->seeStatusCode(422);
    }

    private function getErrorStatus(TestCase $response, $code)
    {
        try {
            $response->seeStatusCode($code);
        } catch (\Exception $ex) {
            $this->printIfDie($ex);
        }
    }

    private function printIfDie(\Exception $ex)
    {
        $content = substr($this->response->getContent(), 0, 1000);
        $error = [
            'error' => $ex->getMessage(),
            'content' => $content,
        ];
        dd($error);
    }
}