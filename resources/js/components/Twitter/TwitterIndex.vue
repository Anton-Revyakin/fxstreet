<template>
    <div class="container-fluid h-100">
        <v-dialog name="dialog"/>
        <div class="row bgDark" id="header">
            <div class="col-3">
                <div class="row align-items-center">
                    <div class="col text-center">
                        <router-link to="/news" class="text-decoration-none small" style="color: #e4871b;">
                            <font-awesome-icon :icon="['far', 'newspaper']" style="font-size: 1.7rem;"/>
                            <br/>
                            FxStreet
                        </router-link>
                    </div>
                    <div class="col text-center">
                        <a href="#" @click.prevent="showVCalendar = !showVCalendar" class="text-white text-decoration-none small">
                            <font-awesome-icon :icon="['fas', 'calendar-day']" class="text-white" style="font-size: 1.7rem;"/>
                            <br/>
                            Календарь
                        </a>
                    </div>
                    <div class="col text-center"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="row h-100">
                    <div class="col-3 border-right" id="left_menu">
                        <overlay-scrollbars class="height">
                            <div class="bgDark" v-show="showVCalendar">
                                <v-date-picker
                                        is-expanded
                                        mode='single'
                                        is-inline
                                        color='gray'
                                        is-dark
                                        v-model=vcDate
                                        :available-dates="vcAvailableDates"
                                        :value=null
                                        style="border: none;">
                                </v-date-picker>
                            </div>
                            <div v-show="showTitlesLoader" class="text-muted small text-center" style="margin-top: 15px;">ЗАГРУЗКА СПИСКА...</div>
                            <ul class="list-group" v-show="!showTitlesLoader">
                                <li class="border-bottom"
                                    v-for="(tweetItem) in tweetsList"
                                    :key="tweetItem.id">
                                    <router-link :to="{name : 'twitter', params: {id : tweetItem.id}}" :event="[loadingBody ? '' : 'click']" class="text-decoration-none">
                                        <div>
                                            <img :src="tweetItem.image" alt="">
                                        </div>
                                        <div class="text-dark text-nowrap ellipsis border-info" :style="[!isShown(tweetItem.id) ? {'border-right' : '4px solid'} : '']">
                                            <span v-html="tweetItem.title"></span>
                                            <br>
                                            <span class="small text-black-50">{{ tweetItem.published_at }}</span>
                                        </div>
                                    </router-link>
                                </li>
                            </ul>
                        </overlay-scrollbars>
                    </div>
                    <div class="col-9" id="content">
                        <overlay-scrollbars class="height col">
                            <div v-if="Object.keys(tweetBody).length > 0">
                                <h4 style="margin-top: 16px;" v-html="tweetBody.body"></h4>
                                <div class="small text-black-50">Ссылка на твит: <a :href="'https://twitter.com' + tweetBody.full_url" target="_blank">https://twitter.com{{ tweetBody.full_url }}</a></div>
                            </div>
                        </overlay-scrollbars>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    let currDate = null;
    let showDate = null;
    let storageShown = [];
    let indexById = {};

    export default {
        name   : "Tweets",
        data() {
            return {
                tweetsList: [],

                showVCalendar   : false,
                vcDate          : new Date(),
                vcAvailableDates: null,

                showTitlesLoader: true,

                loadingTitles: false,
                loadingBody  : false,
                tweetBody    : {}
            }
        },
        mounted() {
            showDate = currDate = this.$moment(new Date()).format('YYYYMMDD');
            this.getShown();
            this.getTweetsTitles(() => {
                this.getTweetBodyLastShown();
            });
            setInterval(() => {
                this.getTweetsTitles();
            }, 30000);
        },
        methods: {
            /**
             * Модальное окно сообщений
             */
            showModal(title, text) {
                this.$modal.show('dialog', {
                    'title': title,
                    'text' : '<div class="text-center text-danger">' + text + '</div>',
                    buttons: [{
                        'title': 'Закрыть'
                    }]
                });
            },
            /**
             * Получить данные из Хранилища
             */
            getShown() {
                //Получить список id прочитанных новостей из хранилища
                storageShown = localStorage.getItem(this.$options.name + '_reads');
                storageShown = null === storageShown ? {} : JSON.parse(storageShown);

                //Создать объект с текущей датой, если еще нет или есть, но старый
                if (!storageShown.hasOwnProperty(currDate)) storageShown = {[currDate]: []};
            },
            /**
             * Было ли показано
             */
            isShown(tweetId) {
                if (showDate !== currDate) return true;

                return storageShown[currDate].indexOf(tweetId) !== -1;
            },
            /**
             * Сохранить данные в Хранилище
             */
            saveShown(tweetId) {
                storageShown[currDate].push(tweetId);
                localStorage.setItem(this.$options.name + '_reads', JSON.stringify(storageShown));
            },
            /**
             * Выключить календарь
             */
            disableCalendar() {
                this.vcAvailableDates = {
                    start: this.vcDate,
                    end  : this.vcDate
                }
            },
            /**
             * Включить календарь
             */
            enableCalendar() {
                this.vcAvailableDates = {
                    start: null,
                    end  : new Date()
                }
            },
            /**
             * Получить список твитов
             */
            getTweetsTitles(cb) {
                if (this.loadingTitles) return;

                this.loadingTitles = true;
                this.disableCalendar();

                axios.get('/twitter/?date=' + this.$moment(this.vcDate).format('YYYY-MM-DD'))
                    .then((response) => {
                        this.loadingTitles = false;
                        this.showTitlesLoader = false;
                        this.enableCalendar();
                        this.compactTweetsList(response.data);

                        if (undefined !== cb) cb();
                    })
                    .catch((e) => {
                        this.loadingTitles = false;
                        this.showTitlesLoader = false;
                        this.enableCalendar();
                        this.showModal(null, e.hasOwnProperty('response') ? e.response.data.errors.date[0] : e.toString());
                    })
            },
            /**
             * Привести список твитов в компактный вид
             *
             * @param list
             */
            compactTweetsList(list) {
                list.forEach((item, index) => {
                    indexById[item.id] = index;
                    item.published_at = this.formatDate(item.published_at);
                    item['title'] = item.body.replace('<br/>', ' ');
                });

                this.tweetsList = list;
            },
            /**
             * Конвертировать дату в таймзону клиента
             *
             * @param date
             * @returns {*}
             */
            formatDate(date) {
                return this.$moment((date + '+00:00')).tz(this.$moment.tz.guess()).format('DD.MM.YYYY HH:mm');
            },
            /**
             * Отправить на последний твит
             */
            getTweetBodyLastShown() {
                if (undefined === this.$router.currentRoute.params.id) {
                    //Получить id последнейго просмотреного твита или первого твита за сегодня
                    let lastTweetId = storageShown[currDate][(storageShown[currDate].length - 1)] || this.tweetsList[(Object.keys(this.tweetsList).length - 1)].id;
                    //Если есть хоть какая-то новость - перенаправить на нее
                    if (undefined !== lastTweetId) this.$router.push({name: 'twitter', params: {id: lastTweetId}});
                } else {
                    this.getTweetBody(this.$router.currentRoute.params.id);
                }
            },
            /**
             * Получить полный контент твита и пометить как прочитанное
             *
             * @param tweetId
             */
            getTweetBody(tweetId) {
                tweetId = parseInt(tweetId);
                this.tweetBody = this.tweetsList[indexById[tweetId]];
                if (!this.isShown(tweetId)) this.saveShown(tweetId);
            }
        },
        watch  : {
            vcDate() {
                showDate = this.$moment(this.vcDate).format('YYYYMMDD');
                this.showTitlesLoader = true;
                this.getTweetsTitles();
            },
            '$route'(to) {
                undefined !== to.params.id ? this.getTweetBody(to.params.id) : this.getTweetBodyLastShown();
            }
        }
    }
</script>

<style scoped>
    .bgDark {
        background : #1a202c !important;
    }

    html, body {
        height   : 100% !important;
        overflow : hidden;
    }

    #header > div > div {
        height : 70px;
    }

    .height {
        height : calc(100vh - 70px);
    }

    #content {
        padding : 0;
    }

    .ellipsis {
        overflow      : hidden;
        text-overflow : ellipsis;
    }

    #left_menu,
    #left_menu ul {
        padding : 0;
    }

    #left_menu li {
        cursor  : pointer;
        height  : 70px;
        margin  : 0;
        padding : 0;
        width   : 100%;
    }

    #left_menu li:hover {
        background : #eef2f6;
    }

    #left_menu li >>> em {
        background : #ff0;
        font-style : normal;
        margin     : 0 !important;
    }

    .router-link-active {
        background : #dee2e6 !important;
    }

    #left_menu li > a {
        display : block;
        height  : 70px;
    }

    #left_menu li > a > div {
        display : inline-block;
        height  : 70px;
    }

    #left_menu li > a > div:first-child {
        overflow : hidden;
        width    : 70px;
    }

    #left_menu li > a > div:last-child {
        line-height : 35px;
        width       : calc(100% - 75px);
    }
</style>
