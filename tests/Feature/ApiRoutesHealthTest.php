<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiRoutesHealthTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_public_routes()
    {
        // Test Register (Validasi error karena data kosong)
        $response = $this->postJson('/api/register', []);
        $response->assertStatus(422);

        // Test Login (Validasi error karena data kosong)
        $response = $this->postJson('/api/login', []);
        $response->assertStatus(422);
    }

    public function test_protected_routes_without_auth_return_unauthorized()
    {
        $this->getJson('/api/me')->assertStatus(401);
        $this->postJson('/api/logout')->assertStatus(401);
    }

    public function test_class_routes()
    {
        // Index
        $this->getJson('/api/classes')->assertStatus(401);

        // Bikin dummy ID untuk test show yang mungkin tidak ketemu (404)
        $this->getJson('/api/classes/999')->assertStatus(401);
    }

    public function test_enrollment_routes()
    {
        $this->getJson('/api/my-classes')->assertStatus(401);

        $this->postJson('/api/enroll', [])->assertStatus(401);
    }

    public function test_material_routes()
    {
        // Testing endpoint pengambilan data via URL parameter dummy (contoh 999)
        $this->getJson('/api/classes/999/materials')->assertStatus(401);
    }

    public function test_assignment_routes()
    {
        $this->getJson('/api/classes/999/assignments')->assertStatus(401);
    }

    public function test_submission_routes()
    {
        $this->getJson('/api/my-submissions')->assertStatus(401);

        $this->getJson('/api/materials/999/my-submission')->assertStatus(401);

        $this->getJson('/api/assignments/999/submissions')->assertStatus(401);
    }

    public function test_grade_routes()
    {
        $this->getJson('/api/my-grades')->assertStatus(401);
    }

    public function test_announcement_routes()
    {
        $this->getJson('/api/classes/999/announcements')->assertStatus(401);
    }
}
