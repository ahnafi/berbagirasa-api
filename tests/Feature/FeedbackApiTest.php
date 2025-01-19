<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FeedbackApiTest extends TestCase
{
    /**
     * Test fetching all feedback
     *
     * @return void
     */
    public function test_fetch_feedback(): void
    {
        $this->get('/api/feedback')
            ->assertStatus(200)
            ->assertJson([
            'status' => 'success',
            'message' => 'Data fetched successfully',
        ]);
    }

    /**
     * Test creating a feedback
     *
     * @return void
     */
    public function test_create_feedback(): void
    {
        \DB::table('feedback')->truncate();
        // Structure of the data to be sent
        // 'name' => 'required|string|min:3|max:255',
        // 'email' => 'required|email',
        // 'message' => 'required|string',

        $data = [
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com',
            'message' => 'This is a test feedback',
        ];

        $this->post('/api/feedback', $data)
            ->assertStatus(201)
            ->assertJson([
            'status' => 'success',
            'message' => 'Feedback created successfully',
        ]);
    }

    /**
     * Test fetching a feedback
     *
     * @return void
     */
    public function test_fetch_feedback_by_id(): void
    {
        $this->get('/api/feedback/1')
            ->assertStatus(200)
            ->assertJson([
            'status' => 'success',
            'message' => 'Feedback fetched successfully',
        ]);
    }

    /**
     * Test updating a feedback
     *
     * @return void
     */
    public function test_update_feedback(): void
    {
        // Structure of the data to be sent
        // 'name' => 'required|string|min:3|max:255',
        // 'email' => 'required|email',
        // 'message' => 'required|string',

        $data = [
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com',
            'message' => 'This is a test feedback',
        ];

        $this->put('/api/feedback/1', $data)
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Feedback updated successfully',
            ]);
    }

    /**
     * Test deleting a feedback
     *
     * @return void
     */
    public function test_delete_feedback(): void
    {
        $this->delete('/api/feedback/1')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Feedback deleted successfully',
            ]);
    }
}
