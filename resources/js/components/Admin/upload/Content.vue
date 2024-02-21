<template>
    <section>
        <div class="container category-content d-flex flex-column align-items-center mr-5 lg">

            <a-upload-dragger :multiple="true" :before-upload="beforeUpload" :on-success="onUploadSuccess"
                :on-error="onUploadError" class="w-50"
                :accept="'text/csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'">
                <p class="ant-upload-text">Click or drag file to this area to upload</p>
                <p class="ant-upload-hint">
                    Support for multiple file upload. Strictly prohibit from uploading company data or other banned files
                </p>
            </a-upload-dragger>

            <a-list class="file-list w-50">
                <a-list-item v-for="(file, index) in fileList" :key="index">
                    <a href="#" class="file-name">{{ file.name }}</a>
                    <a-icon type="delete" @click="deleteFile(index)" />
                </a-list-item>
            </a-list>

            <a-button class="button w-25" @click="submitFiles">Submit</a-button>

        </div>
    </section>
</template>
  
<script>
import { message } from 'ant-design-vue';
import axios from 'axios';

export default {
    data() {
        return {
            fileList: [],
        };
    },
    methods: {
        beforeUpload(file) {
            this.fileList = [...this.fileList, file];
        },
        deleteFile(index) {
            this.fileList.splice(index, 1);
        },
        submitFiles() {
            if (this.fileList.length === 0) {
                message.warning('Please select a file to upload');
                return;
            }
            const formData = new FormData();
            this.fileList.forEach((file) => {
                formData.append('file[]', file);
            });
            axios.post('/api/import-multiple', formData, {
                responseType: 'blob'
            })
                .then((response) => {
                    const url = URL.createObjectURL(new Blob([response.data], { type: 'application/zip' }));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'Super.zip');
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    Toast.fire({
                        icon: 'success',
                        title: 'Export Success'
                    });
                })
                .catch((error) => {
                    message.error('Upload error');
                });
        },
    },
};
</script>
  
<style>
.category-content {
    min-height: calc(100vh - 96px);
    margin-left: calc(22% + 10px);
    margin-right: calc(20% + 10px);
    padding: 20px 24px;
    border-radius: 5px;
    box-shadow: 0px 4px 4px rgb(0 0 0 / 25%);
    display: flex;
    flex-direction: column;
    color: #000;
    background-color: #fff;
    margin-top: 16px;
}

.file-list {
    margin-top: 16px;
}

.file-name {
    margin-right: 16px;
}

.ant-upload-list {
    display: none;
}
</style>