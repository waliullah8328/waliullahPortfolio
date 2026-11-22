<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCategoryTest extends TestCase
{
    use RefreshDatabase;

    private function project(string $title, ?string $category): Project
    {
        return Project::create(['title' => $title, 'slug' => str($title)->slug(), 'type' => 'app', 'category' => $category, 'is_published' => true]);
    }

    public function test_admin_can_add_rename_and_delete_categories(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.categories.index'))->assertOk()->assertSee('No categories yet');

        $this->post(route('admin.categories.store'), ['name' => 'Fintech'])->assertSessionHasNoErrors();
        $this->post(route('admin.categories.store'), ['name' => 'fintech'])->assertSessionHasErrors('name');

        $cat = ProjectCategory::sole();
        $project = $this->project('WalletX', 'Fintech');

        $this->put(route('admin.categories.update', $cat), ['name' => 'Finance'])->assertSessionHasNoErrors();
        $this->assertSame('Finance', $cat->fresh()->name);
        $this->assertSame('Finance', $project->fresh()->category);

        $this->delete(route('admin.categories.destroy', $cat))->assertRedirect();
        $this->assertDatabaseCount('project_categories', 0);
        $this->assertNull($project->fresh()->category);
    }

    public function test_renaming_to_an_existing_name_merges_categories(): void
    {
        $this->actingAs(User::factory()->create());
        $education = ProjectCategory::create(['name' => 'Education']);
        $educational = ProjectCategory::create(['name' => 'Educational']);
        $a = $this->project('BiddaBari', 'Educational');
        $b = $this->project('EduLearn', 'Education');

        $this->put(route('admin.categories.update', $educational), ['name' => 'education'])->assertSessionHasNoErrors();

        $this->assertModelMissing($educational);
        $this->assertModelExists($education);
        $this->assertSame('Education', $a->fresh()->category);
        $this->assertSame('Education', $b->fresh()->category);
    }

    public function test_project_form_only_accepts_managed_categories(): void
    {
        $this->actingAs(User::factory()->create());
        ProjectCategory::create(['name' => 'SaaS']);

        $this->get(route('admin.projects.create'))->assertOk()->assertSee('<option value="SaaS"', false);

        $base = ['title' => 'BizDesk', 'type' => 'website'];
        $this->post(route('admin.projects.store'), $base + ['category' => 'Made Up'])->assertSessionHasErrors('category');
        $this->post(route('admin.projects.store'), $base + ['category' => 'SaaS'])->assertSessionHasNoErrors();
        $this->assertSame('SaaS', Project::sole()->category);
    }

    public function test_projects_page_filters_follow_the_admin_order(): void
    {
        ProjectCategory::create(['name' => 'Zeta', 'sort_order' => 0]);
        ProjectCategory::create(['name' => 'Alpha', 'sort_order' => 1]);
        ProjectCategory::create(['name' => 'Unused', 'sort_order' => 2]);
        $this->project('One', 'Alpha');
        $this->project('Two', 'Zeta');

        $this->get(route('projects.index'))->assertOk()
            ->assertSeeInOrder(['All categories', 'Zeta', 'Alpha'])
            ->assertDontSee('>Unused<', false);
    }
}
