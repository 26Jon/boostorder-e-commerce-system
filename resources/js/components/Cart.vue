<template>
    <TopBar :cartCount="cartQuantity" />
    <div class="container mt-4">
        <h2>My Cart</h2>
        <div v-if="cartItems.length === 0" class="alert alert-info">
            Your cart is empty.
        </div>
        <div v-else>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price (RM)</th>
                        <th>Quantity</th>
                        <th>Subtotal (RM)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in cartItems" :key="item.id">
                        <td>{{ item.product_name }}</td>
                        <td>RM {{ item.product_price }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <button
                                    class="btn btn-sm btn-outline-secondary"
                                    @click="
                                        updateQuantity(item, item.quantity - 1)
                                    "
                                >
                                    -
                                </button>
                                <span class="mx-2">{{ item.quantity }}</span>
                                <button
                                    class="btn btn-sm btn-outline-secondary"
                                    @click="
                                        updateQuantity(item, item.quantity + 1)
                                    "
                                >
                                    +
                                </button>
                            </div>
                        </td>
                        <td>RM {{ item.subtotal.toFixed(2) }}</td>
                        <td>
                            <button
                                class="btn btn-danger btn-sm"
                                @click="removeFromCart(item)"
                            >
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <h5 class="me-3">Total: RM {{ totalPrice.toFixed(2) }}</h5>
                <button class="btn btn-success" @click="checkout">
                    Place Order
                </button>
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
            user: null,
            cartItems: [],
            totalPrice: 0,
        };
    },
    computed: {
        cartQuantity() {
            return this.cartItems.reduce(
                (total, item) => total + item.quantity,
                0
            );
        },
    },
    mounted() {
        this.loadUser();
        this.fetchCart();
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
        async fetchCart() {
            try {
                const response = await axios.get(
                    `/api/cart/user/${this.user.id}`
                );
                const data = response.data;
                this.cartItems = data.items;
                this.totalPrice = data.total_price;
            } catch (error) {
                console.error("There was a problem fetching cart data:", error);
            }
        },

        async updateQuantity(item, newQuantity) {
            if (
                newQuantity <= 0 ||
                (item.quantity == 1 && newQuantity < item.quantity)
            ) {
                const confirmDelete = confirm(
                    `Remove ${item.product_name} from cart?`
                );
                if (confirmDelete) {
                    await this.removeFromCart(item, true);
                }
                return;
            }

            try {
                await axios.put(`/api/user/${this.user.id}/cart/${item.id}`, {
                    quantity: newQuantity,
                });
                this.fetchCart();
            } catch (error) {
                console.error("There was a problem updating quantity:", error);
            }
        },

        async removeFromCart(item, skipConfirm = false) {
            if (
                !skipConfirm &&
                !confirm(`Remove ${item.product_name} from cart?`)
            ) {
                return;
            }
            try {
                await axios.delete(`/api/user/${this.user.id}/cart/${item.id}`);
                this.fetchCart();
            } catch (error) {
                console.error("There was a problem removing the item:", error);
            }
        },

        async checkout() {
            if (!confirm("Are you sure you want to place this order?")) return;

            try {
                const response = await axios.post(`/api/cart/checkout`, {
                    user_id: this.user.id,
                });
                alert(response.data.message);
                this.fetchCart();
            } catch (error) {
                alert("Checkout failed. Please try again.");
                console.error("There was a problem during checkout:", error);
            }
        },
    },
};
</script>
