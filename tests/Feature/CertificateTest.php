<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_edit_and_delete_a_certificate(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.certificates.index'))->assertOk()->assertSee('No certificates yet');
        $this->get(route('admin.certificates.create'))->assertOk();

        $this->post(route('admin.certificates.store'), [
            'title' => 'Flutter Advanced',
            'issuer' => 'Google Developers',
            'issued_at' => '2025-03-10',
            'credential_id' => 'ABC-123',
            'credential_url' => 'https://example.com/verify/abc',
            'image_link' => 'https://drive.google.com/file/d/1AbCdEfGhIjKlMnOpQrStUvWxYz012345/view',
            'skills' => 'Flutter, Dart',
            'is_visible' => '1',
        ])->assertRedirect(route('admin.certificates.index'))->assertSessionHasNoErrors();

        $cert = Certificate::sole();
        $this->assertSame('GD', $cert->initials);
        $this->assertSame(['Flutter', 'Dart'], $cert->skill_list);
        $this->assertStringEndsWith('=w1200', $cert->image_url);

        $this->get(route('admin.certificates.edit', $cert))->assertOk()->assertSee('Flutter Advanced');
        $this->put(route('admin.certificates.update', $cert), [
            'title' => 'Flutter Expert', 'issuer' => 'Google Developers', 'remove_image' => '1',
        ])->assertSessionHasNoErrors();

        $cert->refresh();
        $this->assertSame('Flutter Expert', $cert->title);
        $this->assertNull($cert->image);
        $this->assertFalse($cert->is_visible);

        $this->delete(route('admin.certificates.destroy', $cert))->assertRedirect();
        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_validation_rejects_bad_input(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.certificates.store'), [
            'title' => '', 'issuer' => '', 'credential_url' => 'not a url',
            'issued_at' => '2025-05-01', 'expires_at' => '2024-01-01',
        ])->assertSessionHasErrors(['title', 'issuer', 'credential_url', 'expires_at']);
    }

    public function test_home_page_shows_only_visible_certificates(): void
    {
        $this->get(route('home'))->assertOk()->assertDontSee('id="certificates"', false);

        Certificate::create(['title' => 'Shown Cert', 'issuer' => 'Udemy', 'credential_url' => 'https://example.com/v', 'is_visible' => true]);
        Certificate::create(['title' => 'Hidden Cert', 'issuer' => 'Udemy', 'is_visible' => false]);

        $this->get(route('home'))->assertOk()
            ->assertSee('id="certificates"', false)
            ->assertSee('Shown Cert')
            ->assertSee('https://example.com/v')
            ->assertDontSee('Hidden Cert');
    }
}
