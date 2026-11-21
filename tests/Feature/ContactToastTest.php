<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactToastTest extends TestCase
{
    use RefreshDatabase;

    private array $valid = ['name' => 'Jane', 'email' => 'jane@example.com', 'message' => 'Hello, I have a project for you.'];

    public function test_background_submit_returns_json_success(): void
    {
        $this->postJson(route('contact.store'), $this->valid)
            ->assertOk()
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'Thanks'));

        $this->assertDatabaseHas('messages', ['email' => 'jane@example.com']);
    }

    public function test_background_submit_returns_field_errors(): void
    {
        $this->postJson(route('contact.store'), ['name' => '', 'email' => 'nope', 'message' => 'short'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    public function test_server_error_is_reported_as_a_friendly_message(): void
    {
        Message::creating(fn () => throw new \RuntimeException('database is down'));

        $this->postJson(route('contact.store'), $this->valid)
            ->assertStatus(500)
            ->assertJsonPath('message', fn ($m) => str_contains($m, "couldn't be sent"));
    }

    public function test_plain_form_post_shows_success_toast_after_redirect(): void
    {
        $this->from('/')->post(route('contact.store'), $this->valid)
            ->assertRedirect(url('/').'#contact')
            ->assertSessionHas('contact_success');

        $this->get('/')->assertSee('toast toast-success', false)->assertSee('Message sent');
    }

    public function test_plain_form_post_shows_error_toast_for_invalid_input(): void
    {
        $this->from('/')->post(route('contact.store'), ['name' => 'Jane', 'email' => 'bad', 'message' => 'Hello there friend'])
            ->assertRedirect('/');

        $this->get('/')->assertSee('toast toast-error', false)->assertSee('Please fix the form')
            ->assertSee('must be a valid email', false);
    }

    public function test_too_many_plain_posts_go_back_with_an_error_toast(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->from('/')->post(route('contact.store'), $this->valid);
        }

        $this->from('/')->post(route('contact.store'), $this->valid)
            ->assertRedirect(url('/').'#contact')
            ->assertSessionHas('toast_error', fn ($m) => str_contains($m, 'too fast'));
    }

    public function test_recommendation_background_submit(): void
    {
        $this->postJson(route('recommend.store'), [
            'name' => 'Sam', 'content' => 'Great developer, delivered on time and beyond.', 'rating' => 5,
        ])->assertOk()->assertJsonPath('message', fn ($m) => str_contains($m, 'Thank you'));

        $this->assertSame(1, Testimonial::where('is_visible', false)->count());
    }
}
