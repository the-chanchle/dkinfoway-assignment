<template>
  <div ref="table"></div>
</template>

<script>
import { TabulatorFull as Tabulator } from 'tabulator-tables';
import api from '../services/api';

export default {
  mounted() {
    this.table = new Tabulator(this.$refs.table, {
      ajaxURL: `${process.env.VUE_APP_API_URL}/stocks`,
      ajaxConfig: {
        method: "GET",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      },
      layout: "fitColumns",
      pagination: "remote",
      paginationSize: 10,
      paginationDataSent: {
        page: "page",
        size: "per_page",
      },
      paginationDataReceived: {
        last_page: "last_page",
        current_page: "page",
        total_records: "total",
      },

      ajaxResponse: function (url, params, response) {
        return response.data;
      },

      columns: [
        { title: "ID", field: "id" },
        { title: "Item Code", field: "item_code" },
        { title: "Item Name", field: "item_name" },
        { title: "Quantity", field: "quantity" },
        { title: "Location", field: "location" },
        { title: "Store", field: "store.name" },
        { title: "In-Stock Date", field: "in_stock_date" },
        {
          title: "Actions",
          formatter: () => '<button>Delete</button>',
          cellClick: (e, cell) =>
            this.deleteRecord(cell.getRow().getData().id),
        },
      ],
    });
  },
  methods: {
    async deleteRecord(id) {
      await api.delete(`/stocks/${id}`);
      this.table.replaceData();
    },
  },
};
</script>