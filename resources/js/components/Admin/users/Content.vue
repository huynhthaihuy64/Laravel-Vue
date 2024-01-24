<template>
  <div class="container-fluid category-content">
    <h1 class="title">{{ $store.getters.localizedStrings.user_management.title }}</h1>
    <div class="content-body">
      <a-row>
        <a-col :span="12">
          <a-button v-if="currentUser.role === 1" type="primary" @click="showAddUser()" class="button-type">
            <a-icon type="plus" />
            {{ $store.getters.localizedStrings.user_management.add_user.title }}
          </a-button>
        </a-col>
        <a-col :span="2"></a-col>
        <a-col :span="3">
          <a-button class="button-type" @click="showModalSendMail()">
            <font-awesome-icon :icon="['fas', 'mail-forward']" class="mr-2" />
            Send Mail All
          </a-button></a-col>
        <a-modal title="Send All MAil" :visible="this.flagModelSendMail" @cancel="() => cancelModalSendMail()">
          <template #footer>
            <a-button class="btn-button-cancel" @click="cancelModalSendMail">Cancel</a-button>
            <a-button key="submit" type="primary" class="btn-button primary" @click="sendMailAll">Send</a-button>
          </template>
          <a-form :form="form">
            <a-form-item label="Subject">
              <a-input v-decorator="[
                'subject',
                {
                  initialValue: '',
                  rules: [{ required: true, message: 'Please input your Subject!' }],
                },
              ]" />
            </a-form-item>
            <a-form-item label="Content">
              <textarea v-decorator="[
                'content',
                {
                  initialValue: '',
                  rules: [{ required: true, message: 'Please input your Content!' }],
                },
              ]" type="text" class="textarea"></textarea>
            </a-form-item>
          </a-form>
        </a-modal>
        <a-col :span="3">
          <a-button class="button-type" @click="exportUser">
            <font-awesome-icon :icon="['fas', 'file-excel']" class="mr-2" />
            {{ $store.getters.localizedStrings.user_management.export_excel }}
          </a-button></a-col>
        <a-col :span="4"></a-col>
      </a-row>
      <table class="table">
        <thead>
          <tr>
            <th scope="col" class="category-order center-text">{{ $store.getters.localizedStrings.user_management.no }}</th>
            <th class="sortType center-text"><a @click="toggleSort('name')">{{ $store.getters.localizedStrings.user_management.name }}
                <i class="fas fa-sort"></i></a></th>
            <th class="sortType center-text"><a @click="toggleSort('email')">{{ $store.getters.localizedStrings.user_management.email
            }} <i class="fas fa-sort"></i></a></th>
            <th class="sortType center-text"><a @click="toggleSort('phone')">{{ $store.getters.localizedStrings.user_management.phone
            }}<i class="fas fa-sort"></i></a></th>
            <th class="sortType center-text"><a @click="toggleSort('created_at')">{{
              $store.getters.localizedStrings.user_management.created }} <i class="fas fa-sort"></i></a></th>
            <th v-if="currentUser.role === 1"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in users" :key="item.id">
            <th scope="row" class="center-text">{{ index + 1 }}</th>
            <td class="center-text">{{ item.name }}</td>
            <td class="center-text">{{ item.email }}</td>
            <td class="center-text">{{ item.phone }}</td>
            <td class="center-text">{{ item.created_at }}</td>
            <td class="center-text" v-if="currentUser.role === 1">
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
      <a-modal :title="$store.getters.localizedStrings.user_management.add_user.title" :visible="this.flagModalAdd"
        @cancel="() => cancelModalAdd()">
        <template #footer>
          <a-button class="btn-button-cancel" @click="cancelModalAdd">{{
            $store.getters.localizedStrings.user_management.cancel }}</a-button>
          <a-button key="submit" type="primary" class="btn-button primary" @click="addUser">{{
            $store.getters.localizedStrings.user_management.add_user.title }}</a-button>
        </template>
        <a-form :form="form">
          <a-row :gutter="20">
            <a-col :span="12">
              <a-form-item :label="$store.getters.localizedStrings.user_management.add_user.name">
                <a-input v-decorator="[
                  'name',
                  {
                    initialValue: '',
                    rules: [{ required: true, message: 'Please input your name!' }],
                  },
                ]" />
              </a-form-item>
              <a-form-item :label="$store.getters.localizedStrings.user_management.add_user.email">
                <a-input v-decorator="['email',
                  {
                    initialValue: '',
                    rules: [{ required: true, message: 'Please input your email!' }],
                  },
                ]" type="email" />
              </a-form-item>
              <a-form-item :label="$store.getters.localizedStrings.user_management.add_user.avatar">
                <a-upload name="avatar" v-decorator="['avatar']" :file-list="fileList"
                  action="//jsonplaceholder.typicode.com/posts/" :headers="headers" @remove="handleRemove"
                  :beforeUpload="beforeUpload" list-type="picture">
                  <a-button> <a-icon type="upload" />{{
                    $store.getters.localizedStrings.user_management.add_user.attachments
                  }}</a-button>
                </a-upload>
              </a-form-item>
            </a-col>
            <a-col :span="12">
              <a-form-item :label="$store.getters.localizedStrings.user_management.add_user.password">
                <a-input v-decorator="[
                  'password',
                  {
                    initialValue: '',
                    rules: [
                      {
                        required: true,
                        message: 'Please input your password!',
                      },
                    ],
                  },
                ]" type="password" />
              </a-form-item>
              <a-form-item :label="$store.getters.localizedStrings.user_management.add_user.phone">
                <a-input v-decorator="['phone',
                  {
                    initialValue: '',
                  }
                ]" />
              </a-form-item>
              <a-form-item label="Role">
                <a-select v-decorator="[
                  'role_id',
                  {
                    initialValue: '',
                    rules: [
                      {
                        required: true,
                        message: 'Please input your password!',
                      },
                    ],
                  }
                ]" placeholder="Choose Role">
                  <a-select-option v-for="(role) in roles" :value="role.id" :key="role.id">
                    {{ role.name }}
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
      <div class="confirm-edit-title">{{ $store.getters.localizedStrings.user_management.confirm.title }}</div>
      <div class="confirm-edit-content">
        {{ $store.getters.localizedStrings.user_management.confirm.content }}
      </div>
      <div class="footer">
        <a-button class="btn-button-cancel mr-2" @click="cancelModalConfirm">{{
          $store.getters.localizedStrings.user_management.confirm.cancel }}</a-button>
        <a-button key="submit" type="primary" class="btn-button primary" @click="editUsers">{{
          $store.getters.localizedStrings.user_management.confirm.confirm }}</a-button>
      </div>
    </a-modal>

    <a-modal v-model="this.flagModalDelete" @cancel="cancelModalDelete" footer="" :closable="false"
      class="confirm-delete">
      <div class="confirm-delete-title">{{ $store.getters.localizedStrings.user_management.delete.title }}</div>
      <div class="confirm-delete-content">
        {{ $store.getters.localizedStrings.user_management.delete.content }}
      </div>

      <div class="footer">
        <a-button class="btn-button mr-2" @click="cancelModalDelete">{{
          $store.getters.localizedStrings.user_management.delete.cancel }}</a-button>
        <a-button key="submit" type="primary" class="btn-button primary" @click="deleteUser()">{{
          $store.getters.localizedStrings.user_management.delete.confirm }}</a-button>
      </div>
    </a-modal>
    <a-modal title="Edit User" :visible="this.flagModalEdit" @cancel="cancelModalEdit" class="">
      <template #footer>
        <a-button class="btn-button-cancel" @click="cancelModalEdit">{{
          $store.getters.localizedStrings.user_management.delete.cancel }}</a-button>
        <a-button key="submit" type="primary" class="btn-button primary" @click="editUser">{{
          $store.getters.localizedStrings.user_management.edit_user.title }}</a-button>
      </template>
      <a-form :form="form">
        <a-row :gutter="20">
          <a-col :span="12">
            <a-form-item :label="$store.getters.localizedStrings.user_management.edit_user.name">
              <a-input v-decorator="[
                'name',
                {
                  initialValue: initialValue.edit_name,
                  rules: [{ required: true, message: 'Please input your name!' }],
                },
              ]" />
            </a-form-item>
            <a-form-item :label="$store.getters.localizedStrings.user_management.edit_user.email">
              <a-input v-decorator="['email',
                {
                  initialValue: initialValue.edit_email,
                  rules: [{ required: true, message: 'Please input your email!' }],
                },
              ]" type="email" />
            </a-form-item>
            <a-form-item :label="$store.getters.localizedStrings.user_management.edit_user.avatar">
              <a-upload name="avatar" v-decorator="['avatar']" :multiple="true" :headers="headers"
                :supportServerRender="false" action="//jsonplaceholder.typicode.com/posts/" @remove="handleRemove"
                :beforeUpload="beforeUpload">
                <a-button> <a-icon type="upload" />{{
                  $store.getters.localizedStrings.user_management.edit_user.attachments }}</a-button>
              </a-upload>
              <div v-if="initialValue.edit_avatar != null">
                <a :href="initialValue.edit_avatar" target="_blank"><img class="image-contain fileUpdate"
                    :src="`${initialValue.edit_avatar}`" /></a>
              </div>
            </a-form-item>
          </a-col>
          <a-col :span="12">
            <a-form-item :label="$store.getters.localizedStrings.user_management.edit_user.password">
              <a-input v-decorator="[
                'password',
                {
                  initialValue: '',
                },
              ]" type="password" />
            </a-form-item>
            <a-form-item :label="$store.getters.localizedStrings.user_management.edit_user.phone">
              <a-input v-decorator="[
                'phone',
                {
                  initialValue: initialValue.edit_phone,
                },
              ]" />
            </a-form-item>
            <a-form-item label="Role">
              <a-select v-decorator="[
                'role_id',
                {
                  initialValue: initialValue.edit_role,
                }
              ]" placeholder="Choose Role" labelInValue>
                <a-select-option v-for="(role) in roles" :value="role.id" :key="role.id">
                  {{ role.name }}
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
      users: {},
      roles: {},
      flagModalAdd: false,
      flagModalEdit: false,
      flagModelSendMail: false,
      flagModalConfirm: false,
      flagModalDelete: false,
      headers: {
        "Content-Type": "multipart/form-data",
      },
      placeholderContent: 'Input content',
      fileList: [],
      name: '',
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
        edit_email: '',
        edit_password: '',
        edit_phone: '',
        edit_id: 1,
        edit_avatar: [],
        edit_role: 3,
      },
      currentUser: {
        role: 1,
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
    getResult(row, page, name = '', sorter = 'desc') {
      httpRequest
        .get('/api/users/list?limit=' + row + '&page=' + page + '&field=' + name + '&sortType=' + sorter)
        .then(
          ({ data }) => (
            (this.users = data.data),
            (this.totalPage = data.meta.total),
            (this.lastPage = data.meta.last_page),
            (this.checkPage()),
            (this.checkRow())
          )
        );
    },
    toggleSort(name) {
      this.isSorter = !this.isSorter
      this.getResult(this.row, this.page, name, this.inputType);
    },
    handleRemove(avatar) {
      const index = this.fileList.indexOf(avatar);
      const newFileList = this.fileList.slice();
      newFileList.splice(index, 1);
      this.fileList = newFileList;
    },
    beforeUpload(avatar) {
      this.fileList = [...this.fileList, avatar];
      return false;
    },
    showModalEdit(id) {
      this.getUser(id);
      this.flagModalEdit = true
    },
    handlePrevPage() {
      if (this.page > 1) {
        this.page = this.page - 1;
      }
      this.checkPage();
      this.getResult(this.row, this.page);
    },
    handleNextPage() {
      this.btn = false;
      if (this.page < this.lastPage) {
        this.page = this.page + 1;
      }
      this.checkPage();
      this.getResult(this.row, this.page);
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
    showAddUser() {
      this.flagModalAdd = true
    },
    showModalSendMail() {
      this.flagModelSendMail = true
    },
    addUser(e) {
      e.preventDefault();
      this.form
        .validateFields((err, values) => {
          if (err) return;
          const formData = new FormData();
          formData.append("name", values.name);
          formData.append("email", values.email);
          formData.append("password", values.password);
          formData.append("phone", values.phone);
          this.fileList.forEach((item, index) => {
            formData.append("avatar", item);
          });
          httpRequest.post('/api/users/add', formData).then((response) => {
            this.info = response
            this.flagModalAdd = false
            this.getResult(this.row, this.page)
            Toast.fire({
              icon: 'success',
              title: '' + this.$store.getters.localizedStrings.user_management.add_user.success
            });
          })
          this.form.resetFields();
        })
    },
    sendMailAll(e) {
      e.preventDefault();
      this.form
        .validateFields((err, values) => {
          if (err) return;
          const formData = new FormData();
          formData.append("subject", values.subject);
          formData.append("content", values.content);
          this.fileList.forEach((item, index) => {
            formData.append("avatar", item);
          });
          httpRequest.post('/api/sendMailAll', formData).then((response) => {
            this.info = response
            this.flagModelSendMail = false
            this.getResult(this.row, this.page)
            Toast.fire({
              icon: 'success',
              title: 'Send Mail Success'
            });
          })
          this.form.resetFields();
        })
    },
    showModalDelete(id) {
      this.delete_id = id
      this.flagModalDelete = true
    },
    editUsers() {
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
    cancelModalSendMail() {
      this.flagModelSendMail = false
    },
    cancelModalConfirm() {
      this.flagModalConfirm = false
      this.form.resetFields()
    },
    cancelModalDelete() {
      this.flagModalDelete = false
    },
    deleteUser() {
      httpRequest.delete('/api/users/destroy/' + this.delete_id).then((response) => {
        this.info = response
        this.getResult(this.row, this.page)
        Toast.fire({
          icon: 'success',
          title: '' + this.$store.getters.localizedStrings.user_management.delete.success
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
    getUser(id) {
      httpRequest
        .get('/api/users/edit/' + id)
        .then((response) => {
          this.initialValue.edit_name = response.data.name;
          this.initialValue.edit_email = response.data.email;
          this.initialValue.edit_password = response.data.password;
          this.initialValue.edit_phone = response.data.phone;
          this.initialValue.edit_id = response.data.id;
          this.initialValue.edit_avatar = response.data.avatar;
          this.initialValue.edit_role = response.data.role.name;
        })
    },
    getCurrentUser() {
      httpRequest
        .get('/api/admin/users/currentUser')
        .then((response) => {
          this.currentUser.role = response.data.role_id;
        })
    },
    getRole() {
      httpRequest
        .get('/api/admin/users/role')
        .then((data) => {
          this.roles = data.data
        });
    },
    editUser(e) {
      e.preventDefault();
      this.form
        .validateFields((err, values) => {
          if (err) return;
          const formData = new FormData();
          formData.append("name", values.name);
          formData.append("email", values.email);
          formData.append("password", values.password);
          formData.append("phone", values.phone);
          formData.append("role_id", values.role_id);
          this.fileList.forEach((item, index) => {
            formData.append("avatar", item);
          });
          httpRequest
            .post('/api/users/edit/' + this.initialValue.edit_id + '?_method=PUT', formData)
            .then((response) => {
              this.info = response
              this.flagModalEdit = false
              this.form.resetFields()
              this.getResult(this.row, this.page)
              Toast.fire({
                icon: 'success',
                title: '' + this.$store.getters.localizedStrings.user_management.edit_user.success
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
    exportUser() {
      httpRequest.get('/api/export/4', { responseType: 'arraybuffer' })
        .then((response) => {
          var fileURL = window.URL.createObjectURL(new Blob([response.data]));
          var fileLink = document.createElement('a');
          fileLink.href = fileURL;
          fileLink.setAttribute('download', 'user.csv');
          document.body.appendChild(fileLink);
          fileLink.click();
          Toast.fire({
            icon: 'success',
            title: '' + this.$store.getters.localizedStrings.user_management.export_success
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
    this.getCurrentUser()
    this.getRole()
    this.getResult(this.row, this.page)
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
  // display: block;
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

.sortType:hover {
  background-color: yellow;
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

.center-text {
  text-align: center !important;
  vertical-align: middle;
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
  width: 30%;
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
  