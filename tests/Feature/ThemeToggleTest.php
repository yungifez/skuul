<?php

namespace Tests\Feature;

use Tests\TestCase;

class ThemeToggleTest extends TestCase
{
    public function test_header_uses_the_application_theme_handler(): void
    {
        $header = file_get_contents(resource_path('views/livewire/layouts/header.blade.php'));

        $this->assertIsString($header);
        $this->assertStringContainsString("setTheme('light')", $header);
        $this->assertStringContainsString("setTheme('dark')", $header);
        $this->assertStringContainsString("setTheme('system')", $header);
        $this->assertStringNotContainsString('setAprilTheme', $header);
    }

    public function test_layouts_pick_the_theme_before_the_stylesheet_loads(): void
    {
        foreach (['views/layouts/app.blade.php', 'views/layouts/guest.blade.php'] as $layout) {
            $markup = file_get_contents(resource_path($layout));

            $this->assertIsString($markup);
            $this->assertStringContainsString('<x-partials.theme-script />', $markup);
            $this->assertLessThan(
                strpos($markup, "@vite('resources/css/app.css')"),
                strpos($markup, '<x-partials.theme-script />'),
                "{$layout} must set the theme before it loads the stylesheet."
            );
        }
    }

    public function test_the_theme_script_applies_the_class_without_waiting_for_the_bundle(): void
    {
        $script = $this->blade('<x-partials.theme-script />');

        $script->assertSee("classList.toggle('dark'", false);
        $script->assertSee("localStorage.getItem('theme')", false);
        $script->assertDontSee('type="module"', false);
        $script->assertDontSee('defer', false);
    }

    public function test_application_bundle_exposes_the_theme_handler(): void
    {
        $appJs = file_get_contents(resource_path('js/app.js'));

        $this->assertIsString($appJs);
        $this->assertStringContainsString('function setTheme(theme)', $appJs);
        $this->assertStringContainsString('window.setTheme = setTheme;', $appJs);
        $this->assertStringContainsString('document.addEventListener("livewire:navigated"', $appJs);
    }
}
