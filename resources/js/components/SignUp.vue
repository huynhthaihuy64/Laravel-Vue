<template>
  <div class="body">
    <div class="background">
      <div class="shape"></div>
      <div class="shape"></div>
    </div>
    <a-form :form="form" id="form-container" @submit="handleSubmit">
      <h3> {{ $store.getters.localizedStrings.register }}</h3>
      <a-form-item>
        <a-input v-decorator="[
          'name',
          {
            rules: [
              {
                required: true,
                message: 'Please input your Name!',
              },
            ],
          },
        ]" placeholder="Your Name">
        </a-input>
      </a-form-item>
      <a-form-item>
        <a-input v-decorator="[
          'email',
          {
            rules: [
              {
                type: 'email',
                message: 'The input is not valid E-mail!',
              },
              {
                required: true,
                message: 'Please input your E-mail!',
              },
            ],
          },
        ]" placeholder="Your Email">
        </a-input>
      </a-form-item>

      <a-form-item class="form-item">
        <a-input v-decorator="[
          'password',
          { rules: [{ required: true, message: 'Please input your Password!' }] },
        ]" :type="inputType" placeholder="Your Password">
        </a-input>
      </a-form-item>
      <a-form-item>
        <a-input v-decorator="[
          'phone',
          {
            rules: [
              {
                required: true,
                message: 'Please input your Phone!',
              },
            ],
          },
        ]" placeholder="Your Phone">
        </a-input>
      </a-form-item>
      <span class="incorrect">{{ errorMessage }}</span>
      <a-col :span="14">
        <a-form-item class="form-item">
          <a-checkbox @change="onToggleShowPassword" class="login-checkbox">
            {{ $store.getters.localizedStrings.showPassword }}
          </a-checkbox>
        </a-form-item>
      </a-col>
      <a-col :span="10">
        <a-button type="primary"><router-link to="/signIn">{{ $store.getters.localizedStrings.login
        }}</router-link></a-button>
      </a-col>
      <a-button type="primary" html-type="submit" class="login-form-button" :disabled="hasErrors(form.getFieldsError())">
        {{ $store.getters.localizedStrings.register }}
      </a-button>
      <div class="social">
        <a class="go" href="/api/redirect/google" target="_blank"><font-awesome-icon :icon="['fab', 'google']"
            class="mr-1" />Google
        </a>
        <a class="fb" href="/api/auth/facebook"><font-awesome-icon :icon="['fab', 'facebook']" /> Facebook</a>
      </div>
    </a-form>
  </div>
</template>
<script>
import { setUserInfo, setAccessToken } from '../auth'

const hasErrors = (fieldsError) => {
  return Object.keys(fieldsError).some(field => fieldsError[field]);
}

export default {
  beforeCreate() {
    this.form = this.$form.createForm(this, { name: 'login_form' })
  },
  data() {
    return {
      formData: {
        email: '',
        password: '',
        name: '',
        phone: '',
      },
      errorMessage: '',
      isShowPassword: false,
      hasErrors
    }
  },
  methods: {
    async handleSubmit(e) {
      this.textError = null
      e.preventDefault()
      this.form.validateFields((error, values) => {
        if (!error) {
          this.formData = { ...values }
          axios.post('/api/admin/users/register/store', this.formData).then((response) => {
            if (response.data.user) {
              setUserInfo(JSON.stringify(response.data.user))
              setAccessToken(response.data.success.access_token)
              Toast.fire({
                icon: 'success',
                title: '' + this.$store.getters.localizedStrings.login_success
              });
              this.$router.push({ name: 'Home' })
              window.axios.defaults.headers.common['Authorization'] =
                'Bearer ' + response.data.success.access_token
            } else {
              Toast.fire({
                icon: 'error',
                title: response.data.message
              });
            }
          }).catch(function (error) {
            if (error.response.data.errors.email) {
              Toast.fire({
                icon: 'error',
                title: error.response.data.errors.email
              });
            }
          });
        }
      })
        .catch(function (error) {
          Toast.fire({
            icon: 'error',
            title: error
          });
        })
    },
    onToggleShowPassword() {
      this.isShowPassword = !this.isShowPassword
    },
    socialLogin(provider) {
      axios.get('/api/redirect/google').then(response => {
        setUserInfo(JSON.stringify(response.data.user))
        setAccessToken(response.data.access_token)
        this.$router.push({ name: 'Home' })
        window.axios.defaults.headers.common['Authorization'] =
          'Bearer ' + response.data.access_token
      }).catch(err => {
      })
    }
  },
  computed: {
    inputType() {
      return this.isShowPassword ? 'text' : 'password'
    },
  },
}
</script>
<style lang="scss" scoped>
// @import 'https://fonts.gstatic.com';
@import 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';
@import 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap';

* *:before,
*:after {
  padding: 0;
  margin: 0;
  box-sizing: border-box;
}

.body {
  height: 100vh;
  background-color: #080710;
}

.background {
  width: 430px;
  height: 520px;
  position: absolute;
  transform: translate(-50%, -50%);
  left: 50%;
  top: 50%;
}

.background .shape {
  height: 200px;
  width: 200px;
  position: absolute;
  border-radius: 50%;
}

.shape:first-child {
  background: linear-gradient(#1845ad,
      #23a2f6);
  left: -80px;
  top: -80px;
}

.shape:last-child {
  background: linear-gradient(to right,
      #ff512f,
      #f09819);
  right: -30px;
  bottom: -80px;
}

form {
  height: 700px;
  width: 400px;
  background-color: rgba(255, 255, 255, 0.13);
  position: absolute;
  transform: translate(-50%, -50%);
  top: 50%;
  left: 50%;
  border-radius: 10px;
  backdrop-filter: blur(10px);
  border: 2px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 0 40px rgba(8, 7, 16, 0.6);
  padding: 50px 35px;
}

form * {
  font-family: 'Poppins', sans-serif;
  color: gray;
  letter-spacing: 0.5px;
  outline: none;
  border: none;
}

form h3 {
  font-size: 32px;
  font-weight: 500;
  line-height: 42px;
  text-align: center;
}

.login-form-button:hover {
  background: #56fc6c !important;
  border-color: #FC9C56 !important;
}

label {
  display: block;
  margin-top: 30px;
  font-size: 16px;
  font-weight: 500;
}

input {
  display: block;
  height: 50px;
  width: 100%;
  background-color: rgba(255, 255, 255, 0.07);
  border-radius: 3px;
  padding: 0 10px;
  margin-top: 8px;
  font-size: 14px;
  font-weight: 300;
}

::placeholder {
  color: #e5e5e5;
}

button {
  margin-top: 20px;
  width: 100%;
  height: 50px;
  background-color: #ffffff;
  color: #080710;
  padding: 15px 0;
  font-size: 18px;
  font-weight: 600;
  border-radius: 5px;
  cursor: pointer;
}

.social {
  margin-top: 30px;
  display: flex;
}

.social div {
  background: red;
  width: 150px;
  border-radius: 3px;
  padding: 5px 10px 10px 5px;
  background-color: rgba(255, 255, 255, 0.27);
  color: #eaf0fb;
  text-align: center;
}

.social div:hover {
  background-color: rgba(255, 255, 255, 0.47);
}

.social .fb {
  background: #56acfc !important;
  margin-left: 25px;
  text-align: center;
  padding: 10px;
  margin: 0 auto // căn giữa 
}

.social .go {
  background: #FC9C56 !important;
  margin-left: 25px;
  text-align: center;
  padding: 10px;
  margin: 0 auto // căn giữa 
}

.social i {
  margin-right: 4px;
}

.form-item {
  height: 40px;
}
</style>