<template>
    <div class="container-fluid category-content">
        <h1>Form URL</h1>
        <div>
            <button @click="selectedTab = 'url'" :class="{ 'active': selectedTab === 'url' }"
                class="button-nav">URL</button>
            <button @click="selectedTab = 'image'" :class="{ 'active': selectedTab === 'image' }"
                class="button-nav">Image</button>
        </div>

        <div v-show="selectedTab === 'url'">
            <a-form :form="form">
                <a-form-item label="Url" :name="'url'" class="w-75"
                    :rules="[{ required: true, message: 'Please input your url!' }]">
                    <a-input v-decorator="[
                        'url',
                        { rules: [{ required: true, message: 'Please input your URL!' }] }
                    ]" />
                </a-form-item>
            </a-form>
            <div class="d-flex justify-content-center w-75">
                <button class="btn btn-xs btn-primary mt-3" style="width: 10%" @click="search">Submit</button>
            </div>
        </div>

        <div v-show="selectedTab === 'image'">
            <form @submit.prevent="searchImage">
                <label for="image">Image:</label>
                <input type="file" id="image" name="image" @change="handleImageChange">
                <button class="btn btn-xs btn-primary mt-3" style="width: 10%">Submit</button>
            </form>
            <div v-if="imageUrl">
                <img :src="imageUrl" alt="Uploaded Image" style="max-width: 100%; max-height: 300px;">
            </div>
        </div>
        <div class="row w-75 mt-5">
            <div v-for="(card, index) in searchArr" :key="index" class="col-md-4 mb-4">
                <div class="card" style="width: 100%; height: 100%;">
                    <img :src="card.bannerImage" class="card-img-top h-50" alt="...">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">episode: {{ card.episodes }}</h5>
                        <p class="card-text flex-grow-1">{{ card.title.english }}</p>
                        <a :href="card.siteUrl" target="_blank" class="btn btn-primary mt-auto">Link Video</a>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="isLoading" class="loading-icon d-flex justify-content-center align-items-center">
            <span>Loading...</span>
        </div>
    </div>
</template>

<script>
import httpRequest from '../../../axios'
export default {
    data() {
        return {
            form: this.$form.createForm(this),
            searchArr: [],
            selectedTab: 'url',
            imageUrl: null,
            selectedFile: null,
            isLoading: false
        };
    },
    methods: {
        search() {
            const urlValue = this.form.getFieldValue('url');
            this.isLoading = true;
            httpRequest.get('/api/files/getAnimeByUrl', {
                params: {
                    url: urlValue
                }
            })
                .then((response) => {
                    this.searchArr = response.data.data.data.Page.media;
                    if (this.searchArr.length === 0) {
                        Toast.fire({
                            icon: 'error',
                            title: 'Can not find Image'
                        });
                    }
                })
                .catch((error) => {
                    Toast.fire({
                        icon: 'error',
                        title: error
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                    Toast.fire({
                        icon: 'success',
                        title: 'Load Image Sucessfully'
                    });
                });
                
        },
         searchImage() {
             if (!this.selectedFile) {
                console.error('No image selected.');
                return;
            }
            this.isLoading = true;
            const formData = new FormData();
            formData.append('image', this.selectedFile);
            httpRequest.post('/api/files/getAnimeByImage', formData)
                .then((response) => {
                    this.searchArr = response.data.data.data.Page.media;
                    if (this.searchArr.length === 0) {
                        Toast.fire({
                            icon: 'error',
                            title: 'Can not find Image'
                        });
                    }
                })
                .catch((error) => {
                    Toast.fire({
                        icon: 'error',
                        title: error
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                    Toast.fire({
                        icon: 'success',
                        title: 'Load Image Sucessfully'
                    });
                });
        },
        handleImageChange(event) {
            const file = event.target.files[0];
            this.imageUrl = URL.createObjectURL(file);
            this.selectedFile = file;
        },
    },
};
</script>
<style lang="scss" scope>
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

    .button-nav {
        border: none;
        background-color: transparent;
        cursor: pointer;
        margin-right: 10px;
        padding: 5px 10px;
    }

    .active {
        background-color: #1280bf;
    }
    .loading-icon {
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-left-color: #7983ff;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        text-align: center;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
}
</style>