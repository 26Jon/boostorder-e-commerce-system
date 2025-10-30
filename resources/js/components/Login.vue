<template>
    <div class="container mt-4">
        <div class="card shadow p-4">
            <h4 class="text-center mb-4">Login</h4>
            <form @submit.prevent="login">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input
                        type="email"
                        v-model="email"
                        class="form-control"
                        placeholder="Enter your email"
                        required
                    />
                </div>

                <div class="mb-3 position-relative">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            v-model="password"
                            class="form-control"
                            placeholder="Enter your password"
                            required
                        />
                        <button
                            class="btn btn-outline-secondary"
                            type="button"
                            @click="togglePassword"
                            tabindex="-1"
                        >
                            <i
                                :class="
                                    showPassword
                                        ? 'bi bi-eye-slash'
                                        : 'bi bi-eye'
                                "
                            ></i>
                        </button>
                    </div>
                </div>

                <button
                    class="btn btn-primary w-100"
                    type="submit"
                    :disabled="loading"
                >
                    {{ loading ? "Logging in..." : "Login" }}
                </button>

                <p v-if="errorMessage" class="text-danger text-center mt-3">
                    {{ errorMessage }}
                </p>
            </form>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            email: "",
            password: "",
            loading: false,
            errorMessage: "",
            showPassword: false,
        };
    },

    methods: {
        togglePassword() {
            this.showPassword = !this.showPassword;
        },
        async login() {
            this.loading = true;
            this.errorMessage = "";
            try {
                const response = await axios.post("/api/login", {
                    email: this.email,
                    password: this.password,
                });

                localStorage.setItem(
                    "user",
                    JSON.stringify(response.data.user)
                );

                this.$router.push("/catalogue");
            } catch (error) {
                this.errorMessage =
                    error.response?.data?.message ||
                    "Invalid email or password.";
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
