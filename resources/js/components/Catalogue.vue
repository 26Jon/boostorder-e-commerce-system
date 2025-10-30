<template>
    <div>
        <TopBar :cartCount="cartQuantity" />

        <div class="container mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>Product Catalogue</h3>
                <input
                    type="text"
                    v-model="searchQuery"
                    class="form-control w-25"
                    placeholder="Search products"
                />
            </div>
            <div class="row">
                <div
                    class="col-md-4 mb-4"
                    v-for="product in filteredProducts"
                    :key="product.id"
                >
                    <div class="card h-100">
                        <img
                            :src="product.image"
                            class="card-img-top"
                            :alt="product.name"
                        />
                        <div class="card-body text-center">
                            <h6 class="fw-bold">{{ product.name }}</h6>
                            <p>{{ product.description }}</p>
                            <p>RM {{ product.price }}</p>
                            <button
                                class="btn btn-primary"
                                @click="addToCart(product.id)"
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import TopBar from "../components/TopBar.vue";

export default {
    components: { TopBar },
    data() {
        return {
            products: [],
            cartItems: [],
            searchQuery: "",
            user: null,
        };
    },
    computed: {
        filteredProducts() {
            if (!this.searchQuery) {
                return this.products;
            }
            const query = this.searchQuery.toLowerCase();
            return this.products.filter(
                (product) =>
                    product.name.toLowerCase().includes(query) ||
                    product.description.toLowerCase().includes(query)
            );
        },
        cartQuantity() {
            return this.cartItems.reduce(
                (total, item) => total + item.quantity,
                0
            );
        },
    },
    async mounted() {
        this.loadUser();
        await this.fetchProducts();
        await this.fetchCart();
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
        async fetchProducts() {
            try {
                const response = await fetch("/api/products");
                if (!response.ok) {
                    throw new Error("Failed to fetch products");
                }
                this.products = await response.json();
            } catch (error) {
                console.error("There was a problem fetching products:", error);
            }
        },
        async fetchCart() {
            try {
                const response = await axios.get(
                    `/api/cart/user/${this.user.id}`
                );
                this.cartItems = response.data.items || [];
                console.log(this.cartItems);
            } catch (error) {
                console.error(
                    "There was a problem fetching cart items:",
                    error
                );
            }
        },
        async addToCart(productId) {
            try {
                const response = await fetch("/api/cart/add", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        user_id: this.user.id,
                        product_id: productId,
                        quantity: 1,
                    }),
                });
                if (!response.ok) {
                    throw new Error("Failed to add to cart");
                }
                await this.fetchCart();
            } catch (error) {
                console.error("There was a problem adding to cart:", error);
            }
        },
    },
};
</script>
