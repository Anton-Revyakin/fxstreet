<template>
    <div class="container-fluid h-100">
        <div class="row bgDark" id="header">
            <div class="col-3">
                <div class="row align-items-center">
                    <div class="col text-center">
                        <router-link to="/news" class="text-decoration-none small" style="color: #e4871b;">
                            <font-awesome-icon :icon="['far', 'newspaper']" style="font-size: 1.7rem;"/><br/>
                            FxStreet
                        </router-link>
                    </div>
                    <div class="col text-center">
                        <a href="#" class="text-white text-decoration-none small">
                            <font-awesome-icon :icon="['far', 'star']" class="text-white" style="font-size: 1.7rem;"/><br/>
                            Все
                        </a>
                    </div>
                    <div class="col text-center">
                        <a href="#" class="text-white text-decoration-none small">
                            <font-awesome-icon :icon="['fas', 'star']" class="text-white" style="font-size: 1.7rem;"/><br/>
                            Популярные
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="row h-100">
                    <div class="col-3 border-right" id="left_menu">
                        <overlay-scrollbars class="height">

                        </overlay-scrollbars>
                    </div>
                    <div class="col" id="content">
                        <overlay-scrollbars class="height col">
                            <div>
                                CONTENT...
                            </div>
                        </overlay-scrollbars>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Twitter",
        mounted() {
            $(function () {
                $('[data-toggle="tooltip"]').tooltip({
                    trigger : 'hover',
                    delay : {
                        'show' : 0,
                        'hide' : 1000
                    }
                });
            });
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
</style>
