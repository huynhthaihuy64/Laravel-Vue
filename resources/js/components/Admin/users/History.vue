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
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in history" :key="item.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{item.product.name}}</td>
                    <td>{{ item.qty }}</td>
                    <td>{{ item.price_currency != null ? item.price_currency : item.price }}</td>
                    <td>{{ item.price_currency != null ? (item.price_currency * item.qty) : (item.price * item.qty) }}
                    </td>
                    <td>{{ item.created_at }}</td>
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