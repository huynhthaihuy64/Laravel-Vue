<template>
    <div class="container">
        <h1>Purchase History</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in history" :key="item.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{item.product.name}}</td>
                    <td>{{ item.total_product }}</td>
                    <td>{{ formatPrice(item) }}</td>
                    <td>{{ formatTotalPrice(item) }}</td>
                </tr>
                <!-- Add more rows as needed -->
            </tbody>
        </table>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import httpRequest from '../../../axios'

export default {
    name: "History",
    setup() {
        const history = ref([]);
        const getHistory = () => {
            httpRequest.get('/api/customers/purchase-history')
                .then((data) => {
                    history.value = data.data;
                }).catch(error => {
                    console.error('Error fetching chat history:', error);
                });
        }
        onMounted(getHistory);
        return { history }
    },
    methods: {
        formatPrice(item) {
            let totalPrice = item.price_currency != null ? parseFloat(item.price_currency.replace(/,/g, '')) : item.price;
            let formattedPrice = totalPrice % 1 === 0 ?
                this.$options.filters.formatNumber(totalPrice) :
                this.$options.filters.formatNumber(totalPrice, 4);
            return formattedPrice;
        },
        formatTotalPrice(item) {
            let totalPrice = item.total_currency != null ?
                parseFloat(item.total_currency.toString().replace(/,/g, '')) :
                0;

            let formattedPrice = totalPrice % 1 === 0 ?
                this.$options.filters.formatNumber(totalPrice) :
                this.$options.filters.formatNumber(totalPrice, 4);
            return formattedPrice;
        }
    }
}
</script>

<style scoped>
h1 {
    text-align: center;
    margin-bottom: 30px;
}

table {
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

th,
td {
    text-align: center;
}

th {
    background-color: #007bff;
    color: #fff;
}

tbody tr:nth-child(odd) {
    background-color: #f2f2f2;
}
</style>