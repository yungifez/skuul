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

window.commandPalette = function commandPalette(items) {
    return {
        items,
        open: false,
        query: "",
        selectedIndex: 0,

        init() {
            this.$watch("query", () => {
                this.selectedIndex = 0;
            });
        },

        get filteredItems() {
            const query = this.query.trim().toLocaleLowerCase();

            if (query === "") {
                return this.items;
            }

            return this.items.filter((item) => item.keywords.toLocaleLowerCase().includes(query));
        },

        openPalette() {
            this.open = true;
            this.query = "";
            this.selectedIndex = 0;
            this.$nextTick(() => this.$refs.searchInput?.focus());
        },

        closePalette() {
            this.open = false;
            this.query = "";
            this.selectedIndex = 0;
        },

        handleKeydown(event) {
            if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === "k") {
                event.preventDefault();
                this.open ? this.closePalette() : this.openPalette();

                return;
            }

            if (!this.open) {
                return;
            }

            if (event.key === "Escape") {
                event.preventDefault();
                this.closePalette();

                return;
            }

            if (event.key === "ArrowDown") {
                event.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, this.filteredItems.length - 1);

                return;
            }

            if (event.key === "ArrowUp") {
                event.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, 0);

                return;
            }

            if (event.key === "Enter" && this.filteredItems[this.selectedIndex]) {
                event.preventDefault();
                const url = this.filteredItems[this.selectedIndex].url;

                if (window.Livewire?.navigate) {
                    window.Livewire.navigate(url);
                } else {
                    window.location.assign(url);
                }

                this.closePalette();
            }
        },
    };
};

window.locationFields = function locationFields(configuration) {
    return {
        country: configuration.country ?? "",
        state: configuration.state ?? "",
        city: configuration.city ?? "",
        states: [],
        cities: [],
        loading: false,
        requestId: 0,

        async loadCountry(preserveSelection = true) {
            const requestId = ++this.requestId;
            const selectedState = preserveSelection ? this.state : "";
            const selectedCity = preserveSelection ? this.city : "";

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
                this.city = selectedCity;
            } finally {
                if (requestId === this.requestId) {
                    this.loading = false;
                }
            }
        },
    };
};

window.boardingRooms = function boardingRooms(rooms) {
    return {
        rooms,
        roomModalOpen: false,
        selectedRoomId: null,
        editingRoom: false,
        editingBedId: null,
        leavingBedId: null,

        get selectedRoom() {
            return this.rooms.find((room) => room.id === this.selectedRoomId) ?? null;
        },

        openRoom(roomId) {
            this.selectedRoomId = roomId;
            this.editingRoom = false;
            this.editingBedId = null;
            this.leavingBedId = null;
            this.roomModalOpen = true;
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

    if (form.dataset.submitting === "true") {
        event.preventDefault();

        return;
    }

    const deleteMethod = form.querySelector('input[name="_method"][value="DELETE" i]');

    if (deleteMethod) {
        const message = form.dataset.confirm ?? "Delete this item? This action cannot be undone.";

        if (!window.confirm(message)) {
            event.preventDefault();

            return;
        }
    }

    form.dataset.submitting = "true";

    form.querySelectorAll('button:not([type]), button[type="submit"], input[type="submit"]').forEach((submitButton) => {
        submitButton.disabled = true;
        submitButton.setAttribute("aria-busy", "true");
    });
}, true);

systemThemeQuery.addEventListener("change", () => {
    if (window.localStorage.getItem(themeStorageKey) === "system") {
        setTheme("system");
    }
});

Livewire.start();
