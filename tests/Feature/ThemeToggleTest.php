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

    public function test_application_bundle_exposes_the_theme_handler(): void
    {
        $appJs = file_get_contents(resource_path('js/app.js'));

        $this->assertIsString($appJs);
        $this->assertStringContainsString('function setTheme(theme)', $appJs);
        $this->assertStringContainsString('window.setTheme = setTheme;', $appJs);
        $this->assertStringContainsString('document.addEventListener("livewire:navigated"', $appJs);
    }
}
