import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";

Alpine.plugin(collapse);

window.Alpine = Alpine;

/* ============================================================
   DARK MODE
   ============================================================ */

const setDarkMode = (enabled) => {
    document.documentElement.classList.toggle("dark", enabled);
    localStorage.setItem("darkMode", enabled);
};

setDarkMode(localStorage.getItem("darkMode") === "true");

window.setDarkMode = setDarkMode;
window.isDarkMode = () => document.documentElement.classList.contains("dark");

/* ============================================================
   TOAST STORE
   IMPORTANT: Register BEFORE Alpine.start()
   ============================================================ */

Alpine.store("toasts", {
    items: [],

    push(message, type = "info", duration = 4000) {
        const id = Date.now() + Math.random();

        this.items.push({
            id,
            message,
            type,
        });

        setTimeout(() => {
            this.remove(id);
        }, duration);
    },

    remove(id) {
        this.items = this.items.filter((toast) => toast.id !== id);
    },

    success(message) {
        this.push(message, "success");
    },

    error(message) {
        this.push(message, "error");
    },

    info(message) {
        this.push(message, "info");
    },

    warning(message) {
        this.push(message, "warning");
    },
});

/* ============================================================
   SIDEBAR
   ============================================================ */

Alpine.data("sidebarComponent", () => ({
    sidebarOpen: false,
    openSubmenus: [],
    currentUrl: window.location.pathname,

    menuItems: [
        {
            name: "Dashboard",
            href: "/dashboard",
            icon: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a2 2 0 002-2v-4a1 1 0 011-1h2a1 1 0 011 1v4a2 2 0 002 2m-6 0h6"/>
            </svg>`,
        },

        {
            name: "Analytics",
            href: "/analytics",
            badge: "New",
            icon: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>`,
        },

        {
            name: "Products",
            icon: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>`,

            children: [
                {
                    name: "All Products",
                    href: "/products",
                    icon: "•",
                },
                {
                    name: "Add Product",
                    href: "/products/create",
                    icon: "+",
                },
                {
                    name: "Categories",
                    href: "/categories",
                    icon: "#",
                },
            ],
        },

        {
            name: "Orders",
            icon: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>`,

            children: [
                {
                    name: "All Orders",
                    href: "/orders",
                    icon: "•",
                },
                {
                    name: "Pending",
                    href: "/orders/pending",
                    icon: "•",
                },
                {
                    name: "Completed",
                    href: "/orders/completed",
                    icon: "✓",
                },
            ],
        },

        {
            name: "Customers",
            href: "/customers",
            icon: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>`,
        },

        {
            name: "Settings",

            icon: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 010 3.35 1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31.826-2.37-2.37a1.724 1.724 0 001.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-1.756 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>`,

            children: [
                {
                    name: "General",
                    href: "/settings",
                    icon: "⚙",
                },
                {
                    name: "Profile",
                    href: "/settings/profile",
                    icon: "●",
                },
                {
                    name: "Security",
                    href: "/settings/security",
                    icon: "✓",
                },
            ],
        },
    ],

    init() {
        this.$watch("sidebarOpen", (value) => {
            document.body.style.overflow = value ? "hidden" : "";
        });

        this.menuItems.forEach((item) => {
            if (
                item.children &&
                item.children.some((child) => this.isCurrentUrl(child.href))
            ) {
                this.openSubmenus.push(item.name);
            }
        });
    },

    toggleSubmenu(name) {
        if (this.openSubmenus.includes(name)) {
            this.openSubmenus = this.openSubmenus.filter(
                (item) => item !== name,
            );
        } else {
            this.openSubmenus.push(name);
        }
    },

    isCurrentUrl(href) {
        if (!href || href === "#") {
            return false;
        }

        return (
            this.currentUrl === href || this.currentUrl.startsWith(href + "/")
        );
    },

    isActive(item) {
        if (item.children) {
            return item.children.some((child) => this.isCurrentUrl(child.href));
        }

        return this.isCurrentUrl(item.href);
    },

    closeOnMobile() {
        if (window.innerWidth < 1024) {
            this.sidebarOpen = false;
        }
    },
}));

/* ============================================================
   NOTIFICATIONS
   ============================================================ */

Alpine.data("notificationsComponent", () => ({
    open: false,
    unread: 0,
    loading: false,
    notifications: [],

    init() {
        this.fetch();

        setInterval(() => this.fetch(), 15000);

        document.addEventListener("click", (e) => {
            if (!this.$el.contains(e.target)) {
                this.open = false;
            }
        });
    },

    async fetch() {
        this.loading = true;

        try {
            const res = await fetch("/api/notifications", {
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": window.csrfToken,
                },
            });

            if (res.ok) {
                const data = await res.json();

                this.notifications = data.notifications || [];

                this.unread = this.notifications.filter(
                    (n) => !n.read_at,
                ).length;
            }
        } catch (error) {
            console.error("Notification error:", error);
        }

        this.loading = false;
    },

    async markAllRead() {
        try {
            await fetch("/api/notifications/mark-all-read", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": window.csrfToken,
                    Accept: "application/json",
                },
            });

            this.notifications = this.notifications.map((n) => ({
                ...n,
                read_at: new Date(),
            }));

            this.unread = 0;
        } catch (error) {
            console.error("Mark all read error:", error);
        }
    },

    async markRead(id) {
        try {
            await fetch(`/api/notifications/${id}/read`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": window.csrfToken,
                    Accept: "application/json",
                },
            });

            const notification = this.notifications.find((x) => x.id === id);

            if (notification) {
                notification.read_at = new Date();
            }

            this.unread = Math.max(0, this.unread - 1);
        } catch (error) {
            console.error("Mark read error:", error);
        }
    },

    timeAgo(iso) {
        const diff = (Date.now() - new Date(iso).getTime()) / 1000;

        if (diff < 60) return "just now";
        if (diff < 3600) return Math.floor(diff / 60) + "m ago";
        if (diff < 86400) return Math.floor(diff / 3600) + "h ago";

        return Math.floor(diff / 86400) + "d ago";
    },
}));

/* ============================================================
   LIVE SEARCH
   ============================================================ */

Alpine.data("liveSearchComponent", () => ({
    query: "",
    open: false,
    loading: false,

    results: {
        products: [],
        orders: [],
        customers: [],
    },

    init() {
        document.addEventListener("click", (e) => {
            if (!this.$el.contains(e.target)) {
                this.open = false;
            }
        });
    },

    async search() {
        if (this.query.length < 2) {
            this.results = {
                products: [],
                orders: [],
                customers: [],
            };

            this.open = false;
            return;
        }

        this.loading = true;
        this.open = true;

        try {
            const res = await fetch(
                `/api/search?q=${encodeURIComponent(this.query)}`,
                {
                    headers: {
                        Accept: "application/json",
                    },
                },
            );

            if (res.ok) {
                this.results = await res.json();
            }
        } catch (error) {
            console.error("Search error:", error);
        }

        this.loading = false;
    },

    isEmpty() {
        return (
            !this.results.products.length &&
            !this.results.orders.length &&
            !this.results.customers.length
        );
    },
}));

/* ============================================================
   ACTIVITY FEED
   ============================================================ */

Alpine.data("activityFeedComponent", () => ({
    items: [],
    loading: true,

    init() {
        this.fetch();

        setInterval(() => this.fetch(), 30000);
    },

    async fetch() {
        try {
            const res = await fetch("/api/activity", {
                headers: {
                    Accept: "application/json",
                },
            });

            if (res.ok) {
                const data = await res.json();
                this.items = data.items || [];
            }
        } catch (error) {
            console.error("Activity error:", error);
        }

        this.loading = false;
    },

    timeAgo(iso) {
        const diff = (Date.now() - new Date(iso).getTime()) / 1000;

        if (diff < 60) return "just now";
        if (diff < 3600) return Math.floor(diff / 60) + "m ago";
        if (diff < 86400) return Math.floor(diff / 3600) + "h ago";

        return Math.floor(diff / 86400) + "d ago";
    },
}));

/* ============================================================
   SPECS BUILDER (Create / Edit form)
   Handles: category-driven template, add/remove rows
   ============================================================ */
Alpine.data(
    "specsBuilder",
    ({ allSpecs = {}, categoryMap = {}, existing = {} } = {}) => ({
        allSpecs,
        categoryMap,

        sections: {},
        open: {},
        hasCategory: false,

        init() {
            // Load existing specifications on the edit page.
            if (existing && Object.keys(existing).length > 0) {
                this.sections = JSON.parse(JSON.stringify(existing));
                this.hasCategory = true;

                Object.keys(this.sections).forEach((section) => {
                    this.open[section] = false;
                });
            }

            // Listen for category changes.
            const categorySelect = document.querySelector(
                'select[name="category_id"]',
            );

            if (categorySelect) {
                categorySelect.addEventListener("change", () => {
                    this.loadForCategory(categorySelect.value);
                });

                // Load the selected category on initial page load.
                if (categorySelect.value && !this.hasCategory) {
                    this.loadForCategory(categorySelect.value);
                }
            }
        },

        loadForCategory(categoryId) {
            if (!categoryId) {
                this.sections = {};
                this.open = {};
                this.hasCategory = false;
                return;
            }

            const categoryKey = this.categoryMap[categoryId] || "default";
            const template =
                this.allSpecs[categoryKey] || this.allSpecs.default || {};

            const merged = {};

            Object.entries(template).forEach(([section, labels]) => {
                const existingRows = this.sections[section] || [];
                const rows = JSON.parse(JSON.stringify(existingRows));
                const existingLabels = new Set(rows.map((row) => row.label));

                (Array.isArray(labels) ? labels : []).forEach((label) => {
                    if (!existingLabels.has(label)) {
                        rows.push({ label: label, value: "" });
                    }
                });

                merged[section] = rows;
            });

            Object.keys(this.sections).forEach((section) => {
                if (!Object.prototype.hasOwnProperty.call(merged, section)) {
                    merged[section] = this.sections[section];
                }
            });

            this.sections = merged;
            this.open = {};

            Object.keys(this.sections).forEach((section) => {
                this.open[section] = false;
            });

            this.hasCategory = true;
        },

        toggle(section) {
            this.open[section] = !this.open[section];
        },

        addRow(section) {
            if (!this.sections[section]) {
                this.sections[section] = [];
            }

            this.sections[section].push({ label: "", value: "" });
            this.open[section] = true;
        },

        removeRow(section, index) {
            if (this.sections[section]) {
                this.sections[section].splice(index, 1);
            }
        },
    }),
);

/* ============================================================
   SPECS ACCORDION (Show page)
   Handles: independent open/close per section
   ============================================================ */
Alpine.data("specsAccordion", (initial = {}) => ({
    open: { ...initial },

    isOpen(key) {
        return !!this.open[key];
    },

    toggle(key) {
        this.open[key] = !this.open[key];
    },
}));

/* ============================================================
   RECENTLY VIEWED CAROUSEL
   ============================================================ */
Alpine.data("recentlyViewedCarousel", () => ({
    canScrollLeft: false,
    canScrollRight: true,

    init() {
        this.$nextTick(() => this.updateArrows());
        window.addEventListener("resize", () => this.updateArrows());
    },

    scroll(direction) {
        const track = this.$refs.track;
        if (!track) return;
        const amount = track.clientWidth * 0.75;
        track.scrollBy({
            left: direction === "left" ? -amount : amount,
            behavior: "smooth",
        });
    },

    updateArrows() {
        const track = this.$refs.track;
        if (!track) return;
        this.canScrollLeft = track.scrollLeft > 2;
        this.canScrollRight =
            track.scrollLeft + track.clientWidth < track.scrollWidth - 2;
    },
}));

/* ============================================================
   REVIEW FORM (Show page — write a review)
   ============================================================ */

document.addEventListener("alpine:init", () => {
    Alpine.data("reviewFormComponent", () => ({
        rating: 0,
        hoverRating: 0,
        body: "",
    }));
});

/* ============================================================
   ADD TO CART
   Uses the existing Alpine toast store
   ============================================================ */

Alpine.data("addToCart", ({ productId, inStock, addUrl }) => ({
    loading: false,
    inStock,

    async add() {
        if (this.loading || !this.inStock) return;

        this.loading = true;

        try {
            const data = await window.api(addUrl, {
                method: "POST",
                body: {
                    product_id: productId,
                    quantity: 1,
                },
            });

            // Use the existing Alpine toast store
            Alpine.store("toasts").success(data.message || "Added to cart");

            // Notify other components that the cart has changed
            window.dispatchEvent(
                new CustomEvent("cart:updated", {
                    detail: data,
                }),
            );
        } catch (e) {
            Alpine.store("toasts").error(e.message || "Could not add to cart");
        } finally {
            this.loading = false;
        }
    },
}));

window.api = async function (url, options = {}) {
    const response = await fetch(url, {
        method: options.method || "GET",
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
            ...(options.body
                ? {
                      "Content-Type": "application/json",
                      "X-CSRF-TOKEN":
                          document.querySelector('meta[name="csrf-token"]')
                              ?.content || "",
                  }
                : {}),
            ...(options.headers || {}),
        },
        ...(options.body ? { body: JSON.stringify(options.body) } : {}),
    });

    const data = await response.json();

    if (!response.ok) {
        throw new Error(data.message || "Request failed");
    }

    return data;
};

Alpine.data("cartBadge", ({ initial = 0, url }) => ({
    count: Number(initial) || 0,

    async init() {
        await this.refresh();
    },

    async refresh() {
        try {
            const data = await window.api(url, {
                method: "GET",
            });

            this.count = Number(data.count ?? this.count);
        } catch (error) {
            console.error("Could not refresh cart count:", error);
        }
    },
}));

Alpine.start();
