<template>
    <div class="container-fluid category-content">
        <h1 class="title">List Menu</h1>
        <div class="content-body">
            <div class="container">
                <div class="row">
                    <div class="col-6">
                        <a-button type="primary" @click="showAddMenu()" class="button-type">
                            <a-icon type="plus" />
                            Add Menu
                        </a-button>
                    </div>
                    <div class="col-4 d-flex justify-content-end">
                        <a-upload :beforeUpload="beforeUpload" :headers="headers" action="//jsonplaceholder.typicode.com/posts/">
                            <a-button  class="button-type">
                            <font-awesome-icon :icon="['fas', 'file-excel']" class="mr-2" />
                            Import Excel
                        </a-button>
                        </a-upload>
                    </div>
                    <div class="col-2">
                        <a-button class="button-type" @click="exportMenu">
                            <font-awesome-icon :icon="['fas', 'file-excel']" class="mr-2" />
                            Export Excel
                        </a-button>
                    </div>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="category-order">No</th>
                        <th>Name</th>
                        <th>Level</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in menus" :key="item.id">
                        <th scope="row">{{ index + 1 }}</th>
                        <td>{{ item.name }}</td>
                        <td v-if="item.parent_id == 0"><span class="btn btn-warning btn-xs w-75">Main</span></td>
                        <td v-if="item.parent_id == 1"><span class="btn btn-info btn-xs w-75">Sub</span></td>
                        <td>{{ item.description | truncate(30, '...') }}</td>
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
            <a-modal title="Add new menu" :visible="this.flagModalAdd" @cancel="() => cancelModalAdd()">
                <template #footer>
                    <a-button class="btn-button-cancel" @click="cancelModalAdd">Cancel</a-button>
                    <a-button key="submit" type="primary" class="btn-button primary" @click="addMenu">Add</a-button>
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
                            <a-form-item label="Level">
                                <a-select v-decorator="[
                                    'parent_id',
                                    {
                                        initialValue: '',
                                        rules: [
                                            {
                                                required: true,
                                                message: 'Is Not Null',
                                            },
                                        ],
                                    },
                                ]" placeholder="Choose Level">
                                    <a-select-option :value="0">
                                        Main Menu
                                    </a-select-option>
                                    <a-select-option :value="1">
                                        Sub Menu
                                    </a-select-option>
                                </a-select>
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
                        <VueEditor v-model="content" :placeholder="placeholderContent" @text-change="handleChangeContent" />
                    </div>
                    <p class="msg-error" v-if="contentIsError">Content is not null</p>
                    <a-form-item label="Status">
                        <a-select v-decorator="[
                            'active',
                            {
                                initialValue: '',
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
                </a-form>
            </a-modal>
        </template>

        <a-modal :visible="this.flagModalConfirm" @cancel="cancelModalConfirm" footer="" :closable="false"
            class="confirm-edit">
            <div class="confirm-edit-title">Notification</div>
            <div class="confirm-edit-content">
                You are making menu changes. Are you sure about that?
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
                <p>You are in the process of deleting a menu.</p>
                <p>Are you sure about that?</p>
            </div>

            <div class="footer">
                <a-button class="btn-button mr-2" @click="cancelModalDelete">Cancel</a-button>
                <a-button key="submit" type="primary" class="btn-button primary" @click="deleteMenu()">OK</a-button>
            </div>
        </a-modal>
        <a-modal title="Edit Menu" :visible="this.flagModalEdit" @cancel="cancelModalEdit" class="">
            <template #footer>
                <a-button class="btn-button-cancel" @click="cancelModalEdit">Cancel</a-button>
                <a-button key="submit" type="primary" class="btn-button primary" @click="editMenu">Edit</a-button>
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
                        <a-form-item label="Level">
                            <a-select v-decorator="[
                                'parent_id',
                                {
                                    initialValue: initialValue.edit_parent_id,
                                    rules: [
                                        {
                                            required: true,
                                            message: 'Please Choose one!',
                                        },
                                    ],
                                },
                            ]" placeholder="Choose level">
                                <a-select-option :value="0">
                                    Main Menu
                                </a-select-option>
                                <a-select-option :value="1">
                                    Sub Menu
                                </a-select-option>
                            </a-select>
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
                <a-form-item label="Status">
                    <a-select v-decorator="[
                        'active',
                        {
                            initialValue: initialValue.edit_active,
                            rules: [
                                {
                                    required: true,
                                    message: 'Status is not null!',
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
            url: '',
            contentIsError: false,
            content: '',
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
                edit_parent_id: 1,
                edit_description: '',
                edit_active: 1,
                edit_id: 1
            }
        }
    },
    methods: {
        getResuilt(row, page) {
            httpRequest
                .get('/api/menus/list?limit=' + row + '&page=' + page)
                .then(
                    ({ data }) => (
                        (this.menus = data.data),
                        (this.totalPage = data.total),
                        (this.lastPage = data.last_page),
                        (this.checkPage()),
                        (this.checkRow())
                    )
                );
        },
        handleRemove(file) {
            const index = this.fileList.indexOf(file);
            const newFileList = this.fileList.slice();
            newFileList.splice(index, 1);
            this.fileList = newFileList;
        },
        beforeUpload(file) {
            this.fileList = [...this.fileList, file];
            this.importMenu(this.fileList);
            return new Promise((resolve) => {})
        },
        showModalEdit(id) {
            this.getMenu(id);
            this.flagModalEdit = true
        },
        handleChangeContent() {
            if (this.content.length > 0) {
                this.contentIsError = false;
            }
            if (this.content.length === 0) {
                this.contentIsError = true;
            }
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
        showAddMenu() {
            this.flagModalAdd = true
        },
        addMenu(e) {
            e.preventDefault();
            this.form
                .validateFields((err, values) => {
                    if (err) return;
                    const formData = new FormData();
                    formData.append("name", values.name);
                    formData.append("description", values.description);
                    formData.append("content", this.content);
                    formData.append("active", values.active);
                    formData.append("parent_id", values.parent_id);
                    httpRequest.post('/api/menus/add', formData).then((response) => {
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
        deleteMenu() {
            httpRequest.delete('/api/menus/destroy/' + this.delete_id).then((response) => {
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
        getMenu(id) {
            httpRequest
                .get('/api/menus/edit/' + id)
                .then((response) => {
                    this.initialValue.edit_name = response.data.name;
                    this.initialValue.edit_parent_id = response.data.parent_id,
                        this.initialValue.edit_description = response.data.description,
                        this.content = response.data.content,
                        this.initialValue.edit_active = response.data.active
                    this.initialValue.edit_id = response.data.id
                })
        },
        editMenu(e) {
            e.preventDefault();
            this.form
                .validateFields((err, values) => {
                    if (err) return;
                    const formData = new FormData();
                    formData.append("name", values.name);
                    formData.append("parent_id", values.parent_id);
                    formData.append("description", values.description);
                    formData.append("content", this.content);
                    formData.append("active", values.active);
                    httpRequest
                        .post('/api/menus/edit/' + this.initialValue.edit_id + '?_method=PUT', formData)
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
        exportMenu() {
            httpRequest.get('/api/export/3', { responseType: 'arraybuffer' })
                .then((response) => {
                    var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                    var fileLink = document.createElement('a');
                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', 'menu.csv');
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
        importMenu(file) {
            const formData = new FormData();
            this.fileList.forEach((item, index) => {
                formData.append("file", item);
            });
            formData.append("type", 4);
            httpRequest.post('/api/import', formData)
                .then((response) => {
                    this.info = response
                    Toast.fire({
                        icon: 'success',
                        title: 'import Success'
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
        console.log('Component mounted.')
        this.getResuilt(this.row, this.page)
    },
    created() {
        this.$Progress.start()
        this.getResuilt(this.row, this.page)
        this.$Progress.finish()
    },
    filters: {
        truncate: function (text, length, suffix) {
            return text.substring(0, length) + suffix;
        },
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
    width: 100%;
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
    margin-left: calc(20% + 26px);
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
    