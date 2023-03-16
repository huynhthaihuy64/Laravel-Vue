<template>
    <section>
        <div class="inner-header">
            <div class="container">
                <div class="pull-left">
                    <h6 class="inner-title">{{ $store.getters.localizedStrings.contact }}</h6>
                </div>
                <div class="pull-right">
                    <div class="beta-breadcrumb font-large">
                        <a href="/">{{$store.getters.localizedStrings.shop}}</a> / <span>{{ $store.getters.localizedStrings.contact }}</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="mt-4">
        </div>
        <div class="container">
            <div id="content" class="space-top-none">
                <div class="space50">&nbsp;</div>
                <div class="row">
                    <div class="col-sm-8">
                        <h2>{{$store.getters.localizedStrings.contact_page.form}}</h2>
                        <div class="space20">&nbsp;</div>
                        <p>{{$store.getters.localizedStrings.contact_page.description}}</p>
                        <div class="space20">&nbsp;</div>
                        <form class="contact-form" @submit.prevent="handleSubmit">
                            <div class="form-block">
                                <input v-model="form.name" type="text" placeholder="Your Name (required)" name="name">
                            </div>
                            <div class="form-block">
                                <input v-model="form.email" type="email" placeholder="Your Email (required)" name="email">
                            </div>
                            <div class="form-block">
                                <input v-model="form.subject" type="text" placeholder="Subject" name="subject">
                            </div>
                            <div class="form-block">
                                <textarea v-model="form.message" placeholder="Your Message" name="message"></textarea>
                            </div>
                            <div class="form-block">
                                <button type="submit" class="beta-btn primary">{{$store.getters.localizedStrings.contact_page.send}}<i
                                        class="fa fa-chevron-right"></i></button>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-4">
                        <h2>{{$store.getters.localizedStrings.contact_page.information}}</h2>
                        <div class="space20">&nbsp;</div>

                        <h6 class="contact-title">{{$store.getters.localizedStrings.contact_page.address}}</h6>
                        <p>
                            K448/67 Trưng Nữ Vương<br>
                            Hải Châu <br>
                            Đà Nẵng
                        </p>
                        <div class="space20">&nbsp;</div>
                        <h6 class="contact-title">{{$store.getters.localizedStrings.contact_page.business}}</h6>
                        <p>
                            Please Contact <br>
                            <a href="ch4ut1nhtr1@gmail.com">ch4ut1nhtr1@gmail.com</a>
                        </p>
                        <div class="space20">&nbsp;</div>
                        <h6 class="contact-title">{{$store.getters.localizedStrings.contact_page.employment.title}}</h6>
                        <p>
                            {{$store.getters.localizedStrings.contact_page.employment.content}} <br>
                            <a href="huyhuynh@gmail.com">huyhuynh@gmail.com</a>
                        </p>
                    </div>
                </div>
            </div> <!-- #content -->
        </div> <!-- .container -->
    </section>
</template>

<script>
import httpRequest from '../axios'

export default {
    data() {
        return {
            form: new Form({
                name: '',
                email: '',
                subject: '',
                message: '',
            })
        }
    },
    methods: {
        handleSubmit(e) {
            httpRequest.post('api/sendMailContact', this.form)
            .then((response) => {
                Toast.fire({
                icon: 'success',
                title: 'Send Mail Success'
              });
            })
            .catch(function (error) {
            Toast.fire({
              icon: 'error',
              title: error
            });
          });
        }
    },
    mounted() {
        console.log('Component mounted.')
    },
    created() {
        this.$Progress.start()
        this.$Progress.finish()
    },
}
</script>