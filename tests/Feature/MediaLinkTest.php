<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Setting;
use App\Models\User;
use App\Support\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaLinkTest extends TestCase
{
    use RefreshDatabase;

    private const DRIVE_ID = '1AbCdEfGhIjKlMnOpQrStUvWxYz012345';

    public function test_google_drive_share_links_are_converted(): void
    {
        foreach ([
            'https://drive.google.com/file/d/'.self::DRIVE_ID.'/view?usp=sharing',
            'https://drive.google.com/open?id='.self::DRIVE_ID,
            'https://drive.google.com/uc?export=download&id='.self::DRIVE_ID,
        ] as $link) {
            $this->assertSame('https://lh3.googleusercontent.com/d/'.self::DRIVE_ID.'=w2000', Media::url($link));
            $this->assertSame('https://drive.google.com/file/d/'.self::DRIVE_ID.'/view', Media::url($link, true));
        }
    }

    public function test_dropbox_and_plain_links(): void
    {
        $this->assertSame('https://www.dropbox.com/s/abc/me.png?raw=1', Media::url('https://www.dropbox.com/s/abc/me.png?dl=0'));
        $this->assertSame('https://www.dropbox.com/s/abc/cv.pdf?dl=1', Media::url('https://www.dropbox.com/s/abc/cv.pdf?dl=0', true));
        $this->assertSame('https://i.imgur.com/x.png', Media::url('https://i.imgur.com/x.png'));
        $this->assertSame(asset('uploads/site/a.png'), Media::url('uploads/site/a.png'));
    }

    public function test_uploads_can_be_turned_off_for_hosts_without_a_durable_disk(): void
    {
        config(['filesystems.uploads_enabled' => false]);
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.settings', ['tab' => 'profile']))->assertOk()->assertDontSee('type="file"', false);

        $this->post(route('admin.settings'), [
            'tab' => 'profile',
            'avatar' => \Illuminate\Http\UploadedFile::fake()->image('me.png'),
        ])->assertSessionHasErrors('upload');
    }

    public function test_admin_can_save_links_and_cv_redirects(): void
    {
        $this->actingAs(User::factory()->create());
        $cv = 'https://drive.google.com/file/d/'.self::DRIVE_ID.'/view?usp=sharing';

        $this->post(route('admin.settings'), ['tab' => 'profile', 'cv_link' => $cv, 'avatar_link' => 'https://i.imgur.com/me.png'])
            ->assertSessionHasNoErrors();

        $this->assertSame($cv, Setting::get('cv'));
        $this->assertSame('https://i.imgur.com/me.png', Setting::get('avatar'));
        $this->get(route('cv'))->assertRedirect('https://drive.google.com/file/d/'.self::DRIVE_ID.'/view');

        $this->post(route('admin.projects.store'), [
            'title' => 'Link Project', 'type' => 'app',
            'image_link' => 'https://drive.google.com/file/d/'.self::DRIVE_ID.'/view',
        ])->assertSessionHasNoErrors();
        $this->assertStringStartsWith('https://lh3.googleusercontent.com/d/', Project::firstWhere('title', 'Link Project')->image_url);

        $this->post(route('admin.settings'), ['tab' => 'profile', 'avatar_link' => 'not a url'])
            ->assertSessionHasErrors('avatar_link');
    }
}
