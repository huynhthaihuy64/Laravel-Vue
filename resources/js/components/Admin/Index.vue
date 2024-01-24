<template>
  <section>
    <div style="margin-left: 400px">
      <div class="container">
        <div class="row">
          <div class="col-6">
            <a-select :default-value="year">
          <i slot="suffixIcon" class="fas fa-sort-down dropdown-icon"></i>
          <a-select-option v-for="(data, index) in years" :value="data.year" :key="`data_${index}`"
            @click="getChartCustomer(data.year)">
            {{ data.year }}
          </a-select-option>
        </a-select>
            <Bar id="my-chart-id" :options="chartOptions" :data="chartData" class="my-chart" height="400px" />
          </div>
          <div class="col-6">
            <a-select :default-value="year">
          <i slot="suffixIcon" class="fas fa-sort-down dropdown-icon"></i>
          <a-select-option v-for="(data, index) in years" :value="data.year" :key="`data_${index}`"
            @click="getChartRevenue(data.year)">
            {{ data.year }}
          </a-select-option>
        </a-select>
            <Bar id="my-chart-id" :options="chartOptions" :data="chartData1" class="my-chart" height="400px" />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import httpRequest from '../../axios'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)
export default {
  name: 'BarChart',
  components: { Bar },
  data() {
    return {
      chartData: {
        labels: [],
        datasets: [{ data: [] }]
      },
      year: new Date().getFullYear(),
      chartOptions: {
        responsive: false
      },
      chartData1: {
        labels: [],
        datasets: [{ data: [] }]
      },
      chartCustomer: [],
      chartRevenue: [],
      years: {},
    }
  },
  methods: {
    async getChartCustomer(year = new Date().getFullYear()) {
      await httpRequest.get('/api/charts/customers/chart?year=' + year)
        .then((data) => {
          this.chartCustomer = data.data
          
          this.createdChart()
        })
    },
    async getChartRevenue(year = new Date().getFullYear()) {
      await httpRequest.get('/api/charts/customers/chart-revenue?year=' + year)
        .then((data) => {
          this.chartRevenue = data.data
          this.createdChartRevenue()
        })
    },
    createdChart() {
      const labels = ["January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"];
      const values = this.chartCustomer;
      this.chartData = {
        labels,
        datasets: [{
          label: "Customer of year",
          data: values,
          backgroundColor: "#f87979",
          borderWidth: 1,
        }]
      }
    },
    createdChartRevenue() {
      const labels = ["January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"];
      const values = this.chartRevenue;
      this.chartData1 = {
        labels,
        datasets: [{
          label: "Revenue of year",
          data: values,
          backgroundColor: "#1E90FF",
          borderWidth: 1,
        }]
      }
    },
    async getYear() {
      await httpRequest.get('/api/charts/customers/years')
      .then((data) => {
          this.years = data.data
        })
    }
  },
  mounted() {
    this.getChartCustomer()
    this.getChartRevenue()
    this.getYear()
  }
}
</script>
<style scoped>
.my-chart {
  width: 400px;
}
</style>