<template>
    <div class="container-fluid category-content">
        <h1 class="title">List Product</h1>
        <div class="content-body">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="center-text">No</th>
                        <th class="sortType center-text"><a @click="toggleSort('type')">File Type <i class="fas fa-sort"></i></a></th>
                        <th class="sortType center-text"><a @click="toggleSort('name')">File Name<i class="fas fa-sort"></i></a></th>
                        <th scope="col" class="center-text">Download</th>
                        <th class="sortType center-text"><a @click="toggleSort('created_at')">Upload Time <i
                                    class="fas fa-sort"></i></a></th>
                        <th class="sortType center-text"><a @click="toggleSort('user_id')">Handler <i class="fas fa-sort"></i></a>
                        </th>
                        <th class="sortType center-text"><a @click="toggleSort('status')">Result <i class="fas fa-sort"></i></a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in files" :key="item.id">
                        <th scope="row" class= "align-items-center center-text">{{ index + 1 }}</th>
                        <td scope="row" v-if="item.type == 1" class="align-items-center center-text">Product</td>
                        <td scope="row" v-if="item.type == 2" class="align-items-center center-text">Menu</td>
                        <td scope="row" class="align-items-center center-text">
                            {{ item.file_name !== null ? (item.file_name.length > 30 ? item.file_name.slice(0, 30) + '...' :
                                item.file_name) : '' }}
                        </td>
                        <td scope="row" class="align-items-center center-text">
                            <button @click="handleDownload(item.id, item.file_name)">
                                <font-awesome-icon :icon="['fas', 'fa-download']" />
                            </button>
                        </td>
                        <td scope="row" class="align-items-center center-text">{{ formatDateTime(item.created_at) }}</td>
                        <td scope="row" class="align-items-center center-text">{{ item.user.name }}</td>
                        <td v-if="item.status == 1" class="align-items-center center-text">
                            <span scope="row" class="btn btn-secondary btn-xs w-75 h-50">UnRead</span>
                        </td>
                        <td v-if="item.status == 2" class="align-items-center center-text">
                            <span scope="row" class="btn btn-danger btn-xs w-75 h-50">Failed</span>
                        </td>
                        <td v-if="item.status == 3" class="align-items-center center-text">
                            <span scope="row" class="btn btn-success btn-xs w-75 h-50">Success</span>
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
    </div>
</template>
  
<script>
import IconDelete from '../../icon/icon-delete.vue'
import IconEdit from '../../icon/icon-edit.vue'
import { VueEditor } from 'vue2-editor';
import httpRequest from '../../../axios'
import moment from 'moment';

export default {
    components: { IconDelete, IconEdit, VueEditor },
    data() {
        return {
            files: {},
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
        formatDateTime(dateTime) {
            return moment(dateTime).format('YYYY-MM-DD HH:mm:ss');
        },
        getResult(row, page, name = '', sorter = 'desc') {
            httpRequest
                .get('/api/files/listFile?limit=' + row + '&page=' + page + '&field=' + name + '&sortType=' + sorter)
                .then(
                    ({ data }) => (
                        (this.files = data.data),
                        (this.totalPage = data.paginate.total),
                        (this.lastPage = data.paginate.last_page),
                        (this.checkPage()),
                        (this.checkRow())
                    )
                );
        },
        toggleSort(name) {
            this.isSorter = !this.isSorter
            this.getResult(this.row, this.page, name, this.inputType);
        },
        beforeUpload(file) {
            this.fileList = [...this.fileList, file];
            return false;
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
        handleDownload(id, name) {
            httpRequest.get('/api/files/download/' + id, { responseType: 'arraybuffer' })
                .then((response) => {
                    var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                    var fileLink = document.createElement('a');
                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', name);
                    document.body.appendChild(fileLink);
                    fileLink.click();
                    Toast.fire({
                        icon: 'success',
                        title: 'Download Success'
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
    // display: block;
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
}</style>
