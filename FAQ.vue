<template>
    <div class="container-fluid page-content">
        <div class="row">
          <div class="col-12">
            <div class="card" v-if="$gate.isAdmin()">
              <div class="card-header">
                <h3 class="card-title">Danh sách câu hỏi thường gặp</h3>

                <div class="card-tools">
                  <a-button type="primary" @click="newModal">
                    <a-icon type="plus" />
                    Thêm bài viết
                  </a-button>
                </div>
              </div>
              <!-- /.card-header -->
                  <table class="table">
                    <thead>
                      <tr>
                          <th scope="col" class="category-order">STT</th>
                          <th scope="col">Tiêu Đề</th>
                          <th scope="col">Chi Tiết</th>
                          <th scope="col">Trạng Thái</th>
                          <th scope="col">Người Đăng</th>
                          <th scope="col">Ngày Đăng</th>
                          <th scope="col"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="page in pages.data" :key="page.id">
                          <th scope="row">{{ page.id }}</th>
                          <td>{{page.title}}</td>
                          <td>{{page.description | truncate(30, '...')}}</td>
                          <td v-if="page.status == 0"><span class="btn btn-danger btn-xs">InActive</span></td>
                          <td v-if="page.status == 1"><span class="btn btn-success btn-xs">Active</span></td>
                          <td>{{page.init_date}}</td>
                          <td>{{page.user.name}}</td>
                          <td>
                              <a href="#" @click="editModal(page)">
                                  <i class="fa fa-edit blue"></i>
                              </a>
                              /
                              <a href="#" @click="deletePage(page.id)">
                                  <i class="fa fa-trash red"></i>
                              </a>
                          </td>
                      </tr>
                    </tbody>
                  </table>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="pages" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>

        <div v-if="!$gate.isAdmin()">
            <not-found></not-found>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode">Create New Page</h5>
                    <h5 class="modal-title" v-show="editmode">Update Page's Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createPage"> -->

                <form @submit.prevent="editmode ? updatePage() : createPage()" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Title</label>
                            <input v-model="form.title" type="text" name="title"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                            <has-error :form="form" field="title"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea v-model="form.description" type="text" name="description"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('description') }"> </textarea>
                            <has-error :form="form" field="description"></has-error>
                        </div>
                    
                        <div class="form-group">
                            <label>Content</label>
                            <textarea v-model="form.content" type="text" name="content"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('content') }"> </textarea>
                            <has-error :form="form" field="content"></has-error>
                        </div>
                    
                        <div class="form-group">
                            <label>Files</label>
                            <input type="file" name="files[]" v-on:change="onFileChange"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('files') }" multiple>
                            <has-error :form="form" field="type"></has-error>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                        <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data () {
            return {
                editmode: false,
                pages : {},
                form: new Form({
                    id: '',
                    title : '',
                    description: '',
                    content: '',
                })
            }
        },
        methods: {
            onFileChange(e) {
                  console.log(e.target.files);
                  this.file = e.target.files;
            },
            getResults(page = 1) {
                  this.$Progress.start();
                  axios.get('api/page?page=' + page).then(({ data }) => (this.pages = data.data));
                  this.$Progress.finish();
            },
            updatePage(){
                this.$Progress.start();
                console.log('FormData',this.form);
                // console.log('Editing data');
                let formData = new FormData();

                for(let i=0; i<this.file.length; i++){
                  formData.append('files[]', this.file[i])
                }
                formData.append('_method', 'PUT');
                _.each(this.form, (value, key) => {
                  formData.append(key, value)
                });
                console.log('Data',formData)
                axios.post('api/page/'+this.form.id,formData,{
                  headers: { 'content-type': 'multipart/form-data' }
                })
                .then((response) => {
                    // success
                    $('#addNew').modal('hide');
                    Toast.fire({
                      icon: 'success',
                      title: response.data.message
                    });
                    this.$Progress.finish();
                        //  Fire.$emit('AfterCreate');

                    this.loadPages();
                })
                .catch(() => {
                    this.$Progress.fail();
                });

            },
            editModal(page){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(page);
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            deletePage(id){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {

                        // Send request to the server
                         if (result.value) {
                                this.form.delete('api/page/'+id).then(()=>{
                                        Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                        );
                                    // Fire.$emit('AfterCreate');
                                    this.loadPages();
                                }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                         }
                    })
            },
          loadPages(){
            this.$Progress.start();

            if(this.$gate.isAdmin()){
              axios.get("api/page").then(({ data }) => (this.pages = data.data));
            }
            this.$Progress.finish();
          },
          createPage(){
              console.log('data',this.file.length);
              console.log('Form',this.form);
              let formData = new FormData();

              for(let i=0; i<this.file.length; i++){
                formData.append('files[]', this.file[i])
              }
              _.each(this.form, (value, key) => {
                formData.append(key, value)
              });
              console.log('Data',formData);
              axios.post('api/page',formData, {
                headers: { 'content-type': 'multipart/form-data' }
              })
              .then((response)=>{
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: response.data.message
                  });

                  this.$Progress.finish();
                  this.loadPages();

              })
              .catch(()=>{

                  Toast.fire({
                      icon: 'error',
                      title: 'Some error occured! Please try again'
                  });
              })
          }

        },
        mounted() {
            console.log('page Component mounted.')
        },
        created() {

            this.$Progress.start();
            this.loadPages();
            this.$Progress.finish();
        },
  filters: {
      truncate: function (text, length, suffix) {
      return text.substring(0, length) + suffix;
      },
  },
    }
</script>

<style lang="scss" scoped>
.page-content {
height: 100%;
flex: 4;
padding: 20px 24px;
border-radius: 5px;
box-shadow: 0px 4px 4px rgb(0 0 0 / 25%);
display: flex;
flex-direction: column;
color: #000;
background-color: #fff;
.ant-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
  margin-bottom: 18px;
  float: right;
  width: 147px;
  padding: 6px;
}
.ant-btn-primary {
  background-color: #1280bf;
}
}
.content-body {
width: 60%;
}

.title {
font-size: 24px;
font-weight: 700;
color: #000;
margin-bottom: 18px;
}
</style>