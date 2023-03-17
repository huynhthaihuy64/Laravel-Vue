<template>
  <section>
    <div style="margin-left: 400px">
      <div class="container">
        <div class="row">
          <div class="col-6">
            <Bar id="my-chart-id" :options="chartOptions" :data="chartData" class="my-chart" height="400px" />
          </div>
          <div class="col-6">
            <Bar id="my-chart-id" :options="chartOptions" :data="chartData1" class="my-chart" height="400px" />
          </div>
          <!-- <Bar id="my-chart-id" :options="chartOptions" :data="chartData" /> -->
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
      chartOptions: {
        responsive: false
      },
      chartData1: {
        labels: [],
        datasets: [{ data: [] }]
      },
      chartCustomer: [],
      chartRevenue: [],
      year: 2023,
    }
  },
  methods: {
    async getChartCustomer(year) {
      await httpRequest.get('/api/charts/customers/chart?' + year)
        .then((data) => {
          this.chartCustomer = data.data
          
          this.createdChart()
        })
    },
    async getChartRevenue(year) {
      await httpRequest.get('/api/charts/customers/chart-revenue?' + year)
        .then((data) => {
          this.chartRevenue = data.data
          console.log(this.chartRevenue);
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
      console.log(this.chartCustomer);
    },createdChartRevenue() {
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
      console.log(this.chartData1);
    }
  },
  mounted() {
    this.getChartCustomer()
    this.getChartRevenue()
  }
}
</script>
<style scoped>
.my-chart {
  width: 400px;
}
</style>