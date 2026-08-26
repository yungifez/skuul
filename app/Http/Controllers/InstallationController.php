<?php

namespace App\Http\Controllers;

use App\Actions\Installation\CompleteExistingInstallation;
use App\Actions\Installation\ConfigureDatabase;
use App\Actions\Installation\GenerateApplicationKey;
use App\Actions\Installation\InstallApplication;
use App\Actions\Installation\SeedWorldData;
use App\Http\Requests\InstallApplicationRequest;
use App\Http\Requests\InstallDatabaseRequest;
use App\Models\Installation;
use App\Services\Installation\InstallationReadiness;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class InstallationController extends Controller
{
    /**
     * Show the one-time installer and its preflight checks.
     */
    public function index(
        InstallationReadiness $readiness,
        ConfigureDatabase $configureDatabase,
        CompleteExistingInstallation $completeExistingInstallation,
    ): View {
        $completeExistingInstallation->recordIfReady();
        abort_if(Installation::isInstalled(), 404);

        return view('pages.install.index', [
            'checks' => $readiness->checks(),
            'ready' => $readiness->ready(),
            'emailConfigured' => $readiness->emailConfigured(),
            'appKeyAvailable' => filled(config('app.key')),
            'databaseSettings' => $configureDatabase->currentSettings(),
            'countries' => $readiness->countries(),
            'locales' => config('app.supported_locales', []),
        ]);
    }

    /**
     * Install the country and state reference data used by location fields.
     */
    public function setupWorldData(SeedWorldData $seedWorldData): RedirectResponse
    {
        abort_if(Installation::isInstalled(), 404);

        try {
            $seedWorldData->seed();
        } catch (\Throwable $exception) {
            Log::error('Installer world data setup failed.', [
                'exception' => $exception,
                'reason' => $exception->getMessage(),
            ]);

            return back()->withErrors([
                'world_data' => 'The country and state data could not be installed. Check the deployment logs and try again.',
            ]);
        }

        return back()->with('success', 'Countries and states are ready. Reloading the installer.');
    }

    /**
     * Generate the application key when the deployment does not have one.
     */
    public function generateKey(GenerateApplicationKey $generateApplicationKey): RedirectResponse
    {
        abort_if(Installation::isInstalled(), 404);

        try {
            $generateApplicationKey->generate();
        } catch (\Throwable $exception) {
            return back()->withErrors(['installer' => $exception->getMessage()]);
        }

        return back()->with('success', 'The application key was generated. Reloading the installer.');
    }

    /**
     * Test database settings without saving them.
     */
    public function testDatabase(
        InstallDatabaseRequest $request,
        ConfigureDatabase $configureDatabase,
    ): RedirectResponse {
        abort_if(Installation::isInstalled(), 404);

        try {
            $configureDatabase->test($request->validated());
        } catch (\Throwable $exception) {
            return back()
                ->withErrors(['database' => 'The database connection failed. Check the values and try again.'])
                ->withInput($request->except('password'));
        }

        return back()->with('success', 'The database connection works. You can now set up the database.');
    }

    /**
     * Save database settings and run the migrations after an explicit submit.
     */
    public function setupDatabase(
        InstallDatabaseRequest $request,
        ConfigureDatabase $configureDatabase,
    ): RedirectResponse {
        abort_if(Installation::isInstalled(), 404);

        if (!filled(config('app.key'))) {
            return back()->withErrors(['installer' => 'Generate the application key before setting up the database.']);
        }

        try {
            $configureDatabase->migrate($request->validated());
        } catch (\Throwable $exception) {
            return back()
                ->withErrors(['database' => 'The database setup failed. Check the values and deployment logs, then try again.'])
                ->withInput($request->except('password'));
        }

        return redirect()
            ->route('install.index')
            ->with('success', 'The database is ready. Complete the administrator and campus details below.');
    }

    /**
     * Create the first administrator, organization, and campus.
     */
    public function store(
        InstallApplicationRequest $request,
        InstallationReadiness $readiness,
        CompleteExistingInstallation $completeExistingInstallation,
        InstallApplication $installApplication,
    ): RedirectResponse {
        $completeExistingInstallation->recordIfReady();
        abort_if(Installation::isInstalled(), 404);

        if (!$readiness->ready()) {
            return back()
                ->withErrors(['installer' => 'Complete the installer checks before continuing.'])
                ->withInput($request->except(['admin_password', 'admin_password_confirmation']));
        }

        try {
            $installation = $installApplication->install($request->validated());
        } catch (\InvalidArgumentException $exception) {
            return back()
                ->withErrors(['installer' => $exception->getMessage()])
                ->withInput($request->except(['admin_password', 'admin_password_confirmation']));
        }

        session()->put('url.intended', route('schools.setup', [$installation->school_id, 'details']));

        return redirect()
            ->route('login')
            ->with('success', 'Skuul is installed. Sign in with the System Administrator account.');
    }
}
