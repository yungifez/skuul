import { Livewire } from "../../vendor/livewire/livewire/dist/livewire.esm";
import "../../vendor/yungifez/april-ui/dist/april.js";

const themeStorageKey = "theme";
const systemThemeQuery = window.matchMedia("(prefers-color-scheme: dark)");

function setTheme(theme) {
    const selectedTheme = ["light", "dark", "system"].includes(theme) ? theme : "system";
    const isDark = selectedTheme === "dark" || (selectedTheme === "system" && systemThemeQuery.matches);

    document.documentElement.classList.toggle("dark", isDark);
    document.documentElement.style.colorScheme = isDark ? "dark" : "light";
    window.localStorage.setItem(themeStorageKey, selectedTheme);
}

window.setTheme = setTheme;
setTheme(window.localStorage.getItem(themeStorageKey) ?? "system");

document.addEventListener("livewire:navigated", () => {
    setTheme(window.localStorage.getItem(themeStorageKey) ?? "system");
});

systemThemeQuery.addEventListener("change", () => {
    if (window.localStorage.getItem(themeStorageKey) === "system") {
        setTheme("system");
    }
});

Livewire.start();
