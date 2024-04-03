<template>
    <section>
        <div class="container">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card chat-app">
                        <div id="plist" class="people-list my-0 py-0">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Search...">
                            </div>
                            <ul class="list-unstyled chat-list mt-2 mb-0">
                                <li class="clearfix" v-for="(friend, index) in friends" :key="friend.id"
                                    @click="showChat(index, friend.id)">
                                    <div class="position-relative">
                                        <div class="pl-5 col-6" v-if="friend.avatar">
                                            <img :src="friend.avatar" alt="avatar" height="40px">
                                        </div>
                                        <div class="pl-5 col-6" v-if="!friend.avatar">
                                            <img src="../../../../storage/app/public/avatar/reynolds.immanuel.jpg"
                                                height="40px">
                                        </div>
                                        <span
                                            class="bg-danger text-light rounded-circle px-2 py-1 chat-badge-counter position-absolute top-0 end-0"
                                            v-if="friend.unseen_messages && friend.unseen_messages.length > 0">
                                            {{ friend.unseen_messages.length }}
                                        </span>
                                    </div>
                                    <div class="about">
                                        <div class="name">{{ friend.name }}</div>
                                        <div class="status"> <i class="fa fa-circle offline"></i> left 7 mins ago </div>
                                    </div>
                                </li>
                            </ul>
                            <a-button type="primary" @click="showAddMenu()" class="button-type mt-3 mb-2">
                                <a-icon type="plus" />
                                Add Friend
                            </a-button>
                        </div>
                        <div class="chat" v-if="activeFriendIndex !== null">
                            <div class="chat-header clearfix">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <a href="javascript:void(0);" data-toggle="modal" data-target="#view_info"
                                            v-if="friend.avatar">
                                            <img :src="friend.avatar" alt="avatar" height="40px">
                                        </a>
                                        <a href="javascript:void(0);" data-toggle="modal" data-target="#view_info"
                                            v-if="!friend.avatar">
                                            <img src="../../../../storage/app/public/avatar/reynolds.immanuel.jpg"
                                                height="40px">
                                        </a>
                                        <div class="chat-about">
                                            <h6 class="m-b-0">{{ friend.name }}</h6>
                                            <small>Last seen: 2 hours ago</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 d-flex hidden-sm text-right">
                                        <a href="javascript:void(0);" class="btn btn-outline-secondary"><i
                                                class="fa fa-camera"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-outline-primary"><i
                                                class="fa fa-image"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-outline-info"><i
                                                class="fa fa-cogs"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-outline-warning"><i
                                                class="fa fa-question"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="chat-history" ref="chatHistory" @scroll="onScroll">
                                <div v-if="showLoader" class="loader mt-3 d-flex justify-align-center">
                                    <div class="loader-inner"></div>
                                </div>
                                <ul class="m-b-0">
                                    <li class="clearfix" v-for="(chat) in chats" :key="chat.id" v-bind:class="{
                                        'clearfix text-right': chat.user_id === user.id,
                                        'clearfix': chat.user_id === friend.id
                                    }">
                                        <div class="message-data">
                                            <span class="message-data-time">{{ chat.created_at }}</span>
                                            <img :src="getAvatar(chat, user.id, friend.id)" alt="avatar">
                                        </div>
                                        <div class="message other-message" v-html="chat.chat"></div>
                                    </li>
                                </ul>
                            </div>
                            <EmojiSelect v-if="emojiBtnClicked" @onSelect="handleSelectEmoji"
                                @onClose="emojiBtnClicked = false" />
                            <div class="chat-message clearfix">
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-send"></i></span>
                                    </div>
                                    <button class="text-xl font-bold text-gray-600 mx-1" type="button" title="add emoji"
                                        @click="showEmojiList">&#128512;</button>
                                    <input type="text" class="form-control" placeholder="Enter text here..."
                                        v-model="message" @keyup.enter="sendChat(user.id, friend.id)">
                                </div>
                            </div>
                        </div>
                        <a-modal title="Add new friend" :visible="this.flagModalAdd" @cancel="() => cancelModalAdd()">
                            <template #footer>
                                <a-button class="btn-button-cancel" @click="cancelModalAdd">Cancel</a-button>
                            </template>
                            <div class="dropdown w-100">
                                <div class="row">
                                    <div class="col-12">
                                        <input type="text" placeholder="Search.." v-model="searchFriend" id="myInput"
                                            @keyup="search()" class="h-100 w-100">
                                    </div>
                                    <div class="mt-3 col-12" v-for="(item) in searchArr" :key="item.id">
                                        <a-popconfirm class="w-100 content-dropdown"
                                            title="Are you sure add this friend?" ok-text="Yes" cancel-text="No"
                                            @confirm="addFriend(item.id)" @cancel="cancel">
                                            <div class="row">
                                                <div class="pl-5 col-6" v-if="item.avatar">
                                                    <img :src="item.avatar" class="h-50 w-100">
                                                </div>
                                                <div class="pl-5 col-6" v-if="!item.avatar">
                                                    <img src="../../../../storage/app/public/avatar/reynolds.immanuel.jpg"
                                                        class="h-50 w-100">
                                                </div>
                                                <div class="col-6">
                                                    <b class="ml-4">{{ item.name }}</b><br />
                                                    <p class="ml-4 mt-4">{{ item.phone }}</p>
                                                </div>
                                            </div>
                                        </a-popconfirm>
                                    </div>
                                </div>
                            </div>
                        </a-modal>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import httpRequest from '../../axios'
import { message } from 'ant-design-vue';
import Echo from 'laravel-echo';
import { getUserInfo } from '../../auth'
import { ref, onMounted } from 'vue';
import EmojiSelect from "./EmojiSelect.vue";
export default {
    components: { EmojiSelect },
    setup() {
        const confirm = (e) => {
            message.success('Click on Yes');
        };
        const searchFriend = ref('');
        const cancel = (e) => {
            message.error('Click on No');
        };
        const user = ref(JSON.parse(getUserInfo()));
        const onlineUsers = ref([]);
        const chatContentRef = ref(null);
        const limit = ref(10);
        const chats = ref([]);
        const chatHistoryRef = ref(null);
        const emojiBtnClicked = ref(false);
        function showEmojiList() {
            emojiBtnClicked.value = true;
        }
        async function getOnlineUsers() {
            const result = await httpRequest
                .get('/api/users/users-online');
            if (result) {
                onlineUsers.value = result.data
            }
        }
        const displayToastMessage = (message) => {
            Toast.fire({
                icon: 'success',
                title: '' + message
            });
        };
        getOnlineUsers();

        const getChats = (id) => {
            httpRequest.post('/api/history/' + id + '?limit=' + limit.value)
                .then((data) => {
                    chats.value = data.data;
                }).catch(error => {
                    console.error('Error fetching chat history:', error);
                });
        };

        const playChatTone = () => {
            const audio = new Audio('/template/sound/grunting_notification.mp3'); // Thay đổi đường dẫn tới tệp âm thanh của bạn
            audio.play();
        }

        onMounted(() => {
            if (user) {
                window.Echo.private(`messages.${user.value.id}`).listen('\\App\\Events\\ChatRoomBroadCast', e => {
                    getChats(e.message.user_id)
                    displayToastMessage('Bạn có tin nhắn mới');
                    playChatTone();
                });
            }
        });

        return {
            confirm,
            cancel,
            user,
            searchFriend,
            users: onlineUsers,
            chatContentRef,
            getChats,
            limit,
            chats,
            chatHistoryRef,
            displayToastMessage,
            emojiBtnClicked,
            showEmojiList,
        };
    },
    data() {
        return {
            friends: {},
            users: {},
            friend: {},
            message: '',
            flagModalAdd: false,
            activeFriendIndex: null,
            CountData: 10,
            pageSize: 10,
            lastPage: '',
            totalPage: 2,
            page: 1,
            row: 10,
            meta: {
                "from": 1,
                "to": 10
            },
            searchArr: {},
            history_id: 1,
            autoScroll: true,
            showLoader: false,
            loadingMore: false,
            apiCalled: false,
        }
    },
    methods: {
        getListFriends() {
            httpRequest
                .get('/api/list-friend')
                .then((data) => {
                    this.friends = data.data;
                })
        },
        handleSelectEmoji(emojiHtml) {
            this.message += emojiHtml; // Thêm emoji vào message
        },
        showChat(index, id) {
            this.activeFriendIndex = index;
            this.history_id = id;
            this.getFriend(id)
            this.getChats(id)
            this.getListFriends()
        },
        getFriend(id) {
            httpRequest
                .get('/api/chat/' + id)
                .then((data) => {
                    this.friend = data.data;
                })
        },
        getAvatar(chat, userId, friendId) {
            if (chat.user_id === userId) {
                return this.user.avatar;
            } else if (chat.user_id === friendId) {
                return this.friend.avatar;
            }
        },
        sendChat(userId, friendId) {
            const formData = new FormData();
            formData.append("user_id", userId);
            formData.append("friend_id", friendId);
            formData.append("chat", this.message);
            httpRequest
                .post('/api/chat-room/sendChat', formData).then((response) => {
                    this.info = response
                    this.flagModalAdd = false
                    Toast.fire({
                        icon: 'success',
                        title: '' + this.$store.getters.localizedStrings.user_management.add_user.success
                    });
                })
            this.message = '';
            this.getChats(friendId)
        },
        showAddMenu() {
            this.flagModalAdd = true
        },
        cancelModalAdd() {
            this.flagModalAdd = false
            this.searchFriend = '';
        },
        search() {
            httpRequest.get('/api/search-friend?keyword=' + this.searchFriend)
                .then((response) => {
                    this.searchArr = response.data;
                });
        },
        addFriend(friendId) {
            const formData = new FormData();
            formData.append("friend_id", friendId);
            httpRequest.post('/api/add-friend', formData).then((response) => {
                this.info = response
                this.flagModalAdd = false
                this.getResult(this.row, this.page)
                Toast.fire({
                    icon: 'success',
                    title: 'Add Success'
                });
            })
            this.searchFriend = '';
        },
        onScroll() {
            let chatHistory = this.$refs.chatHistory;
            if (chatHistory.scrollTop + chatHistory.clientHeight >= chatHistory.scrollHeight && !this.apiCalled) {
                this.apiCalled = true; // Đánh dấu rằng API đã được gọi
                this.loadingMore = true;
                this.showLoader = true;

                setTimeout(() => {
                    this.limit += 10;
                    this.getChats(this.history_id);
                    this.showLoader = false;
                    this.loadingMore = false;
                    this.apiCalled = false; // Đặt lại biến sau khi kết thúc xử lý API
                }, 1000);
            }
        },
    },
    mounted() {
        this.$refs.chatHistory.addEventListener('scroll', this.onScroll);
    },
    beforeDestroy() {
        // Trước khi component bị destroy, gỡ bỏ lắng nghe sự kiện scroll
        this.$refs.chatHistory.removeEventListener('scroll', this.onScroll);
    },
    created() {
        this.getListFriends()
    },
}
</script>
<style>
body {
    background-color: #f4f7f6;
    margin-top: 20px;
}

.card {
    background: #fff;
    transition: .5s;
    border: 0;
    margin-bottom: 30px;
    border-radius: .55rem;
    position: relative;
    width: 100%;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 10%);
}

.chat-app .people-list {
    width: 280px;
    position: absolute;
    left: 0;
    top: 0;
    padding: 20px;
    z-index: 7
}

.chat-app .chat {
    margin-left: 280px;
    border-left: 1px solid #eaeaea
}

.people-list {
    -moz-transition: .5s;
    -o-transition: .5s;
    -webkit-transition: .5s;
    transition: .5s
}

.people-list .chat-list li {
    padding: 10px 15px;
    list-style: none;
    border-radius: 3px
}

.people-list .chat-list li:hover {
    background: #efefef;
    cursor: pointer
}

.people-list .chat-list li.active {
    background: #efefef
}

.people-list .chat-list li .name {
    font-size: 15px
}

.people-list .chat-list img {
    width: 45px;
    border-radius: 50%
}

.people-list img {
    float: left;
    border-radius: 50%
}

.people-list .about {
    float: left;
    padding-left: 8px
}

.people-list .status {
    color: #999;
    font-size: 13px
}

.chat .chat-header {
    padding: 15px 20px;
    border-bottom: 2px solid #f4f7f6
}

.chat .chat-header img {
    float: left;
    border-radius: 40px;
    width: 40px
}

.chat .chat-header .chat-about {
    float: left;
    padding-left: 10px
}

.chat .chat-history {
    padding: 20px;
    border-bottom: 2px solid #fff
}

.chat .chat-history ul {
    padding: 0
}

.chat .chat-history ul li {
    list-style: none;
    margin-bottom: 30px
}

.chat .chat-history ul li:last-child {
    margin-bottom: 0px
}

.chat .chat-history .message-data {
    margin-bottom: 15px
}

.chat .chat-history .message-data img {
    border-radius: 40px;
    width: 40px
}

.chat .chat-history .message-data-time {
    color: #434651;
    padding-left: 6px
}

.chat .chat-history .message {
    color: #444;
    padding: 18px 20px;
    line-height: 26px;
    font-size: 16px;
    border-radius: 7px;
    display: inline-block;
    position: relative
}

.chat .chat-history .message:after {
    bottom: 100%;
    left: 7%;
    border: solid transparent;
    content: " ";
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
    border-bottom-color: #fff;
    border-width: 10px;
    margin-left: -10px
}

.chat .chat-history .my-message {
    background: #efefef
}

.chat .chat-history .my-message:after {
    bottom: 100%;
    left: 30px;
    border: solid transparent;
    content: " ";
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
    border-bottom-color: #efefef;
    border-width: 10px;
    margin-left: -10px
}

.chat .chat-history .other-message {
    background: #e8f1f3;
    text-align: right
}

.chat .chat-history .other-message:after {
    border-bottom-color: #e8f1f3;
    left: 93%
}

.chat .chat-message {
    padding: 20px
}

.online,
.offline,
.me {
    margin-right: 2px;
    font-size: 8px;
    vertical-align: middle
}

.online {
    color: #86c541
}

.offline {
    color: #e47297
}

.me {
    color: #1d8ecd
}

.float-right {
    float: right
}

.clearfix:after {
    visibility: hidden;
    display: block;
    font-size: 0;
    content: " ";
    clear: both;
    height: 0
}

.dropdown {
    position: relative;
    display: inline-block;
    z-index: 999;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f6f6f6;
    min-width: 300px;
    border: 1px solid #ddd;
    z-index: 1;
}

.dropdown-category {
    display: none;
    position: absolute;
    background-color: #f6f6f6;
    min-width: 200px;
    border: 1px solid #ddd;
    z-index: 1;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover {
    background-color: #f1f1f1
}

.content-dropdown {
    max-height: 150px;
}

.chat-history {
    overflow-y: scroll;
    /* Kích hoạt thanh cuộn dọc */
    max-height: 500px;
    /* Điều chỉnh chiều cao tối đa để thanh cuộn hiển thị */
    /* Các thiết lập CSS khác */
    scroll-behavior: smooth;
}

.chat-badge-counter {
    top: -10px;
}

.loader {
    position: relative;
    width: 64px;
    height: 64px;
    margin: auto;
}

.loader-inner {
    position: absolute;
    left: 6px;
    top: 6px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(white, #e0e0e0, white);
    animation: loader-animation 1.2s linear infinite;
}

@keyframes loader-animation {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

@media only screen and (max-width: 767px) {
    .chat-app .people-list {
        height: 465px;
        width: 100%;
        overflow-x: auto;
        background: #fff;
        left: -400px;
        display: none
    }

    .chat-app .people-list.open {
        left: 0
    }

    .chat-app .chat {
        margin: 0
    }

    .chat-app .chat .chat-header {
        border-radius: 0.55rem 0.55rem 0 0
    }

    .chat-app .chat-history {
        height: 300px;
        overflow-x: auto
    }
}

@media only screen and (min-width: 768px) and (max-width: 992px) {
    .chat-app .chat-list {
        height: 650px;
        overflow-x: auto
    }

    .chat-app .chat-history {
        height: 600px;
        overflow-x: auto
    }
}

@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: landscape) and (-webkit-min-device-pixel-ratio: 1) {
    .chat-app .chat-list {
        height: 480px;
        overflow-x: auto
    }

    .chat-app .chat-history {
        height: calc(100vh - 350px);
        overflow-x: auto
    }
}
</style>