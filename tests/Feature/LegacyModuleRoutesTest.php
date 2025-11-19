<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacyModuleRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_legacy_index(): void
    {
        $this->get(route('legacy.modules.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_list_modules(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('legacy.modules.index'));

        $response->assertOk()
            ->assertSee('Pacientes y HC', false)
            ->assertSee('Facturación', false);
    }

    public function test_authenticated_user_can_view_module_detail(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('legacy.modules.show', ['module' => 'billing']));

        $response->assertOk()
            ->assertSee('BillingController.php', false)
            ->assertSee('api/billing/guardar_billing.php', false);
    }

    public function test_api_manifest_is_available(): void
    {
        $response = $this->getJson(route('api.legacy.modules.index'));

        $response->assertOk()
            ->assertJsonPath('data.0.slug', 'pacientes');
    }

    public function test_api_module_show_returns_detail(): void
    {
        $response = $this->getJson(route('api.legacy.modules.show', ['module' => 'solicitudes']));

        $response->assertOk()
            ->assertJsonPath('data.slug', 'solicitudes')
            ->assertJsonPath('data.routes.api.0.uri', '/api/solicitudes/guardar.php');
    }

    public function test_api_asset_routes_provide_metadata(): void
    {
        $allAssets = $this->getJson(route('api.legacy.modules.assets.index', ['module' => 'pacientes']));

        $allAssets->assertOk()
            ->assertJsonPath('data.styles.0.relative', 'css/vendors_css.css');

        $styles = $this->getJson(route('api.legacy.modules.assets.show', [
            'module' => 'pacientes',
            'type' => 'scripts',
        ]));

        $styles->assertOk()
            ->assertJsonPath('data.type', 'scripts')
            ->assertJsonStructure(['data' => ['type', 'entries']]);
    }
}
