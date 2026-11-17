<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_valid_submission_creates_a_contact_message_and_shows_a_success_message(): void
    {
        $response = $this->post(route('contact.send'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '0712 345 678',
            'message' => 'I need a quote for a laptop repair.',
        ]);

        $response->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);
    }

    public function test_filled_honeypot_field_rejects_the_submission_without_creating_a_message(): void
    {
        $response = $this->post(route('contact.send'), [
            'website' => 'http://spam.example',
            'name' => 'Spam Bot',
            'email' => 'not-an-email',
            'message' => '',
        ]);

        $response->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_submission_with_missing_required_fields_fails_validation(): void
    {
        $response = $this->post(route('contact.send'), [
            'name' => '',
            'email' => '',
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_submission_with_an_invalid_email_fails_validation(): void
    {
        $response = $this->post(route('contact.send'), [
            'name' => 'Jane Doe',
            'email' => 'not-an-email',
            'message' => 'Hello there',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseCount('contact_messages', 0);
    }
}
