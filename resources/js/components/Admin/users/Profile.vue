<template v-bind:class="{ 'dark': darkMode }">
  <section>
    <div class="wrapper">
      <!-- Content Wrapper. Contains page content -->
      <div class="content-fluid" style="background-color: #f4f6f9; margin-left: 15px;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="text-black">{{ $store.getters.localizedStrings.profile.title }}</h1>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><router-link to="/">{{ $store.getters.localizedStrings.shop }}</router-link>
                  </li>
                  <li class="breadcrumb-item active">{{ $store.getters.localizedStrings.profile.title }}</li>
                </ol>
              </div>
            </div>
          </div>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-3">

                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                  <div class="card-body box-profile">
                    <div class="text-center">
                      <a :href="initialValue.edit_avatar" target="_blank"><img
                          class="image-contain profile-user-img img-fluid img-circle" :src="`${initialValue.edit_avatar}`" /></a>
                    </div>

                    <h3 class="profile-username text-center">{{ initialValue.edit_name }}</h3>

                    <p class="text-muted text-center">{{ initialValue.edit_department }}</p>

                    <ul class="list-group list-group-unbordered mb-3">
                      <li class="list-group-item">
                        <b>{{ $store.getters.localizedStrings.profile.activity.followers }}</b> <a
                          class="float-right">1,234</a>
                      </li>
                      <li class="list-group-item">
                        <b>{{ $store.getters.localizedStrings.profile.activity.following }}</b> <a
                          class="float-right">567</a>
                      </li>
                      <li class="list-group-item">
                        <b>{{ $store.getters.localizedStrings.profile.activity.friends }}</b> <a
                          class="float-right text-size">∞</a>
                      </li>
                    </ul>

                    <a href="#" class="btn btn-primary btn-block"><b>{{
                      $store.getters.localizedStrings.profile.activity.follow }}</b></a>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->

                <!-- About Me Box -->
                <div class="card card-primary">
                  <div class="card-header">
                    <h3 class="card-title">{{ $store.getters.localizedStrings.profile.activity.about }}</h3>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <strong><i class="fas fa-venus-mars mr-1"></i> {{
                      $store.getters.localizedStrings.profile.profile.gender.title }}</strong>

                    <p class="text-muted">
                      {{ initialValue.edit_gender == 0 ? 'Male' : 'Female' }}
                    </p>

                    <hr>

                    <strong><i class="fas fa-map-marker-alt mr-1"></i> {{
                      $store.getters.localizedStrings.profile.profile.address }}</strong>

                    <p class="text-muted">{{ initialValue.edit_address }}</p>

                    <hr>

                    <strong><i class="fas fa-birthday-cake mr-1"></i>{{
                      $store.getters.localizedStrings.profile.profile.birthday }}</strong>

                    <p class="text-muted">
                      {{ initialValue.edit_birthday }}
                    </p>

                    <hr>

                    <strong><i class="fas fa-mobile mr-1"></i> {{ $store.getters.localizedStrings.profile.profile.phone
                    }}</strong>

                    <p class="text-muted">
                      {{ initialValue.edit_phone }}
                    </p>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
              <!-- /.col -->
              <div class="col-md-9">
                <div class="card">
                  <div class="card-header p-2">
                    <ul class="nav nav-pills">
                      <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">{{
                        $store.getters.localizedStrings.profile.activity.title }}</a></li>
                      <li class="nav-item"><a class="nav-link" href="#profile" data-toggle="tab">{{
                        $store.getters.localizedStrings.profile.profile.title }}</a></li>
                      <li class="nav-item"><a class="nav-link" href="#setting" data-toggle="tab">{{
                        $store.getters.localizedStrings.profile.setting }}</a></li>
                    </ul>
                  </div><!-- /.card-header -->
                  <div class="card-body">
                    <div class="tab-content">
                      <div class="active tab-pane" id="activity">
                        <!-- Post -->
                        <div class="post">
                          <div class="user-block">
                            <img class="image-contain img-circle img-bordered-sm" src="../../../../../public/dist/img/user1-128x128.jpg"
                              alt="user image">
                            <span class="username">
                              <a href="#">Jonathan Burke Jr.</a>
                              <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                            </span>
                            <span class="description">Shared publicly - 7:30 PM today</span>
                          </div>
                          <!-- /.user-block -->
                          <p>
                            Lorem ipsum represents a long-held tradition for designers,
                            typographers and the like. Some people hate it and argue for
                            its demise, but others ignore the hate as they create awesome
                            tools to help create filler text for everyone from bacon lovers
                            to Charlie Sheen fans.
                          </p>

                          <p>
                            <a href="#" class="link-black text-sm mr-2"><i class="fas fa-share mr-1"></i> Share</a>
                            <a href="#" class="link-black text-sm"><i class="far fa-thumbs-up mr-1"></i> Like</a>
                            <span class="float-right">
                              <a href="#" class="link-black text-sm">
                                <i class="far fa-comments mr-1"></i> Comments (5)
                              </a>
                            </span>
                          </p>

                          <input class="form-control form-control-sm" type="text" placeholder="Type a comment">
                        </div>
                        <!-- /.post -->

                        <!-- Post -->
                        <div class="post clearfix">
                          <div class="user-block">
                            <img class="image-contain img-circle img-bordered-sm" src="../../../../../public/dist/img/user7-128x128.jpg"
                              alt="User Image">
                            <span class="username">
                              <a href="#">Sarah Ross</a>
                              <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                            </span>
                            <span class="description">Sent you a message - 3 days ago</span>
                          </div>
                          <!-- /.user-block -->
                          <p>
                            Lorem ipsum represents a long-held tradition for designers,
                            typographers and the like. Some people hate it and argue for
                            its demise, but others ignore the hate as they create awesome
                            tools to help create filler text for everyone from bacon lovers
                            to Charlie Sheen fans.
                          </p>

                          <form class="form-horizontal">
                            <div class="input-group input-group-sm mb-0">
                              <input class="form-control form-control-sm" placeholder="Response">
                              <div class="input-group-append">
                                <button type="submit" class="btn btn-danger">Send</button>
                              </div>
                            </div>
                          </form>
                        </div>
                        <!-- /.post -->

                        <!-- Post -->
                        <div class="post">
                          <div class="user-block">
                            <img class="image-contain img-circle img-bordered-sm" src="../../../../../public/dist/img/user6-128x128.jpg"
                              alt="User Image">
                            <span class="username">
                              <a href="#">Adam Jones</a>
                              <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                            </span>
                            <span class="description">Posted 5 photos - 5 days ago</span>
                          </div>
                          <!-- /.user-block -->
                          <div class="row mb-3">
                            <div class="col-sm-6">
                              <img class="image-contain img-fluid" src="../../../../../public/dist/img/photo1.png" alt="Photo">
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-6">
                              <div class="row">
                                <div class="col-sm-6">
                                  <img class="image-contain img-fluid mb-3" src="../../../../../public/dist/img/photo2.png" alt="Photo">
                                  <img class="image-contain img-fluid" src="../../../../../public/dist/img/photo3.jpg" alt="Photo">
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-6">
                                  <img class="image-contain img-fluid mb-3" src="../../../../../public/dist/img/photo4.jpg" alt="Photo">
                                  <img class="image-contain img-fluid" src="../../../../../public/dist/img/photo1.png" alt="Photo">
                                </div>
                                <!-- /.col -->
                              </div>
                              <!-- /.row -->
                            </div>
                            <!-- /.col -->
                          </div>
                          <!-- /.row -->

                          <p>
                            <a href="#" class="link-black text-sm mr-2"><i class="fas fa-share mr-1"></i> Share</a>
                            <a href="#" class="link-black text-sm"><i class="far fa-thumbs-up mr-1"></i> Like</a>
                            <span class="float-right">
                              <a href="#" class="link-black text-sm">
                                <i class="far fa-comments mr-1"></i> Comments (5)
                              </a>
                            </span>
                          </p>

                          <input class="form-control form-control-sm" type="text" placeholder="Type a comment">
                        </div>
                        <!-- /.post -->
                      </div>

                      <div class="tab-pane" id="profile">
                        <a-form :form="form" @submit="handleSubmit">
                          <a-row :gutter="20">
                            <a-col :span="12">
                              <a-form-item :label="$store.getters.localizedStrings.profile.profile.name">
                                <a-input v-decorator="[
                                  'name',
                                  {
                                    initialValue: initialValue.edit_name,
                                    rules: [{ required: true, message: 'Please input your name!' }],
                                  },
                                ]" />
                              </a-form-item>
                              <a-form-item :label="$store.getters.localizedStrings.profile.profile.email">
                                <a-input v-decorator="['email',
                                  {
                                    initialValue: initialValue.edit_email,
                                    rules: [{ required: true, message: 'Please input your email!' }],
                                  },
                                ]" type="email" />
                              </a-form-item>
                              <a-form-item :label="$store.getters.localizedStrings.profile.profile.department">
                                <a-input v-decorator="[
                                  'department',
                                  {
                                    initialValue: initialValue.edit_department,
                                    rules: [{ required: true, message: 'Please input your name!' }],
                                  },
                                ]" />
                              </a-form-item>
                              <a-form-item :label="$store.getters.localizedStrings.profile.profile.address">
                                <a-input v-decorator="[
                                  'address',
                                  {
                                    initialValue: initialValue.edit_address,
                                    rules: [{ required: true, message: 'Please input your name!' }],
                                  },
                                ]" />
                              </a-form-item>
                            </a-col>
                            <a-col :span="12">
                              <a-form-item :label="$store.getters.localizedStrings.profile.profile.password">
                                <a-input v-decorator="[
                                  'password',
                                  {
                                    initialValue: '',
                                  },
                                ]" type="password" />
                              </a-form-item>
                              <a-form-item :label="$store.getters.localizedStrings.profile.profile.phone">
                                <a-input v-decorator="[
                                  'phone',
                                  {
                                    initialValue: initialValue.edit_phone,
                                  },
                                ]" />
                              </a-form-item>
                              <a-form-item name="birthday"
                                :label="$store.getters.localizedStrings.profile.profile.birthday">
                                <a-date-picker v-decorator="['birthday', {
                                  initialValue: initialValue.edit_birthday,
                                }]" value-format="YYYY-MM-DD" />
                              </a-form-item>
                              <a-form-item :label="$store.getters.localizedStrings.profile.profile.gender.title">
                                <a-select v-decorator="[
                                  'gender',
                                  {
                                    initialValue: initialValue.edit_gender,
                                    rules: [
                                      {
                                        required: true,
                                        message: 'Please Choose one!',
                                      },
                                    ],
                                  },
                                ]" placeholder="Choose level">
                                  <a-select-option :value="0">
                                    {{ $store.getters.localizedStrings.profile.profile.gender.male }}
                                  </a-select-option>
                                  <a-select-option :value="1">
                                    {{ $store.getters.localizedStrings.profile.profile.gender.female }}
                                  </a-select-option>
                                </a-select>
                              </a-form-item>
                            </a-col>
                          </a-row>
                          <a-form-item name="avatar" :label="$store.getters.localizedStrings.profile.profile.upload">
                            <a-upload name="avatar" v-decorator="['avatar']" :headers="headers"
                              :supportServerRender="false" :beforeUpload="beforeUpload"
                              action="//jsonplaceholder.typicode.com/posts/" list-type="picture">
                              <a-button> <a-icon
                                  type="upload" />{{ $store.getters.localizedStrings.profile.profile.upload }}</a-button>
                            </a-upload>
                            <a :href="initialValue.edit_avatar" target="_blank"><img class="image-contain fileUpdate"
                                :src="`${initialValue.edit_avatar}`" height="100px" /></a>
                          </a-form-item>
                          <a-form-item :label="$store.getters.localizedStrings.profile.profile.gender.album">
                            <a-upload v-decorator="['images']" name="images" :multiple="true"
                              :default-file-list="initialValue.fileList" :headers="headers"
                              action="//jsonplaceholder.typicode.com/posts/" list-type="picture">
                              <a-button> <a-icon type="upload" />Choose Your Album</a-button>
                            </a-upload>
                          </a-form-item>
                          <div class="d-flex justify-content-center">
                            <a-button html-type="submit" class="btn-danger">
                              {{ $store.getters.localizedStrings.profile.profile.save }}
                            </a-button>
                          </div>
                        </a-form>
                      </div>

                      <div class="tab-pane" id="setting">
                        <div class="container">
                          <div class="row">
                            <div class="col-md-6">
                              <h5>{{ $store.getters.localizedStrings.profile.prefer_language.title }}</h5> <br>
                              <p>{{ $store.getters.localizedStrings.profile.prefer_language.content }}</p>
                            </div>
                            <div class="col-md-6 d-flex justify-content-end">
                              <select v-model="selected" @click="switchLocale()" style="height:30px; width: 400px"
                                class="ml-5">
                                <option :selected="true" disabled>Choose Language</option>
                                <option value="en">English </option>
                                <option value="vi">Vietnamese </option>
                              </select>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <h5>{{ $store.getters.localizedStrings.dark_mode.title }}</h5> <br>
                              <p>{{ $store.getters.localizedStrings.dark_mode.content }}</p>
                            </div>
                            <div class="col-md-6 d-flex justify-content-end">
                              <a-switch @click="toggleDarkMode" />
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <h5>API Documentation</h5> <br>
                              <p>Show all necessary api</p>
                            </div>
                            <div class="col-md-6 d-flex justify-content-end">
                              <a @click="apiDocument">All API NECESSARY</a>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                  </div><!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->
      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
      </aside>
      <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
  </section>
  <!-- jQuery -->
</template>

<script>
import IconDelete from '../../icon/icon-delete.vue'
import IconEdit from '../../icon/icon-edit.vue'
import { reactive, computed, ref } from "vue";
import httpRequest from '../../../axios'

const isDark = ref();
isDark.value = localStorage.getItem("darkMode") == "true";
export default {
  components: { IconDelete, IconEdit },
  computed: {
    currentLocale() {
      return this.$store.state.currentLocale;
    },
  },
  data() {
    return {
      users: {},
      fileList: [],
      uploading: false,
      flagModalAdd: false,
      flagModalEdit: false,
      flagModalConfirm: false,
      flagModalDelete: false,
      selected: 'Choose Language',
      checked: false,
      headers: {
        "Content-Type": "multipart/form-data",
      },
      placeholderContent: 'Input content',
      name: '',
      form: this.$form.createForm(this, { name: 'coordinated' }),
      initialValue: {
        edit_name: '',
        edit_email: '',
        edit_password: '',
        edit_phone: '',
        edit_id: 1,
        edit_avatar: [],
        edit_department: '',
        edit_gender: '',
        edit_birthday: '',
        edit_address: '',
        fileList: [],
      }
    }
  },
  methods: {
    toggleDarkMode() {
      isDark.value = !isDark.value;
      localStorage.setItem("darkMode", isDark.value);
      setTimeout(function () {
        window.location.reload();
      }, 1000);
    },
    apiDocument() {
      this.$router.push({
        path: '/api/documentation',
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      }).then(() => {
          window.location.reload();
      });
    },
    getUser() {
      httpRequest
        .get('/api/admin/users/currentUser')
        .then((response) => {
          this.initialValue.edit_name = response.data.name;
          this.initialValue.edit_email = response.data.email;
          this.initialValue.edit_password = response.data.password;
          this.initialValue.edit_phone = response.data.phone;
          this.initialValue.edit_department = response.data.department;
          this.initialValue.edit_gender = response.data.gender;
          this.initialValue.edit_birthday = response.data.birthday;
          this.initialValue.edit_address = response.data.address;
          this.initialValue.edit_id = response.data.id;
          this.initialValue.edit_avatar = response.data.avatar;
          response.data.images.map((item, index) => {
            const file = {
              name: item.name,
              uid: item.id,
              url: item.path,
            };
            this.initialValue.fileList.push(file);
          });
        })
    },
    beforeUpload(avatar) {
      this.fileList = [...this.fileList, avatar];
      return false;
    },
    handleSubmit(e) {
      e.preventDefault();
      this.form
        .validateFields((err, values) => {
          if (err) {
            return;
          }
          const formData = new FormData();
          formData.append('name', values.name);
          formData.append('email', values.email);
          formData.append('password', values.password);
          formData.append('phone', values.phone);
          formData.append('department', values.department);
          formData.append('gender', values.gender);
          formData.append('birthday', values.birthday);
          formData.append('address', values.address);
          this.fileList.forEach((item, index) => {
            formData.append("avatar", item);
          });
          if (values.images) {
            const listFileUpload = values.images.fileList.filter(
              (e) => e.originFileObj
            );

            const listIdFileNotUpload = values.images.fileList.filter(
              (e) => !e.originFileObj
            );

            const fileDuplicate = this.initialValue.fileList.filter((item) => {
              return listIdFileNotUpload.some((e) => e.uid === item.uid);
            });

            const fileRemoved = this.initialValue.fileList.filter((item) => {
              return fileDuplicate.every((e) => e.uid !== item.uid);
            });

            fileRemoved.forEach((item, index) => {
              formData.append('removeFiles[]', item.uid);
            });

            listFileUpload.forEach((item, index) => {
              formData.append('images[]', item.originFileObj);
            });
          }
          httpRequest.post('/api/admin/users/updateCurrentUser', formData)
            .then((response) => {
              this.info = response
              this.flagModalEdit = false
              this.form.resetFields()
              Toast.fire({
                icon: 'success',
                title: 'Edit Success'
              });
              setTimeout(function () {
                window.location.reload();
              }, 2000);
            })
        })
        .catch(function (error) {
          return Promise.reject(error);
        });
    },
    switchLocale() {
      this.$store.state.currentLocale = this.selected;
    },
  },
  mounted() {
    this.getUser()
  },
  created() {
    this.$Progress.start()
    this.$Progress.finish()
  },
}
</script>
<style scoped>@import 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback';
@import '../../../../../public/plugins/fontawesome-free/css/all.min.css';

.text-size {
  font-size: 1.8rem;
}

;

.light-mode {
  background-color: #ffffff;
  color: #333333;
}

.dark-mode {
  background-color: #282c34;
  color: #e6e6e6;
}</style>
<style scoped src="../../../../../public/dist/css/adminlte.min.css"></style>