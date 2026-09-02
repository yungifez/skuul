@extends('layouts.guest')

@section('title', 'Install Skuul')

@section('body')
    <div class="min-h-screen bg-muted/30 px-4 py-10 sm:px-6">
        <div class="mx-auto w-full max-w-3xl">
            <div class="mb-8 text-center">
                <img src="{{ asset(config('app.logo')) }}" alt="{{ config('app.name') }} logo" class="mx-auto mb-4 h-16 w-16 rounded-2xl border bg-background object-cover shadow-lg">
                <p class="text-sm font-semibold uppercase text-muted-foreground">{{ config('app.name') }}</p>
                <h1 class="mt-3 text-3xl font-semibold tracking-tight">Install Skuul</h1>
                <p class="mx-auto mt-2 max-w-xl text-muted-foreground">Create the first System Administrator account, organization, and campus.</p>
            </div>

            <april:card class="border-border/70 bg-card/95 shadow-xl shadow-black/5 backdrop-blur" header-class="border-0">
                <slot:content class="space-y-8">
                    <section aria-labelledby="preflight-heading" class="space-y-4">
                        <div>
                            <h2 id="preflight-heading" class="text-lg font-semibold">Before you begin</h2>
                            <p class="mt-1 text-sm text-muted-foreground">These checks protect the first installation.</p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach ($checks as $check)
                                <div class="rounded-lg border border-border/70 p-4">
                                    <div class="flex items-center gap-2">
                                        @if ($check['passed'])
                                            <span class="text-emerald-600" aria-hidden="true">●</span>
                                            <span class="font-medium">{{ $check['label'] }}</span>
                                        @else
                                            <span class="text-destructive" aria-hidden="true">●</span>
                                            <span class="font-medium">{{ $check['label'] }}</span>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-sm text-muted-foreground">{{ $check['detail'] }}</p>
                                    @if ($check['action'])
                                        <p class="mt-2 text-xs font-medium text-amber-700 dark:text-amber-300">{{ $check['action'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="rounded-lg border border-border/70 bg-muted/30 p-4 text-sm">
                            <span class="font-medium">Email:</span>
                            @if ($emailConfigured)
                                <span class="text-muted-foreground">Email settings are present. You can review them later.</span>
                            @else
                                <span class="text-muted-foreground">Email is optional. Configure it later in your environment settings.</span>
                            @endif
                        </div>
                    </section>

                    @if ($errors->has('installer'))
                        <div class="rounded-lg border border-destructive/30 bg-destructive/10 p-4 text-sm text-destructive">
                            {{ $errors->first('installer') }}
                        </div>
                    @endif

                    @if ($errors->has('database'))
                        <div class="rounded-lg border border-destructive/30 bg-destructive/10 p-4 text-sm text-destructive">
                            {{ $errors->first('database') }}
                        </div>
                    @endif

                    @if ($errors->has('world_data'))
                        <div class="rounded-lg border border-destructive/30 bg-destructive/10 p-4 text-sm text-destructive">
                            {{ $errors->first('world_data') }}
                        </div>
                    @endif

                    @if (!$appKeyAvailable)
                        <section aria-labelledby="key-heading" class="space-y-4 border-t border-border/70 pt-8">
                            <div>
                                <h2 id="key-heading" class="text-lg font-semibold">Application key</h2>
                                <p class="mt-1 text-sm text-muted-foreground">Skuul needs an encryption key. This generates it once and never replaces an existing key.</p>
                            </div>
                            <form method="POST" action="{{ route('install.key') }}">
                                <button type="submit" class="rounded-md bg-primary px-4 py-2.5 text-sm font-semibold text-primary-foreground shadow-sm hover:bg-primary/90">
                                    Generate application key
                                </button>
                            </form>
                        </section>
                    @endif

                    @if (!$checks['database']['passed'] || !$checks['schema']['passed'])
                        <section aria-labelledby="database-heading" class="space-y-4 border-t border-border/70 pt-8">
                            <div>
                                <h2 id="database-heading" class="text-lg font-semibold">Database setup</h2>
                                <p class="mt-1 text-sm text-muted-foreground">Test the connection first. The setup button saves these values and runs pending migrations.</p>
                            </div>

                            <form method="POST" action="{{ route('install.database.test') }}" class="space-y-4">
                                @csrf
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="driver" class="mb-2 block text-sm font-medium">Database driver</label>
                                        <select id="driver" name="driver" required class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                            <option value="mysql" @selected(old('driver', $databaseSettings['driver']) === 'mysql')>MySQL / MariaDB</option>
                                            <option value="pgsql" @selected(old('driver', $databaseSettings['driver']) === 'pgsql')>PostgreSQL</option>
                                            <option value="sqlite" @selected(old('driver', $databaseSettings['driver']) === 'sqlite')>SQLite</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="database" class="mb-2 block text-sm font-medium">Database name or path</label>
                                        <input id="database" name="database" value="{{ old('database', $databaseSettings['database']) }}" required class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label for="host" class="mb-2 block text-sm font-medium">Host <span class="font-normal text-muted-foreground">(not used for SQLite)</span></label>
                                        <input id="host" name="host" value="{{ old('host', $databaseSettings['host']) }}" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" autocomplete="off">
                                    </div>
                                    <div>
                                        <label for="port" class="mb-2 block text-sm font-medium">Port</label>
                                        <input id="port" type="number" name="port" value="{{ old('port', $databaseSettings['port']) }}" min="1" max="65535" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" inputmode="numeric">
                                    </div>
                                    <div>
                                        <label for="username" class="mb-2 block text-sm font-medium">Username</label>
                                        <input id="username" name="username" value="{{ old('username', $databaseSettings['username']) }}" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" autocomplete="username">
                                    </div>
                                    <div>
                                        <label for="password" class="mb-2 block text-sm font-medium">Password</label>
                                        <input id="password" type="password" name="password" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" autocomplete="current-password">
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <button type="submit" class="rounded-md border border-input bg-background px-4 py-2.5 text-sm font-semibold hover:bg-muted">
                                        Test database connection
                                    </button>
                                    <button type="submit" formaction="{{ route('install.database.setup') }}" class="rounded-md bg-primary px-4 py-2.5 text-sm font-semibold text-primary-foreground shadow-sm hover:bg-primary/90">
                                        Set up database
                                    </button>
                                </div>
                            </form>
                        </section>
                    @endif

                    @if ($checks['schema']['passed'] && !$checks['world_data']['passed'])
                        <section aria-labelledby="world-data-heading" class="space-y-4 border-t border-border/70 pt-8">
                            <div>
                                <h2 id="world-data-heading" class="text-lg font-semibold">Country and state data</h2>
                                <p class="mt-1 text-sm text-muted-foreground">Skuul installs countries and states. Cities are loaded when a country is selected, so the installer does not need to import the full city catalog.</p>
                            </div>

                            <form method="POST" action="{{ route('install.world.setup') }}">
                                @csrf
                                <button type="submit" class="rounded-md bg-primary px-4 py-2.5 text-sm font-semibold text-primary-foreground shadow-sm hover:bg-primary/90">
                                    Install countries and states
                                </button>
                            </form>
                        </section>
                    @endif

                    @if ($ready)
                        <form method="POST" action="{{ route('install.store') }}" class="space-y-8">
                            @csrf

                            <section aria-labelledby="administrator-heading" class="space-y-4">
                                <div>
                                    <h2 id="administrator-heading" class="text-lg font-semibold">System Administrator</h2>
                                    <p class="mt-1 text-sm text-muted-foreground">This account is active immediately. Email verification is not required for the first account.</p>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <label for="admin_name" class="mb-2 block text-sm font-medium">Name</label>
                                        <input id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" autocomplete="name">
                                        @error('admin_name') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="admin_email" class="mb-2 block text-sm font-medium">Email</label>
                                        <input id="admin_email" type="email" name="admin_email" value="{{ old('admin_email') }}" required class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" autocomplete="email">
                                        @error('admin_email') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="locale" class="mb-2 block text-sm font-medium">System language <span class="font-normal text-muted-foreground">(optional)</span></label>
                                        <select id="locale" name="locale" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                            @foreach ($locales as $localeCode => $localeName)
                                                <option value="{{ $localeCode }}" @selected(old('locale', config('app.locale')) === $localeCode)>{{ $localeName }}</option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-sm text-muted-foreground">This sets the default language for the Skuul interface after installation. You can leave English selected.</p>
                                        @error('locale') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="admin_password" class="mb-2 block text-sm font-medium">Password</label>
                                        <input id="admin_password" type="password" name="admin_password" required class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" autocomplete="new-password">
                                        @error('admin_password') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="admin_password_confirmation" class="mb-2 block text-sm font-medium">Confirm password</label>
                                        <input id="admin_password_confirmation" type="password" name="admin_password_confirmation" required class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" autocomplete="new-password">
                                    </div>
                                </div>
                            </section>

                            <section aria-labelledby="organization-heading" class="space-y-4 border-t border-border/70 pt-8">
                                <div>
                                    <h2 id="organization-heading" class="text-lg font-semibold">Organization and campus</h2>
                                    <p class="mt-1 text-sm text-muted-foreground">You can add more campuses and details after signing in.</p>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="organization_name" class="mb-2 block text-sm font-medium">Organization name</label>
                                        <input id="organization_name" name="organization_name" value="{{ old('organization_name') }}" required class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                        @error('organization_name') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="campus_name" class="mb-2 block text-sm font-medium">First campus name</label>
                                        <input id="campus_name" name="campus_name" value="{{ old('campus_name') }}" required class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                        @error('campus_name') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="campus_initials" class="mb-2 block text-sm font-medium">Campus initials <span class="font-normal text-muted-foreground">(optional)</span></label>
                                        <input id="campus_initials" name="campus_initials" value="{{ old('campus_initials') }}" maxlength="10" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label for="campus_email" class="mb-2 block text-sm font-medium">Campus email <span class="font-normal text-muted-foreground">(optional)</span></label>
                                        <input id="campus_email" type="email" name="campus_email" value="{{ old('campus_email') }}" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <x-school-address-fields prefix="campus" :countries="$countries" />
                                    </div>
                                </div>
                            </section>

                            <section aria-labelledby="school-language-heading" class="space-y-4 border-t border-border/70 pt-8">
                                <div>
                                    <h2 id="school-language-heading" class="text-lg font-semibold">{{ __('School language') }}</h2>
                                    <p class="mt-1 text-sm text-muted-foreground">{{ __('Choose the familiar words your school uses for the academic year, classes, sections, class teachers, terms, subjects and fees. You can customize them later.') }}</p>
                                </div>

                                <div>
                                    <label for="school_language_preset" class="mb-2 block text-sm font-medium">{{ __('Starting language pattern') }}</label>
                                    <select id="school_language_preset" name="school_language_preset" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                        @foreach (\App\Models\SchoolOperatingProfile::presetOptions() as $value => $option)
                                            <option value="{{ $value }}" @selected(old('school_language_preset', \App\Models\SchoolOperatingProfile::DEFAULT_PRESET) === $value)>{{ $option['title'] }}{{ $value === \App\Models\SchoolOperatingProfile::DEFAULT_PRESET ? ' (default)' : '' }} — {{ $option['description'] }}</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-muted-foreground">Each choice provides the same seven labels. You can customize them later.</p>
                                    @error('school_language_preset') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                                </div>
                            </section>

                            <section class="space-y-4 border-t border-border/70 pt-8">
                                <label class="flex items-start gap-3 text-sm">
                                    <input type="checkbox" name="load_demo_data" value="1" @checked(old('load_demo_data')) class="mt-1 rounded border-input">
                                    <span>
                                        <span class="font-medium">Add a sample welcome notice</span>
                                        <span class="block text-muted-foreground">Adds one published notice for the installing administrator. It does not send an email.</span>
                                    </span>
                                </label>
                            </section>

                            <button type="submit" class="w-full rounded-md bg-primary px-4 py-2.5 text-sm font-semibold text-primary-foreground shadow-sm hover:bg-primary/90">
                                Install Skuul
                            </button>
                        </form>
                    @else
                        <div class="rounded-lg border border-amber-500/30 bg-amber-500/10 p-4 text-sm text-amber-800 dark:text-amber-200">
                            Run the checks above, then reload this page to continue.
                        </div>
                    @endif
                </slot:content>
            </april:card>
        </div>
    </div>
@endsection
