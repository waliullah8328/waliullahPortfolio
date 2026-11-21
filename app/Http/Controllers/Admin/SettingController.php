<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Media;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Every customizable site setting. Add a line here and it appears in
     * Admin > Settings automatically; read it in Blade with setting('key').
     */
    public const GROUPS = [
        'profile' => [
            'label' => 'Profile & Hero',
            'icon' => 'user',
            'fields' => [
                'name' => ['type' => 'text', 'label' => 'Your name'],
                'role' => ['type' => 'text', 'label' => 'Headline / role', 'help' => 'e.g. Full-Stack Laravel Developer'],
                'typed_words' => ['type' => 'text', 'label' => 'Rotating hero words', 'help' => 'Comma separated, animated in the hero'],
                'hero_intro' => ['type' => 'textarea', 'label' => 'Hero introduction'],
                'avatar' => ['type' => 'image', 'label' => 'Profile photo'],
                'available_for_work' => ['type' => 'toggle', 'label' => 'Show "Available for work" badge'],
                'cv' => ['type' => 'file', 'label' => 'CV / Resume (PDF, DOC)', 'accept' => '.pdf,.doc,.docx'],
            ],
        ],
        'about' => [
            'label' => 'About',
            'icon' => 'file-text',
            'fields' => [
                'about_title' => ['type' => 'text', 'label' => 'About heading'],
                'about' => ['type' => 'textarea', 'label' => 'About me (Markdown supported)', 'rows' => 8],
                'about_image' => ['type' => 'image', 'label' => 'About section image'],
                'years_experience' => ['type' => 'text', 'label' => 'Stat: years of experience'],
                'projects_completed' => ['type' => 'text', 'label' => 'Stat: projects completed'],
                'happy_clients' => ['type' => 'text', 'label' => 'Stat: happy clients'],
            ],
        ],
        'contact' => [
            'label' => 'Contact & Social',
            'icon' => 'mail',
            'fields' => [
                'email' => ['type' => 'email', 'label' => 'Public email'],
                'phone' => ['type' => 'text', 'label' => 'Phone'],
                'location' => ['type' => 'text', 'label' => 'Location'],
                'github' => ['type' => 'url', 'label' => 'GitHub URL'],
                'linkedin' => ['type' => 'url', 'label' => 'LinkedIn URL'],
                'twitter' => ['type' => 'url', 'label' => 'X / Twitter URL'],
                'facebook' => ['type' => 'url', 'label' => 'Facebook URL'],
                'instagram' => ['type' => 'url', 'label' => 'Instagram URL'],
                'dribbble' => ['type' => 'url', 'label' => 'Dribbble URL'],
                'youtube' => ['type' => 'url', 'label' => 'YouTube URL'],
            ],
        ],
        'appearance' => [
            'label' => 'Appearance & Sections',
            'icon' => 'settings',
            'fields' => [
                'accent_color' => ['type' => 'color', 'label' => 'Primary accent color'],
                'accent_color_2' => ['type' => 'color', 'label' => 'Secondary accent (gradients)'],
                'default_theme' => ['type' => 'select', 'label' => 'Default theme', 'options' => ['dark' => 'Dark', 'light' => 'Light', 'system' => 'Follow system']],
                'home_projects_count' => ['type' => 'number', 'label' => 'Projects shown on home page'],
                'show_about' => ['type' => 'toggle', 'label' => 'Show About section'],
                'show_services' => ['type' => 'toggle', 'label' => 'Show Services (What I do) section'],
                'show_skills' => ['type' => 'toggle', 'label' => 'Show Skills section'],
                'show_experience' => ['type' => 'toggle', 'label' => 'Show Experience section'],
                'show_projects' => ['type' => 'toggle', 'label' => 'Show Projects section'],
                'show_testimonials' => ['type' => 'toggle', 'label' => 'Show Recommendations section'],
                'allow_recommendations' => ['type' => 'toggle', 'label' => 'Let visitors submit recommendations'],
                'show_blog' => ['type' => 'toggle', 'label' => 'Show Blog section'],
                'show_contact' => ['type' => 'toggle', 'label' => 'Show Contact section'],
            ],
        ],
        'seo' => [
            'label' => 'SEO & Footer',
            'icon' => 'globe',
            'fields' => [
                'site_title' => ['type' => 'text', 'label' => 'Browser tab title'],
                'meta_description' => ['type' => 'textarea', 'label' => 'Meta description', 'rows' => 3],
                'favicon' => ['type' => 'image', 'label' => 'Favicon (square PNG)'],
                'footer_text' => ['type' => 'text', 'label' => 'Footer text'],
            ],
        ],
    ];

    public function edit(Request $request)
    {
        $tab = array_key_exists($request->tab, self::GROUPS) ? $request->tab : 'profile';

        return view('admin.settings', ['groups' => self::GROUPS, 'tab' => $tab]);
    }

    public function update(Request $request)
    {
        $tab = $request->input('tab', 'profile');
        abort_unless(array_key_exists($tab, self::GROUPS), 404);
        $fields = self::GROUPS[$tab]['fields'];

        $linkRules = collect($fields)->filter(fn ($f) => in_array($f['type'], ['image', 'file'], true))
            ->mapWithKeys(fn ($f, $key) => ["{$key}_link" => ['nullable', 'url', 'max:2048']]);

        $request->validate($linkRules->all() + collect($fields)->map(fn ($f) => match ($f['type']) {
            'image' => ['nullable', 'image', 'max:5120'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'email' => ['nullable', 'email', 'max:150'],
            'url' => ['nullable', 'url', 'max:255'],
            'number' => ['nullable', 'integer', 'min:0', 'max:100'],
            'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'toggle' => ['nullable'],
            default => ['nullable', 'string', 'max:10000'],
        })->all());

        $values = [];
        foreach ($fields as $key => $field) {
            if (in_array($field['type'], ['image', 'file'], true)) {
                $current = Setting::get($key);
                if ($request->boolean("remove_{$key}")) {
                    Media::delete($current);
                    $current = null;
                }
                $values[$key] = Media::replace($request->file($key), $current, 'site', $request->input("{$key}_link"));
            } elseif ($field['type'] === 'toggle') {
                $values[$key] = $request->boolean($key) ? '1' : '0';
            } else {
                $values[$key] = $request->input($key);
            }
        }

        Setting::put($values);

        return redirect()->route('admin.settings', ['tab' => $tab])->with('success', 'Settings saved.');
    }
}
