<template>
  <div class="container-fluid category-content">
    <h1 class="title">List Product</h1>
    <div class="content-body">
      <a-row>
        <a-col :span="12">
          <a-button type="primary" @click="showAddUser()" class="button-type">
            <a-icon type="plus" />
            Add Product
          </a-button></a-col>
        <a-col :span="5" class="d-flex justify-content-end mr-2">
          <a-upload :beforeUpload="beforeUpload" :headers="headers" action="//jsonplaceholder.typicode.com/posts/">
            <a-button class="button-type" @click="importProduct">
              <font-awesome-icon :icon="['fas', 'file-excel']" class="mr-2" />
              Import Excel
            </a-button>
          </a-upload>
        </a-col>
        <a-col :span="3">
          <a-button class="button-type" @click="exportProduct">
            <font-awesome-icon :icon="['fas', 'file-excel']" class="mr-2" />
            Export Excel
          </a-button></a-col>
        <a-col :span="2"></a-col>
      </a-row>
      <table class="table">
        <thead>
          <tr>
            <th scope="col" class="category-order center-text">No</th>
            <th class="sortType center-text"><a @click="toggleSort('name')">Name <i class="fas fa-sort"></i></a></th>
            <th class="center-text">Description</th>
            <th class="center-text">Category</th>
            <th class="sortType center-text"><a @click="toggleSort('price')">Price <i class="fas fa-sort"></i></a></th>
            <th class="sortType center-text"><a @click="toggleSort('price_sale')">Price_Sale <i
                  class="fas fa-sort"></i></a></th>
            <th class="sortType center-text"><a @click="toggleSort('active')">Status <i class="fas fa-sort"></i></a>
            </th>
            <th class="center-text">Image</th>
            <th class="sortType center-text"><a @click="toggleSort('created_at')">Created <i
                  class="fas fa-sort"></i></a></th>
            <th class="center-text">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in products" :key="item.id">
            <th scope="row" class="center-text">{{ index + 1 }}</th>
            <td class="center-text">{{ item.name }}</td>
            <td class="center-text">{{ item.description !== null ? item.description.length > 30 ?
              item.description.slice(0, 30) + '...' :
              item.description : '' }}</td>
            <td class="center-text">{{ item.menuName }}</td>
            <td class="center-text">{{ item.price | formatNumber }}</td>
            <td class="center-text">{{ item.price_sale | formatNumber }}</td>
            <td class="center-text" v-if="item.active == 0"><span class="btn btn-danger btn-xs">InActive</span></td>
            <td class="center-text" v-if="item.active == 1"><span class="btn btn-success btn-xs">Active</span></td>
            <td class="center-text"><img class="file image-contain" :src="`${item.file}`" /></td>
            <td class="center-text">{{ item.created_at }}</td>
            <td class="center-text">
              <a @click="showModalEdit(item.id)">
                <IconEdit class="icon-edit" />
              </a><a @click="showModalDelete(item.id)">
                <IconDelete class="icon-delete" />
              </a>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="pagination-wrapper">
        <span>Rows per page:</span>
        <a-select default-value="10">
          <i slot="suffixIcon" class="fas fa-sort-down dropdown-icon"></i>
          <a-select-option v-for="(pageSize, index) in pageSizeList" :value="pageSize" :key="`pageSize_${index}`"
            @click="rowPerPage(pageSize)">
            {{ pageSize }}
          </a-select-option>
        </a-select>
        <span class="total-page">{{ this.meta.from }}-{{ this.meta.to }} of {{ totalPage }}</span>
        <a-button class="pagination-btn" @click="handlePrevPage">
          <font-awesome-icon :icon="['fas', 'chevron-left']" />
        </a-button>
        <a-button class="pagination-btn" @click="handleNextPage">
          <font-awesome-icon :icon="['fas', 'chevron-right']" />
        </a-button>
      </div>

    </div>
    <template>
      <a-modal title="Add new product" :visible="this.flagModalAdd" @cancel="() => cancelModalAdd()">
        <template #footer>
          <a-button class="btn-button-cancel" @click="cancelModalAdd">Cancel</a-button>
          <a-button key="submit" type="primary" class="btn-button primary" @click="addProduct">Add</a-button>
        </template>
        <a-form :form="form">
          <a-row :gutter="20">
            <a-col :span="12">
              <a-form-item label="Name">
                <a-input v-decorator="[
          'name',
          {
            initialValue: '',
            rules: [{ required: true, message: 'Please input your name!' }],
          },
        ]" />
              </a-form-item>
              <a-form-item label="Price">
                <a-input v-decorator="['price',
          {
            initialValue: '',
          }
        ]" />
              </a-form-item>
            </a-col>
            <a-col :span="12">
              <a-form-item label="Menu">
                <a-select v-decorator="[
          'menu_id',
          {
            rules: [
              {
                required: true,
                message: 'Menu is not null',
              },
            ],
          },
        ]" mode="multiple" placeholder="Choose Menu" labelInValue optionFilterProp="filterProps" showSearch>
                  <a-select-option v-for="(menu) in menus" :value="menu.id" :key="menu.id" :filterProps="menu.name">
                    {{ menu.name }}
                  </a-select-option>
                </a-select>
              </a-form-item>
              <a-form-item label="Price_Sale">
                <a-input v-decorator="['price_sale',
          {
            initialValue: '',
          }
        ]" />
              </a-form-item>
            </a-col>
          </a-row>
          <a-form-item label="Description">
            <textarea v-decorator="[
          'description',
          {
            initialValue: '',
            rules: [{ required: true, message: 'Please input your description!' }],
          },
        ]" type="text" class="textarea"></textarea>
          </a-form-item>
          <a-form-item label="Content"></a-form-item>
          <div :class="{ error: contentIsError, content: true }">
            <VueEditor v-model="content" :placeholder="placeholderContent" @text-change="handleChangeContent"
              :init-value="''"
              />
          </div>
          <p class="msg-error" v-if="contentIsError">Content is not null</p>
          <a-row :gutter="20">
            <a-col :span="12">
              <a-form-item label="Image">
                <a-upload name="file" v-decorator="['file']" :file-list="fileList"
                  action="//jsonplaceholder.typicode.com/posts/" :headers="headers" @remove="handleRemove"
                  :beforeUpload="beforeUpload">
                  <a-button> <a-icon type="upload" />Attachments</a-button>
                </a-upload>
              </a-form-item>
            </a-col>
            <a-col :span="12">
              <a-form-item label="Status">
                <a-select v-decorator="[
          'active',
          {
            initialValue: 0,
            rules: [
              {
                required: true,
                message: 'Status is not null',
              },
            ],
          },
        ]" placeholder="Choose Status">
                  <a-select-option :value="0">
                    InActive
                  </a-select-option>
                  <a-select-option :value="1">
                    Active
                  </a-select-option>
                </a-select>
              </a-form-item>
            </a-col>
            <a-col :span="12">
              <a-form-item label="Album Product">
                <a-upload v-decorator="['images']" name="images" :multiple="true"
                  :default-file-list="initialValue.fileList" :headers="headers"
                  action="//jsonplaceholder.typicode.com/posts/" list-type="picture">
                  <a-button> <a-icon type="upload" />Choose Your Album</a-button>
                </a-upload>
              </a-form-item>
            </a-col>
            <a-col :span="12">
              <a-form-item label="Inventory">
                <a-input v-decorator="['inventory_number',
                    {
                      initialValue: '',
                    }
                  ]" type="number" />
              </a-form-item>
            </a-col>
          </a-row>
        </a-form>
      </a-modal>
    </template>

    <a-modal :visible="this.flagModalConfirm" @cancel="cancelModalConfirm" footer="" :closable="false"
      class="confirm-edit">
      <div class="confirm-edit-title">Notification</div>
      <div class="confirm-edit-content">
        You are doing change Product. Are you sure about that?
      </div>
      <div class="footer">
        <a-button class="btn-button-cancel mr-2" @click="cancelModalConfirm">Cancel</a-button>
        <a-button key="submit" type="primary" class="btn-button primary" @click="editProducts">OK</a-button>
      </div>
    </a-modal>

    <a-modal v-model="this.flagModalDelete" @cancel="cancelModalDelete" footer="" :closable="false"
      class="confirm-delete">
      <div class="confirm-delete-title">Notification</div>
      <div class="confirm-delete-content">
        <p>You are in the process of deleting product.</p>
        <p>Are you sure about that?</p>
      </div>

      <div class="footer">
        <a-button class="btn-button mr-2" @click="cancelModalDelete">Cancel</a-button>
        <a-button key="submit" type="primary" class="btn-button primary" @click="deleteProduct()">OK</a-button>
      </div>
    </a-modal>
    <a-modal title="Edit Product" :visible="this.flagModalEdit" @cancel="cancelModalEdit" class="">
      <template #footer>
        <a-button class="btn-button-cancel" @click="cancelModalEdit">Cancel</a-button>
        <a-button key="submit" type="primary" class="btn-button primary" @click="editProduct">Edit</a-button>
      </template>
      <a-form :form="form">
        <a-row :gutter="20">
          <a-col :span="12">
            <a-form-item label="Name">
              <a-input v-decorator="[
          'name',
          {
            initialValue: initialValue.edit_name,
            rules: [{ required: true, message: 'Please input your name!' }],
          },
        ]" />
            </a-form-item>
            <a-form-item label="Price">
              <a-input v-decorator="[
          'price',
          {
            initialValue: initialValue.edit_price,
          },
        ]" />
            </a-form-item>
          </a-col>
          <a-col :span="12">
            <a-form-item label="Menu">
              <a-select v-decorator="[
          'menu_id',
          {
            initialValue: initialValue.menus,
            rules: [
              {
                required: true,
                message: 'Menu is not null'
              }
            ]
          }
        ]" mode="multiple" placeholder="Choose Menu" labelInValue>
                <a-select-option v-for="(menu) in menus" :value="menu.id" :key="menu.id">
                  {{ menu.name }}
                </a-select-option>
              </a-select>
            </a-form-item>
            <a-form-item label="Price_Sale">
              <a-input v-decorator="[
          'price_sale',
          {
            initialValue: initialValue.edit_price_sale,
          },
        ]" />
            </a-form-item>
          </a-col>
        </a-row>
        <a-form-item label="Description">
          <textarea v-decorator="[
          'description',
          {
            initialValue: initialValue.edit_description,
            rules: [{ required: true, message: 'Please input your description!' }],
          },
        ]" type="text" class="textarea"></textarea>
        </a-form-item>
        <a-form-item label="Content"></a-form-item>
        <div :class="{ error: contentIsError, content: true }">
          <VueEditor v-model="content" :placeholder="placeholderContent" @text-change="handleChangeContent" />
        </div>
        <p class="msg-error" v-if="contentIsError">Content is not null</p>
        <a-row :gutter="20">
          <a-col :span="12">
            <a-form-item label="Image">
              <a-upload name="file" v-decorator="['file']" action="//jsonplaceholder.typicode.com/posts/"
                :headers="headers" @remove="handleRemove" :beforeUpload="beforeUpload">
                <a-button> <a-icon type="upload" />Image</a-button>
              </a-upload>
              <a :href="initialValue.edit_file" target="_blank"><img class="image-contain fileUpdate"
                  :src="`${initialValue.edit_file}`" /></a>
            </a-form-item>
          </a-col>
          <a-col :span="12">
            <a-form-item label="Status">
              <a-select v-decorator="[
          'active',
          {
            initialValue: initialValue.edit_active,
            rules: [
              {
                required: true,
                message: 'Status is not null',
              },
            ],
          },
        ]" placeholder="Choose Status">
                <a-select-option :value="0">
                  InActive
                </a-select-option>
                <a-select-option :value="1">
                  Active
                </a-select-option>
              </a-select>
            </a-form-item>
          </a-col>
          <a-col :span="12">
            <a-form-item label="Inventory">
              <a-input v-decorator="['inventory_number',
          {
            initialValue: initialValue.edit_inventory_number ?? 0,
          }
        ]" type="number" />
            </a-form-item>
          </a-col>
        </a-row>
      </a-form>
    </a-modal>
  </div>
</template>

<script>
import IconDelete from '../../icon/icon-delete.vue'
import IconEdit from '../../icon/icon-edit.vue'
import { VueEditor } from 'vue2-editor';
import httpRequest from '../../../axios'

export default {
  components: { IconDelete, IconEdit, VueEditor },
  data() {
    return {
      products: {},
      menus: {},
      flagModalAdd: false,
      flagModalEdit: false,
      flagModalConfirm: false,
      flagModalDelete: false,
      headers: {
        "Content-Type": "multipart/form-data",
      },
      placeholderContent: 'Input Content',
      fileList: [],
      name: '',
      content: '',
      contentIsError: false,
      form: this.$form.createForm(this, { name: 'coordinated' }),
      totalPage: 2,
      page: 1,
      row: 10,
      delete_id: '',
      CountData: 10,
      pageSizeList: [10, 20, 30],
      pageSize: 10,
      lastPage: '',
      meta: {
        "from": 1,
        "to": 10
      },
      initialValue: {
        edit_name: '',
        edit_description: '',
        edit_price: 20,
        edit_price_sale: 30,
        edit_file: [],
        menus: [],
        edit_active: 1,
        edit_id: 1,
        edit_inventory_number: 1
      },
      isSorter: false,
      field: '',
    }
  },
  computed: {
    inputType() {
      return this.isSorter ? 'asc' : 'desc'
    },
  },
  methods: {
    getResult(row, page, name = '', sorter = 'desc') {
      httpRequest
        .get('/api/products/list?limit=' + row + '&page=' + page + '&field=' + name + '&sortType=' + sorter)
        .then(
          ({ data }) => (
            (this.products = data.data),
            (this.totalPage = data.meta.total),
            (this.lastPage = data.meta.last_page),
            (this.checkPage()),
            (this.checkRow())
          )
        );
    },
    getMenu() {
      httpRequest
        .get('/api/menus/list')
        .then(
          ({ data }) => (
            (this.menus = data.data)
          )
        )
    },
    toggleSort(name) {
      this.isSorter = !this.isSorter
      this.getResult(this.row, this.page, name, this.inputType);
    },
    handleRemove(file) {
      const index = this.fileList.indexOf(file);
      const newFileList = this.fileList.slice();
      newFileList.splice(index, 1);
      this.fileList = newFileList;
    },
    beforeUpload(file) {
      this.fileList = [...this.fileList, file];
      return false;
    },
    showModalEdit(id) {
      this.getProduct(id);
      this.flagModalEdit = true
    },
    handlePrevPage() {
      if (this.page > 1) {
        this.page = this.page - 1;
      }
      this.checkPage();
      this.getResult(this.row, this.page, this.field, this.inputType);
    },
    handleNextPage() {
      this.btn = false;
      if (this.page < this.lastPage) {
        this.page = this.page + 1;
      }
      this.checkPage();
      this.getResult(this.row, this.page, this.field, this.inputType);
    },
    rowPerPage(pageSize) {
      this.pageSize = pageSize
      this.row = pageSize
      if (this.totalPage % pageSize != 0) {
        this.lastPage = Math.floor(this.totalPage / pageSize + 1);
      } else {
        this.lastPage = Math.floor(this.totalPage / pageSize);
      }
      this.page = 1;
      this.checkPage();
      this.getResult(this.row, 1);
    },
    checkPage() {
      if (this.page == this.lastPage) {
        this.btnNext = false;
      } else {
        this.btnNext = true;
      }
      if (this.page == 1) {
        this.btnPrev = false;
      } else {
        this.btnPrev = true;
      }
    },
    checkRow() {
      this.meta.from = this.page * this.pageSize - this.pageSize + 1
      this.meta.to = this.meta.from + this.pageSize - 1
      if (this.meta.to >= this.totalPage) {
        this.meta.to = this.totalPage
      }
    },
    showAddUser() {
      this.flagModalAdd = true
    },
    addProduct(e) {
      e.preventDefault();
      this.form
        .validateFields((err, values) => {
          if (this.content.length === 0) {
            this.contentIsError = true;
            return;
          }
          if (err) return;
          const formData = new FormData();
          formData.append("name", values.name);
          formData.append("price", values.price);
          formData.append("price_sale", values.price_sale);
          formData.append("description", values.description);
          formData.append("content", this.content);
          formData.append("active", values.active);
          formData.append("inventory_number", values.inventory_number);
          values.menu_id.forEach((item, index) => {
            formData.append('menu_id[]', item.key);
          });
          this.fileList.forEach((item, index) => {
            formData.append("file", item);
          });
          if (values.images) {
            const listFileUpload = values.images.fileList.filter(
              (e) => e.originFileObj
            );
            listFileUpload.forEach((item, index) => {
              formData.append('images[]', item.originFileObj);
            });
          }
          httpRequest.post('/api/products/add', formData).then((response) => {
            this.info = response
            this.flagModalAdd = false
            this.getResult(this.row, this.page)
            Toast.fire({
              icon: 'success',
              title: 'Create Success'
            });
            this.form.resetFields()
          })
        })
    },
    showModalDelete(id) {
      this.delete_id = id
      this.flagModalDelete = true
    },
    editProducts() {
      this.flagModalEdit = false
      this.flagModalConfirm = true
    },
    cancelModalAdd() {
      this.flagModalAdd = false
    },
    cancelModalEdit(e) {
      this.form.resetFields()
      this.flagModalEdit = false
    },
    cancelModalConfirm() {
      this.flagModalConfirm = false
      this.form.resetFields()
    },
    cancelModalDelete() {
      this.flagModalDelete = false
    },
    deleteProduct() {
      httpRequest.delete('/api/products/destroy/' + this.delete_id).then((response) => {
        this.info = response
        this.getResult(this.row, this.page)
        Toast.fire({
          icon: 'success',
          title: 'Delete Success'
        });
      })
        .catch(function (error) {
          Toast.fire({
            icon: 'error',
            title: error
          });
        })
      this.flagModalDelete = false
    },
    handleChangeContent() {
      if (this.content.length > 0) {
        this.contentIsError = false;
      }
      if (this.content.length === 0) {
        this.contentIsError = true;
      }
    },
    getProduct(id) {
      httpRequest
        .get('/api/products/edit/' + id)
        .then((response) => {
          this.initialValue.edit_name = response.data.data.name;
          this.initialValue.edit_description = response.data.data.description,
          this.initialValue.edit_price = response.data.data.price,
          this.initialValue.edit_price_sale = response.data.data.price_sale,
          this.content = response.data.data.content,
          this.initialValue.edit_file = response.data.data.file,
          this.initialValue.edit_active = response.data.data.active
          this.initialValue.edit_id = response.data.data.id
          this.initialValue.edit_inventory_number = response.data.data.inventory_number
          this.initialValue.menus = response.data.data.menus.map((item) => {
            return {
              key: item.id,
              label: item.name
            };
          });
        })
    },
    editProduct(e) {
      e.preventDefault();
      this.form
        .validateFields((err, values) => {
          if (this.content.length === 0) {
            this.contentIsError = true;
            return;
          }
          if (err) {
            Toast.fire({
              icon: 'error',
              title: err
            });
          };
          const formData = new FormData();
          formData.append("name", values.name);
          formData.append("price", values.price);
          formData.append("price_sale", values.price_sale);
          formData.append("description", values.description);
          formData.append("content", this.content);
          formData.append("active", values.active);
          formData.append("inventory_number", values.inventory_number);
          values.menu_id.forEach((item, index) => {
            formData.append('menu_id[]', item.key);
          });
          this.fileList.forEach((item, index) => {
            formData.append("file", item);
          });
          httpRequest
            .post('/api/products/edit/' + this.initialValue.edit_id + '?_method=PUT', formData)
            .then((response) => {
              this.info = response
              this.flagModalEdit = false
              this.form.resetFields()
              this.getResult(this.row, this.page)
              Toast.fire({
                icon: 'success',
                title: 'Edit Success'
              });
              this.flagModalConfirm = false
            })
        })
        .catch(function (error) {
          Toast.fire({
            icon: 'error',
            title: error
          });
        })
      this.form.resetFields()
    },
    exportProduct() {
      httpRequest.get('/api/export/1', { responseType: 'arraybuffer' })
        .then((response) => {
          var fileURL = window.URL.createObjectURL(new Blob([response.data]));
          var fileLink = document.createElement('a');
          fileLink.href = fileURL;
          fileLink.setAttribute('download', 'product.xlsx');
          document.body.appendChild(fileLink);
          fileLink.click();
          Toast.fire({
            icon: 'success',
            title: 'Export Success'
          });
        })
        .catch(function (error) {
          Toast.fire({
            icon: 'error',
            title: error
          });
        })
    },
    async beforeImport(file) {
      this.fileList = [...this.fileList, file];
      this.importProduct(this.fileList);
      return new Promise((resolve) => { })
    },

    async importProduct(file) {
      try {
        const uploadFileIds = [];
        for (const item of this.fileList) {
          const formData = new FormData();
          formData.append("file", item);
          formData.append("type", 1);
          const responseUpload = await httpRequest.post('/api/files/upload-file', formData)
          uploadFileIds.push(responseUpload.data.data.id);
        }
        const responseImport = await httpRequest.post('/api/products/import-product', { ids: uploadFileIds, type: 1 });
        this.info = responseImport;
        Toast.fire({
          icon: 'success',
          title: 'Import Success'
        });
      } catch (error) {
        Toast.fire({
          icon: 'error',
          title: error
        });
      }
    }
  },
  mounted() {
    this.getResult(this.row, this.page)
    this.getMenu()
  },
  created() {
    this.$Progress.start()
    this.getResult(this.row, this.page)
    this.$Progress.finish()
  },
}
</script>

<style lang="scss" scoped>
.table {
  overflow-y: auto;
  max-height: calc(100vh - 380px);
}

table {
  border-collapse: collapse;
  width: 80%;
  border: 1px solid #ddd;
}

td {
  text-align: left;
  padding: 16px;
}

.thead {
  width: 100%;
}

.tbody {
  width: 100%;
}

.button-type {
  width: 200px;
  height: 40px;
  margin-bottom: 10px;
}

.textarea {
  height: 100px;
}

::v-deep .notify-success {
  color: red !important;
}

::v-deep .anticon-question-circle {
  display: none !important;
}

::v-depp .ant-modal {
  width: 50%;
}

::v-deep .ant-modal-title {
  text-align: center;
  color: #000000;
  font-size: 24px;
  line-height: calc(28.97 / 24);
  font-weight: 700;
}

::v-deep label {
  color: #333333;
  font-size: 16px;
  line-height: 162.02%;
  font-weight: 700;
}

::v-deep .ant-modal-header {
  border-bottom: none !important;
}

::v-deep .ant-modal-body {
  padding: 8px 24px;
}

::v-deep .ant-modal-content {
  width: 800px;
}

::v-deep .ant-modal-footer {
  border-top: none !important;
  padding-bottom: 20px;
}

.confirm-edit {
  .ant-modal-body {
    padding: 5px 14px 4px 14px;
  }

  &-title {
    font-weight: 700;
    font-size: 20px;
    line-height: 136.02%;
    color: #000000;
  }

  &-content {
    font-weight: 400;
    font-size: 15px;
    line-height: 136.02%;
    color: #515151;
    margin-bottom: 14px;
  }

  .footer {
    display: flex;
    justify-content: right;
    margin-bottom: 5px;
  }
}

.confirm-delete {
  .ant-modal-body {
    padding: 5px 14px 4px 14px;
  }

  &-title {
    font-weight: 700;
    font-size: 20px;
    line-height: 136.02%;
    color: #e41d1d;
  }

  &-content {
    font-weight: 400;
    font-size: 15px;
    line-height: 136.02%;
    color: #e41d1d;
    margin-bottom: 14px;

    p {
      margin: 0px;
    }
  }

  .footer {
    display: flex;
    justify-content: right;
    margin-bottom: 5px;
  }
}

.content {
  padding: 0px;
  margin-bottom: 10px;
}

.sortType:hover {
  background-color: yellow;
}

.category-value {
  display: flex;
  margin-bottom: 10px;

  .category-title {
    color: #333333;
    font-size: 16px;
    line-height: 162.02%;
    font-weight: 700;
    margin-right: 5px;
  }

  .category-name {
    color: #0047ff;
    font-size: 16px;
    line-height: calc(26 / 16);
    font-weight: 700;
  }
}

.icon-edit {
  margin-right: 5px;
  cursor: pointer;
}

.icon-delete {
  margin-right: 5px;
  cursor: pointer;
}

.btn-button {
  padding: 0 29px;
}

.btn-button-cancel {
  padding: 0 35px;
}

.primary {
  background: #1280bf;

  span {
    color: #fff;
  }
}

.category-content {
  min-height: calc(100vh - 96px);
  margin-left: calc(20% + 10px);
  padding: 20px 24px;
  border-radius: 5px;
  box-shadow: 0px 4px 4px rgb(0 0 0 / 25%);
  display: flex;
  flex-direction: column;
  color: #000;
  background-color: #fff;
  margin-top: 16px;

  .ant-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 18px;
    width: 147px;
  }

  .ant-btn-primary {
    background-color: #1280bf;
  }
}

.content-body {
  width: 100%;
}

.file {
  width: 70%;
  height: 70px;
}

.fileUpdate {
  width: 60%;
  height: 150px;
}

.title {
  font-size: 24px;
  font-weight: 700;
  color: #000;
  margin-bottom: 18px;
}

.center-text {
  text-align: center !important;
  vertical-align: middle;
}

.pagination-wrapper {
  display: flex;
  justify-content: center;
  margin-top: 30px;
  align-items: center;
  gap: 10px;
  font-size: 12px;
  margin-right: auto;
  color: rgba(0, 0, 0, 0.6);

  .total-page {
    color: rgba(0, 0, 0, 0.87);
    margin: 0 30px;
  }

  .dropdown-icon {
    font-size: 16px;
    color: rgba(0, 0, 0, 0.54);
    position: relative;
    top: -7px;
    left: 4px;
  }

  .pagination-btn {
    border: none;
    padding: 14px;
    margin-bottom: 0;
    width: unset;

    .icon-btn {
      font-size: 14px;
      color: rgba(0, 0, 0, 0.54);
    }
  }
}

::v-deep .ant-select-selection {
  border: none;
}
</style>
