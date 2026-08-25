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

window.locationFields = function locationFields(configuration) {
    return {
        country: configuration.country ?? "",
        state: configuration.state ?? "",
        city: configuration.city ?? "",
        states: [],
        cities: [],
        loading: false,
        requestId: 0,

        async loadCountry() {
            const requestId = ++this.requestId;
            const selectedState = this.state;
            const selectedCity = this.city;

            this.states = [];
            this.cities = [];
            this.state = "";
            this.city = "";

            if (!this.country) {
                return;
            }

            this.loading = true;

            try {
                const country = encodeURIComponent(this.country);
                const [statesResponse, citiesResponse] = await Promise.all([
                    fetch(configuration.statesUrl + "?country=" + country, {
                        headers: { Accept: "application/json" },
                    }),
                    fetch(configuration.citiesUrl + "?country=" + country, {
                        headers: { Accept: "application/json" },
                    }),
                ]);

                if (requestId !== this.requestId) {
                    return;
                }

                this.states = statesResponse.ok ? await statesResponse.json() : [];
                this.cities = citiesResponse.ok ? await citiesResponse.json() : [];
                this.state = this.states.includes(selectedState) ? selectedState : "";
                this.city = this.cities.includes(selectedCity) ? selectedCity : "";
            } finally {
                if (requestId === this.requestId) {
                    this.loading = false;
                }
            }
        },
    };
};

document.addEventListener("livewire:navigated", () => {
    setTheme(window.localStorage.getItem(themeStorageKey) ?? "system");
});

document.addEventListener("submit", (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    const deleteMethod = form.querySelector('input[name="_method"][value="DELETE" i]');

    if (!deleteMethod) {
        return;
    }

    const message = form.dataset.confirm ?? "Delete this item? This action cannot be undone.";

    if (!window.confirm(message)) {
        event.preventDefault();
    }
});

systemThemeQuery.addEventListener("change", () => {
    if (window.localStorage.getItem(themeStorageKey) === "system") {
        setTheme("system");
    }
});

Livewire.start();
