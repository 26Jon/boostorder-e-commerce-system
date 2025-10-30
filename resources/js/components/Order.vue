<template>
    <TopBar :cartCount="cartQuantity" />
    <div class="container mt-4">
        <h2>My Orders</h2>
        <div v-if="orders.length === 0" class="alert alert-info">
            You have no orders yet.
        </div>

        <div v-else>
            <div
                class="card p-4 mb-3"
                v-for="(order, index) in orders"
                :key="order.id"
            >
                <div
                    class="d-flex justify-content-between align-items-center mb-3"
                >
                    <div>
                        <h5 class="fw-bold">Order #{{ index + 1 }}</h5>
                        <p class="mb-0">
                            Status:
                            <span
                                :class="{
                                    'text-success':
                                        order.status === 'Completed',
                                    'text-warning':
                                        order.status === 'Processing',
                                }"
                            >
                                {{ order.status }}
                            </span>
                        </p>
                        <p class="mb-0">Total: RM {{ order.total_price }}</p>
                    </div>

                    <div>
                        <select
                            v-model="order.newStatus"
                            class="form-select form-select-sm d-inline-block w-auto me-2"
                        >
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                        </select>
                        <button
                            class="btn btn-primary btn-sm"
                            @click="updateStatus(order)"
                        >
                            Update
                        </button>
                    </div>
                </div>

                <hr />

                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 50%">Product</th>
                            <th style="width: 15%">Price (RM)</th>
                            <th style="width: 15%">Quantity</th>
                            <th style="width: 20%">Subtotal (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in order.order_items" :key="item.id">
                            <td class="d-flex align-items-center">
                                <span>{{ item.product.name }}</span>
                            </td>
                            <td>{{ item.product.price }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>
                                {{ item.subtotal.toFixed(2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
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
            orders: [],
            cartItems: [],
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
        this.fetchOrders();
        this.fetchCartItems();
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
        async fetchOrders() {
            try {
                const response = await axios.get(
                    `/api/order/user/${this.user.id}`
                );
                this.orders = response.data.map((order) => ({
                    ...order,
                    newStatus: order.status,
                }));
            } catch (error) {
                console.error("Error fetching orders:", error);
            }
        },

        async fetchCartItems() {
            try {
                const response = await axios.get(
                    `/api/cart/user/${this.user.id}`
                );
                this.cartItems = response.data.items;
            } catch (error) {
                console.error("There was a problem fetching cart data:", error);
            }
        },

        async updateStatus(order) {
            try {
                const response = await axios.put(
                    `/api/order/${order.id}/status`,
                    {
                        status: order.newStatus,
                    }
                );

                alert(response.data.message);
                this.fetchOrders();
            } catch (error) {
                console.error("Error updating order status:", error);
                alert("Failed to update order status.");
            }
        },
    },
};
</script>
