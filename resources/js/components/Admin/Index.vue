<template>
  <section>
    <div style="margin-left: 400px">
      <div class="container">
        <div class="row">
          <div class="col-6">
            <Bar id="my-chart-id" :options="chartOptions" :data="chartData" class="my-chart" height="400px" />
          </div>
          <div class="col-6">

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
      chartCustomer: [],
      year: 2023,
    }
  },
  methods: {
    async getChartCustomer(year) {
      await httpRequest.get('/api/customers/chart?' + year)
        .then((data) => {
          this.chartCustomer = data.data
          this.createdChart()
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
      console.log(this.chartData);
    }
  },
  mounted() {
    this.getChartCustomer()
  }
}
</script>
<style scoped>
.my-chart {
  width: 400px;
}
</style>