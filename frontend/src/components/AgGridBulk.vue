<template>
  <div style="height: 400px;" class="ag-theme-alpine">
    <ag-grid-vue
      class="ag-theme-alpine"
      style="width: 100%; height: 100%;"
      :columnDefs="columnDefs"
      :rowData="rowData"
      :defaultColDef="defaultColDef"
      rowSelection="multiple"
      @grid-ready="onGridReady"
    />
    <div style="margin-top: 10px;">
      <button @click="addRow">Add New Record</button>
      <button @click="saveAll">Save All</button>
    </div>
  </div>
</template>

<script>
import { AgGridVue } from "ag-grid-vue3";
import api from "../services/api";
import { ModuleRegistry, AllCommunityModule } from "ag-grid-community";

ModuleRegistry.registerModules([AllCommunityModule]);

export default {
  name: "AgGridBulk",
  components: { AgGridVue },
  data() {
    return {
      gridApi: null,
      stores: [],
      columnDefs: [
        { headerName: "Stock No", field: "stock_no", editable: false },
        { headerName: "Item Code", field: "item_code" },
        { headerName: "Item Name", field: "item_name" },
        { headerName: "Quantity", field: "quantity" },
        { headerName: "Location", field: "location" },
        {
          headerName: "Store Name",
          field: "store_id",
          editable: true,
          cellEditor: "agSelectCellEditor",
          cellEditorParams: () => {
            return {
              values: this.stores.map((s) => s.id),
            };
          },
          valueFormatter: (params) => {
            const store = this.stores.find((s) => s.id === params.value);
            return store ? store.name : "";
          },
        },
        {
          headerName: "In-Stock Date",
          field: "in_stock_date",
          editable: true,
          cellEditor: DatePicker,
        },
      ],
      defaultColDef: {
        editable: true,
        resizable: true,
        sortable: true,
        filter: true,
      },
      rowData: [],
      nextId: 1,
    };
  },
  async mounted() {
    try {
      const res = await api.get("/stores");
      this.stores = res.data;
    } catch (err) {
      console.error("Failed to load stores:", err);
    }
  },
  methods: {
    onGridReady(params) {
      this.gridApi = params.api;
    },
    addRow() {
      if (!this.gridApi) return;
      this.gridApi.applyTransaction({
        add: [{ stock_no: this.nextId++ }],
      });
    },
    async saveAll() {
      if (!this.gridApi) return;

      this.gridApi.stopEditing();

      const rowData = [];
      this.gridApi.forEachNode(node => rowData.push(node.data));

      if (rowData.length === 0) {
        alert("No records to save");
        return;
      }

      try {
        await api.post("/stocks/bulk", { items: rowData });
        alert("Saved successfully");
        this.rowData = [];
        this.nextId = 1;
      } catch (err) {
        if (err.response && err.response.status === 422) {
          const errors = err.response.data.errors;
          let messages = [];
          for (const field in errors) {
            messages.push(`${field}: ${errors[field].join(", ")}`);
          }
          alert("Validation failed:\n" + messages.join("\n"));
        } else {
          console.error("Save failed:", err);
          alert("Save failed, please check console");
        }
      }
    },
  },
};

const DatePicker = function () {};

DatePicker.prototype.init = function (params) {
  this.eInput = document.createElement("input");
  this.eInput.type = "date";
  this.eInput.classList.add("ag-input");
  this.eInput.style.height = "100%";
  if (params.value) {
    this.eInput.value = params.value;
  }
};

DatePicker.prototype.getGui = function () {
  return this.eInput;
};

DatePicker.prototype.afterGuiAttached = function () {
  this.eInput.focus();
  this.eInput.select();
};

DatePicker.prototype.getValue = function () {
  return this.eInput.value;
};

DatePicker.prototype.destroy = function () {};
DatePicker.prototype.isPopup = function () {
  return false;
};
</script>
