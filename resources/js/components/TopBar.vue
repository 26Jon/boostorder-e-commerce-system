<template>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div
            class="container-fluid d-flex justify-content-between align-items-center"
        >
            <div
                class="navbar-brand mb-0 h1 fs-4"
                @click="goHome"
                style="cursor: pointer"
            >
                MyShop
            </div>

            <div class="d-flex align-items position-relative">
                <div
                    class="position-relative me-4"
                    style="cursor: pointer"
                    @click="goCart"
                >
                    <i class="bi bi-cart fs-4"></i>
                    <span
                        v-if="cartCount > 0"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                    >
                        {{ cartCount }}
                    </span>
                </div>

                <div class="dropdown" v-if="user">
                    <i
                        class="bi bi-person dropdown-toggle fs-4"
                        style="cursor: pointer"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    ></i>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2">{{ user.name }}</li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <a
                                class="dropdown-item"
                                @click="goOrders"
                                style="cursor: pointer"
                            >
                                My Orders
                            </a>
                        </li>
                        <li>
                            <a
                                class="dropdown-item text-danger"
                                @click="logout"
                                style="cursor: pointer"
                            >
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
import axios from "axios";

export default {
    name: "TopBar",
    props: {
        cartCount: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            user: null,
        };
    },
    mounted() {
        this.loadUser();
    },
    methods: {
        loadUser() {
            const storedUser = localStorage.getItem("user");
            if (storedUser) {
                this.user = JSON.parse(storedUser);
            } else {
                console.warn("No user found in localStorage");
            }
        },

        async logout() {
            try {
                await axios.post("/api/logout");
                localStorage.removeItem("user");
                this.user = null;
                this.$router.push("/login");
            } catch (err) {
                console.error("Logout failed:", err);
            }
        },

        goHome() {
            this.$router.push("/catalogue");
        },
        goCart() {
            this.$router.push("/cart");
        },
        goOrders() {
            this.$router.push("/orders");
        },
        goLogin() {
            this.$router.push("/login");
        },
    },
};
</script>
