{{--
    Sets the theme before the browser paints.

    resources/js/app.js is a module, so the browser defers it until after the
    page is parsed and painted. That paint uses the light palette, which shows
    as a white flash on every load for people who chose the dark theme. This
    script is inline and blocking, so it runs first and the page paints once.
    Keep the storage key and the rules here the same as setTheme() in app.js.
--}}
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
