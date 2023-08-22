<template>
  <div class="container-fluid category-content">
    <h1 class="title">List Slide</h1>
    <div class="content-body">
      <a-row>
        <a-col :span="12">
          <a-button type="primary" @click="showAddSlider()" class="button-type">
            <a-icon type="plus" />
            Add Slide
          </a-button>
        </a-col>
        <a-col :span="5"></a-col>
        <a-col :span="5">
          <a-button class="button-type" @click="exportSlider">
            <font-awesome-icon :icon="['fas', 'file-excel']" class="mr-2" />
            Export Excel
          </a-button></a-col>
        <a-col :span="2"></a-col>
      </a-row>
      <table class="table">
        <thead>
          <tr>
            <th scope="col" class="category-order">No</th>
            <th class="sortType"><a @click="toggleSort('name')">Name <i class="fas fa-sort"></i></a></th>
            <th>Url</th>
            <th>Image</th>
            <th class="sortType"><a @click="toggleSort('active')">Status <i class="fas fa-sort"></i></a></th>
            <th class="sortType"><a @click="toggleSort('created_at')">Created <i class="fas fa-sort"></i></a></th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in sliders" :key="item.id">
            <th scope="row">{{ index + 1 }}</th>
            <td>{{ item.name }}</td>
            <td><a :href="item.url" target="_blank"> {{ item.url }} </a></td>
            <td><img class="file image-contain" :src="`${item.file}`" /></td>
            <td v-if="item.active == 0"><span class="btn btn-danger btn-xs w-75">InActive</span></td>
            <td v-if="item.active == 1"><span class="btn btn-success btn-xs w-75">Active</span></td>
            <td>{{ item.created_at }}</td>
            <td>
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
      <a-modal title="Add New Slide" :visible="this.flagModalAdd" @cancel="() => cancelModalAdd()">
        <template #footer>
          <a-button class="btn-button-cancel" @click="cancelModalAdd">Cancel</a-button>
          <a-button key="submit" type="primary" class="btn-button primary" @click="addSlider">Add</a-button>
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
            </a-col>
            <a-col :span="12">
              <a-form-item label="Url">
                <a-input v-decorator="['url',
                  {
                    initialValue: '',
                  }
                ]" />
              </a-form-item>
            </a-col>
          </a-row>
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
                    initialValue: '',
                    rules: [
                      {
                        required: true,
                        message: 'Is Not Null',
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
          </a-row>
        </a-form>
      </a-modal>
    </template>

    <a-modal :visible="this.flagModalConfirm" @cancel="cancelModalConfirm" footer="" :closable="false"
      class="confirm-edit">
      <div class="confirm-edit-title">Notification</div>
      <div class="confirm-edit-content">
        You are doing change Slide. Are you sure about that?
      </div>
      <div class="footer">
        <a-button class="btn-button-cancel mr-2" @click="cancelModalConfirm">Cancel</a-button>
        <a-button key="submit" type="primary" class="btn-button primary" @click="editMenus">OK</a-button>
      </div>
    </a-modal>

    <a-modal v-model="this.flagModalDelete" @cancel="cancelModalDelete" footer="" :closable="false"
      class="confirm-delete">
      <div class="confirm-delete-title">Notification</div>
      <div class="confirm-delete-content">
        <p>You are in the process of deleting slides.</p>
        <p>Are you sure about that?</p>
      </div>

      <div class="footer">
        <a-button class="btn-button mr-2" @click="cancelModalDelete">Cancel</a-button>
        <a-button key="submit" type="primary" class="btn-button primary" @click="deleteSlider()">OK</a-button>
      </div>
    </a-modal>
    <a-modal title="Edit Slide" :visible="this.flagModalEdit" @cancel="cancelModalEdit" class="">
      <template #footer>
        <a-button class="btn-button-cancel" @click="cancelModalEdit">Cancel</a-button>
        <a-button key="submit" type="primary" class="btn-button primary" @click="editSlider">Edit</a-button>
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
          </a-col>
          <a-col :span="12">
            <a-form-item label="Url">
              <a-input v-decorator="['url',
                {
                  initialValue: initialValue.edit_url,
                  rules: [{ required: true, message: 'Please input URL' }],
                },
              ]" />
            </a-form-item>
          </a-col>
        </a-row>
        <a-row :gutter="20">
          <a-col :span="12">
            <a-form-item label="Image">
              <a-upload name="file" v-decorator="['file']" action="//jsonplaceholder.typicode.com/posts/"
                :headers="headers" @remove="handleRemove" :beforeUpload="beforeUpload">
                <a-button> <a-icon type="upload" />Attachments</a-button>
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
                      message: 'Is Not Null',
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
        </a-row>
      </a-form>
    </a-modal>
  </div>
</template>
    
<script>
import IconDelete from '../../icon/icon-delete.vue'
import IconEdit from '../../icon/icon-edit.vue'
import httpRequest from '../../../axios'

export default {
  components: { IconDelete, IconEdit },
  data() {
    return {
      sliders: {},
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
      url: '',
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
        edit_url: '',
        edit_file: [],
        edit_active: 1,
        edit_id: 1
      },
      isSorter: false,
    }
  },
  computed: {
    inputType() {
      return this.isSorter ? 'asc' : 'desc'
    },
  },
  methods: {
    getResuilt(row, page, name = '', sorter = 'desc') {
      httpRequest
        .get('/api/sliders/list?limit=' + row + '&page=' + page + '&field=' + name + '&sortType=' + sorter)
        .then(
          ({ data }) => (
            (this.sliders = data.data),
            (this.totalPage = data.meta.total),
            (this.lastPage = data.meta.last_page),
            (this.checkPage()),
            (this.checkRow())
          )
        );
    },
    toggleSort(name) {
      this.isSorter = !this.isSorter
      this.getResuilt(this.row, this.page, name, this.inputType);
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
      this.getSlider(id);
      this.flagModalEdit = true
    },
    handlePrevPage() {
      if (this.page > 1) {
        this.page = this.page - 1;
      }
      this.checkPage();
      this.getResuilt(this.row, this.page);
    },
    handleNextPage() {
      this.btn = false;
      if (this.page < this.lastPage) {
        this.page = this.page + 1;
      }
      this.checkPage();
      this.getResuilt(this.row, this.page);
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
      this.getResuilt(this.row, 1);
    },
    checkPage() {
      if (this.page == this.lastPage) {
        this.btnNext = false;
      } else {
        this.btnNext = true;
      }
      if (this.page == 1) {
        this.btnPrew = false;
      } else {
        this.btnPrew = true;
      }
    },
    checkRow() {
      this.meta.from = this.page * this.pageSize - this.pageSize + 1
      this.meta.to = this.meta.from + this.pageSize - 1
      if (this.meta.to >= this.totalPage) {
        this.meta.to = this.totalPage
      }
    },
    showAddSlider() {
      this.flagModalAdd = true
    },
    addSlider(e) {
      e.preventDefault();
      this.form
        .validateFields((err, values) => {
          if (err) return;
          const formData = new FormData();
          formData.append("name", values.name);
          formData.append("url", values.url);
          formData.append("active", values.active);
          this.fileList.forEach((item, index) => {
            formData.append("file", item);
          });
          httpRequest.post('/api/sliders/add', formData).then((response) => {
            this.info = response
            this.flagModalAdd = false
            this.getResuilt(this.row, this.page)
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
    editMenus() {
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
    deleteSlider() {
      httpRequest.delete('/api/sliders/destroy/' + this.delete_id).then((response) => {
        this.info = response
        this.getResuilt(this.row, this.page)
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
    getSlider(id) {
      httpRequest
        .get('/api/sliders/edit/' + id)
        .then((response) => {
          this.initialValue.edit_name = response.data.name;
          this.initialValue.edit_url = response.data.url,
            this.initialValue.edit_file = response.data.file,
            this.initialValue.edit_active = response.data.active
          this.initialValue.edit_id = response.data.id
        })
    },
    editSlider(e) {
      e.preventDefault();
      this.form
        .validateFields((err, values) => {
          if (err) return;
          const formData = new FormData();
          formData.append("name", values.name);
          formData.append("url", values.url);
          formData.append("active", values.active);
          this.fileList.forEach((item, index) => {
            formData.append("file", item);
          });
          httpRequest
            .post('/api/sliders/edit/' + this.initialValue.edit_id + '?_method=PUT', formData)
            .then((response) => {
              this.info = response
              this.flagModalEdit = false
              this.form.resetFields()
              this.getResuilt(this.row, this.page)
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
    exportSlider() {
      httpRequest.get('/api/export/2', { responseType: 'arraybuffer' })
        .then((response) => {
          var fileURL = window.URL.createObjectURL(new Blob([response.data]));
          var fileLink = document.createElement('a');
          fileLink.href = fileURL;
          fileLink.setAttribute('download', 'slider.csv');
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
    }
  },
  mounted() {
    this.getResuilt(this.row, this.page)
  },
  created() {
    this.$Progress.start()
    this.getResuilt(this.row, this.page)
    this.$Progress.finish()
  },
}
</script>
    
<style lang="scss" scoped>
.table {
  overflow-y: auto;
  max-height: calc(100vh - 380px);
  // display: block;
}

.sortType:hover {
  background-color: yellow;
}

table {
  border-collapse: collapse;
  width: 80%;
  border: 1px solid #ddd;
}

th,
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
  width: 50%;
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
    