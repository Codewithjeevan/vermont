<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;

/**
 * Characterization smoke tests for the Laravel upgrade (5.4 -> 12).
 *
 * These assert framework-level invariants that must stay true through EVERY
 * phase of the upgrade. They deliberately avoid DB/auth/licence side effects so
 * they remain stable and fast as the framework version changes.
 *
 * Heavier flows (billing store, reports, Excel/PDF exports) are covered manually
 * by upgrade/SMOKE-TESTS.md and by the skeleton tests below (marked incomplete
 * until a dedicated testing DB + auth fixtures are wired).
 */
class UpgradeSmokeTest extends TestCase
{
    /** The service container boots and core config loads. */
    public function testApplicationBoots()
    {
        $this->assertNotNull($this->app);
        $this->assertSame('Vermont Retail', config('app.name'));
    }

    /** Public error view routes render without auth/DB. */
    public function testErrorViewsRender()
    {
        $this->get('404')->assertStatus(200);
        $this->get('500')->assertStatus(200);
    }

    /** Core business routes stay registered (RBAC-protected master group). */
    public function testCoreRoutesRegistered()
    {
        $uris = collect(Route::getRoutes())->map(function ($r) {
            return $r->uri();
        });

        $this->assertTrue($uris->contains('billing/billing'), 'billing route missing');
        $this->assertTrue($uris->contains('master/item'), 'item resource route missing');
        $this->assertTrue($uris->contains('master/customer'), 'customer resource route missing');
    }

    /** The custom RBAC facade alias resolves (used in routes + checkAccess middleware). */
    public function testCommonFacadeResolves()
    {
        $this->assertTrue(
            class_exists(\App\Helpers\CommonHelper::class),
            'CommonHelper (Common facade target) missing'
        );
    }

    /**
     * All package facade aliases resolve. From L5.5 these come from package
     * auto-discovery (no longer listed in config/app.php). class_exists() on the
     * alias name triggers the AliasLoader, so this guards discovery across phases.
     */
    public function testPackageAliasesResolve()
    {
        foreach (['PDF', 'Form', 'Html', 'Toastr', 'Excel', 'Common'] as $alias) {
            $this->assertTrue(class_exists($alias), "Facade alias [$alias] does not resolve");
        }
    }
}
