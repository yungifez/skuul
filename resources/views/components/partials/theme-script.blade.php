{{-- Apply the theme before paint to prevent a light flash in dark mode.
    Keep the storage key and theme rules aligned with setTheme() in resources/js/app.js. --}}
<script>
    (function () {
        var stored = null;

        try {
            stored = window.localStorage.getItem('theme');
        } catch (error) {
            stored = null;
        }

        var theme = ['light', 'dark', 'system'].indexOf(stored) !== -1 ? stored : 'system';
        var isDark = theme === 'dark'
            || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

        document.documentElement.classList.toggle('dark', isDark);
        document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
    })();
</script>
