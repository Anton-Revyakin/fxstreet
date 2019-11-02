<template>
    <div class="container-fluid h-100">
        <v-dialog name="dialog"/>
        <div class="row bgDark" id="header">
            <div class="col-3">
                <div class="row align-items-center">
                    <div class="col text-center">
                        <router-link to="/twitter" class="text-decoration-none small" style="color: #1da1f2;">
                            <font-awesome-icon :icon="['fab', 'twitter']" style="font-size: 1.7rem;"/><br/>
                            Twitter
                        </router-link>
                    </div>
                    <div class="col text-center">
                        <a href="#" @click.prevent="toggleVCalendar" class="text-white text-decoration-none small">
                            <font-awesome-icon :icon="['fas', 'calendar-day']" class="text-white" style="font-size: 1.7rem;"/><br/>
                            Календарь
                        </a>
                    </div>
                    <div class="col text-center">
                        <a href="#" @click.prevent="toggleSearch" class="text-white text-decoration-none small">
                            <font-awesome-icon :icon="['fas', 'search']" class="text-white" style="font-size: 1.7rem;"/><br/>
                            Поиск
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
                            <div class="bgDark" v-show="showSearch">
                                <div class="text-center">
                                    <img style="width: 75%;"
                                         src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNjgiIGhlaWdodD0iMjQiPjxnIGZpbGw9Im5vbmUiPjxwYXRoIGZpbGw9IiM1NDY4RkYiIGQ9Ik03OC45ODguOTM4aDE2LjU5NGEyLjk2OCAyLjk2OCAwIDAgMSAyLjk2NiAyLjk2NlYyMC41YTIuOTY3IDIuOTY3IDAgMCAxLTIuOTY2IDIuOTY0SDc4Ljk4OGEyLjk2NyAyLjk2NyAwIDAgMS0yLjk2Ni0yLjk2NFYzLjg5N0EyLjk2MSAyLjk2MSAwIDAgMSA3OC45ODguOTM4em00MS45MzcgMTcuODY2Yy00LjM4Ni4wMi00LjM4Ni0zLjU0LTQuMzg2LTQuMTA2bC0uMDA3LTEzLjMzNiAyLjY3NS0uNDI0djEzLjI1NGMwIC4zMjIgMCAyLjM1OCAxLjcxOCAyLjM2NHYyLjI0OHptLTEwLjg0Ni0yLjE4Yy44MjEgMCAxLjQzLS4wNDcgMS44NTUtLjEyOXYtMi43MTlhNi4zMzQgNi4zMzQgMCAwIDAtMS41NzQtLjE5OSA1LjcgNS43IDAgMCAwLS44OTcuMDY5IDIuNjk5IDIuNjk5IDAgMCAwLS44MTQuMjRjLS4yNC4xMTYtLjQzOS4yOC0uNTgyLjQ5MS0uMTUuMjEyLS4yMTkuMzM1LS4yMTkuNjU2IDAgLjYyOC4yMTkuOTkxLjYxNiAxLjIzcy45MzguMzYyIDEuNjE1LjM2MnptLS4yMzMtOS43Yy44ODMgMCAxLjYyOS4xMDkgMi4yMzEuMzI4LjYwMi4yMTggMS4wODguNTI1IDEuNDQ0LjkxNS4zNjMuMzk2LjYwOS45MjIuNzYgMS40ODMuMTU3LjU2LjIzMiAxLjE3NS4yMzIgMS44NXY2Ljg3NGEzMi41IDMyLjUgMCAwIDEtMS44NjguMzE0Yy0uODM0LjEyMy0xLjc3Mi4xODUtMi44MTMuMTg1LS42OSAwLTEuMzI3LS4wNjktMS44OTUtLjE5OGE0LjAwMSA0LjAwMSAwIDAgMS0xLjQ3MS0uNjM2IDMuMDg1IDMuMDg1IDAgMCAxLS45NTEtMS4xMzRjLS4yMjYtLjQ2NS0uMzQzLTEuMTItLjM0My0xLjgwMyAwLS42NTYuMTMtMS4wNzMuMzg0LTEuNTI1YTMuMjQgMy4yNCAwIDAgMSAxLjA0Ny0xLjEwNmMuNDQ1LS4yODcuOTUtLjQ5MiAxLjUzMi0uNjE1YTguOCA4LjggMCAwIDEgMS44Mi0uMTg1IDguNDA0IDguNDA0IDAgMCAxIDEuOTcyLjI0di0uNDM4YzAtLjMwNy0uMDM1LS42LS4xMS0uODc0YTEuODggMS44OCAwIDAgMC0uMzg0LS43MyAxLjc4NCAxLjc4NCAwIDAgMC0uNzI0LS40OTMgMy4xNjQgMy4xNjQgMCAwIDAtMS4xNDMtLjIwNWMtLjYxNiAwLTEuMTc3LjA3NS0xLjY5LjE2NGE3LjczNSA3LjczNSAwIDAgMC0xLjI2LjMwN2wtLjMyMS0yLjE5MmMuMzM1LS4xMTcuODM0LS4yMzMgMS40NzgtLjM0OWExMC45OCAxMC45OCAwIDAgMSAyLjA3My0uMTc4em01Mi44NDIgOS42MjZjLjgyMiAwIDEuNDMtLjA0OCAxLjg1NC0uMTNWMTMuN2E2LjM0NyA2LjM0NyAwIDAgMC0xLjU3NC0uMTk5Yy0uMjk0IDAtLjU5NS4wMjEtLjg5Ni4wNjlhMi43IDIuNyAwIDAgMC0uODE0LjI0IDEuNDYgMS40NiAwIDAgMC0uNTgyLjQ5MWMtLjE1LjIxMi0uMjE4LjMzNS0uMjE4LjY1NiAwIC42MjguMjE4Ljk5MS42MTUgMS4yMy40MDQuMjQ1LjkzOC4zNjIgMS42MTUuMzYyem0tLjIyNi05LjY5NGMuODgzIDAgMS42MjkuMTA4IDIuMjMxLjMyNy42MDIuMjE5IDEuMDg4LjUyNiAxLjQ0NC45MTUuMzU1LjM5LjYwOS45MjMuNzU5IDEuNDgzYTYuOCA2LjggMCAwIDEgLjIzMyAxLjg1MnY2Ljg3M2MtLjQxLjA4OC0xLjAzNC4xOS0xLjg2OC4zMTQtLjgzNC4xMjMtMS43NzIuMTg0LTIuODEzLjE4NC0uNjkgMC0xLjMyNy0uMDY4LTEuODk1LS4xOThhNC4wMDEgNC4wMDEgMCAwIDEtMS40NzEtLjYzNSAzLjA4NSAzLjA4NSAwIDAgMS0uOTUxLTEuMTM0Yy0uMjI2LS40NjUtLjM0My0xLjEyLS4zNDMtMS44MDQgMC0uNjU2LjEzLTEuMDczLjM4NC0xLjUyNC4yNi0uNDUuNjA4LS44MiAxLjA0Ny0xLjEwNy40NDUtLjI4Ni45NS0uNDkxIDEuNTMyLS42MTRhOC44MDMgOC44MDMgMCAwIDEgMi43NTEtLjEzYy4zMjkuMDM0LjY3MS4wOTYgMS4wNC4xODV2LS40MzdhMy4zIDMuMyAwIDAgMC0uMTA5LS44NzUgMS44NzMgMS44NzMgMCAwIDAtLjM4NC0uNzMxIDEuNzg0IDEuNzg0IDAgMCAwLS43MjQtLjQ5MiAzLjE2NSAzLjE2NSAwIDAgMC0xLjE0My0uMjA1Yy0uNjE2IDAtMS4xNzcuMDc1LTEuNjkuMTY0YTcuNzUgNy43NSAwIDAgMC0xLjI2LjMwN2wtLjMyMS0yLjE5M2MuMzM1LS4xMTYuODM0LS4yMzIgMS40NzgtLjM0OGExMS42MzMgMTEuNjMzIDAgMCAxIDIuMDczLS4xNzd6bS04LjAzNC0xLjI3MWExLjYyNiAxLjYyNiAwIDAgMS0xLjYyOC0xLjYyYzAtLjg5NS43MjUtMS42MiAxLjYyOC0xLjYyLjkwNCAwIDEuNjMuNzI1IDEuNjMgMS42MiAwIC44OTUtLjczMyAxLjYyLTEuNjMgMS42MnptMS4zNDggMTMuMjJoLTIuNjg5VjcuMjdsMi42OS0uNDIzdjExLjk1NnptLTQuNzE0IDBjLTQuMzg2LjAyLTQuMzg2LTMuNTQtNC4zODYtNC4xMDdsLS4wMDgtMTMuMzM2IDIuNjc2LS40MjR2MTMuMjU0YzAgLjMyMiAwIDIuMzU4IDEuNzE4IDIuMzY0djIuMjQ4em0tOC42OTgtNS45MDNjMC0xLjE1Ni0uMjUzLTIuMTE5LS43NDYtMi43ODgtLjQ5My0uNjc3LTEuMTgzLTEuMDEtMi4wNjctMS4wMS0uODgyIDAtMS41NzQuMzMzLTIuMDY1IDEuMDEtLjQ5My42NzYtLjczMyAxLjYzMi0uNzMzIDIuNzg4IDAgMS4xNjguMjQ2IDEuOTUzLjc0IDIuNjMuNDkyLjY4MyAxLjE4MyAxLjAxOCAyLjA2NiAxLjAxOC44ODIgMCAxLjU3NC0uMzQyIDIuMDY3LTEuMDE5LjQ5Mi0uNjgzLjczOC0xLjQ2LjczOC0yLjYzem0yLjczNy0uMDA3YzAgLjkwMi0uMTMgMS41ODQtLjM5NyAyLjMzYTUuNTIgNS41MiAwIDAgMS0xLjEyOCAxLjkwNiA0Ljk4NiA0Ljk4NiAwIDAgMS0xLjc1MiAxLjIyM2MtLjY4NS4yODYtMS43MzkuNDUtMi4yNjUuNDUtLjUyOC0uMDA2LTEuNTc0LS4xNTctMi4yNTItLjQ1YTUuMDk2IDUuMDk2IDAgMCAxLTEuNzQ0LTEuMjIzYy0uNDg3LS41MjctLjg2My0xLjE2Mi0xLjEzNy0xLjkwNmE2LjM0NSA2LjM0NSAwIDAgMS0uNDEtMi4zM2MwLS45MDIuMTIzLTEuNzcuMzk3LTIuNTA4YTUuNTU0IDUuNTU0IDAgMCAxIDEuMTUtMS44OTIgNS4xMzMgNS4xMzMgMCAwIDEgMS43NS0xLjIxNmMuNjc5LS4yODcgMS40MjUtLjQyMyAyLjIzMi0uNDIzLjgwOCAwIDEuNTUzLjE0MiAyLjIzNy40MjNhNC44OCA0Ljg4IDAgMCAxIDEuNzUzIDEuMjE2IDUuNjQ0IDUuNjQ0IDAgMCAxIDEuMTM1IDEuODkyYy4yODcuNzM4LjQzMSAxLjYwNi40MzEgMi41MDh6bS0yMC4xMzggMGMwIDEuMTIuMjQ2IDIuMzYzLjczOCAyLjg4Mi40OTMuNTIgMS4xMy43OCAxLjkxLjc4LjQyNCAwIC44MjgtLjA2MiAxLjIwNC0uMTc4LjM3Ny0uMTE2LjY3Ny0uMjUzLjkxNy0uNDE3VjkuMzNhMTAuNDc2IDEwLjQ3NiAwIDAgMC0xLjc2Ni0uMjI2Yy0uOTcxLS4wMjgtMS43MS4zNy0yLjIzIDEuMDA0LS41MTMuNjM2LS43NzMgMS43NS0uNzczIDIuNzg4em03LjQzOCA1LjI3NGMwIDEuODI0LS40NjYgMy4xNTYtMS40MDQgNC4wMDQtLjkzNi44NDYtMi4zNjcgMS4yNy00LjI5NiAxLjI3LS43MDUgMC0yLjE3LS4xMzctMy4zNC0uMzk2bC40MzEtMi4xMThjLjk4LjIwNSAyLjI3Mi4yNiAyLjk1LjI2IDEuMDc0IDAgMS44NC0uMjE5IDIuMjk5LS42NTYuNDU5LS40MzcuNjg0LTEuMDg2LjY4NC0xLjk0OHYtLjQzN2E4LjA3IDguMDcgMCAwIDEtMS4wNDcuMzk3Yy0uNDMuMTMtLjkzLjE5OC0xLjQ5Mi4xOTgtLjczOSAwLTEuNDEtLjExNi0yLjAxOC0uMzQ5YTQuMjA2IDQuMjA2IDAgMCAxLTEuNTY3LTEuMDI1Yy0uNDMxLS40NS0uNzc0LTEuMDE3LTEuMDEzLTEuNjk0LS4yNC0uNjc3LS4zNjMtMS44ODUtLjM2My0yLjc3MyAwLS44MzQuMTMtMS44OC4zODQtMi41NzcuMjYtLjY5Ni42MjktMS4yOTggMS4xMjktMS43OTYuNDkzLS40OTggMS4wOTUtLjg4MSAxLjgtMS4xNjJhNi42MDUgNi42MDUgMCAwIDEgMi40MjgtLjQ1N2MuODcgMCAxLjY3LjEwOSAyLjQ1LjI0Ljc4LjEyOSAxLjQ0NC4yNjUgMS45ODUuNDE1VjE4LjE3eiIvPjxwYXRoIGZpbGw9IiM1RDY0OTQiIGQ9Ik02Ljk3MiA2LjY3N3YxLjYyN2MtLjcxMi0uNDQ2LTEuNTItLjY3LTIuNDI1LS42Ny0uNTg1IDAtMS4wNDUuMTMtMS4zOC4zOTFhMS4yNCAxLjI0IDAgMCAwLS41MDIgMS4wM2MwIC40MjUuMTY0Ljc2NS40OTQgMS4wMi4zMy4yNTYuODM1LjUzMiAxLjUxNi44My40NDcuMTkyLjc5NS4zNTYgMS4wNDUuNDk1LjI1LjEzOC41MzcuMzMyLjg2Mi41ODIuMzI0LjI1LjU2My41NDguNzE4Ljg5NC4xNTQuMzQ1LjIzLjc0MS4yMyAxLjE4OCAwIC45NDctLjMzNCAxLjY5MS0xLjAwNCAyLjIzNC0uNjcuNTQyLTEuNTM3LjgxNC0yLjYwMS44MTQtMS4xOCAwLTIuMTYtLjIyOS0yLjkzNi0uNjg2di0xLjcwOGMuODQuNjI4IDEuODE0Ljk0MiAyLjkyLjk0Mi41ODUgMCAxLjA0OC0uMTM2IDEuMzg4LS40MDcuMzQtLjI3MS41MS0uNjQ2LjUxLTEuMTI1IDAtLjI4Ny0uMS0uNTUtLjMwMi0uNzktLjIwMy0uMjQtLjQyLS40Mi0uNjU1LS41NDItLjIzNC0uMTIzLS41ODUtLjI5LTEuMDUzLS41MDNhNjEuMjcgNjEuMjcgMCAwIDEtLjU4Mi0uMjcxIDEzLjY3IDEzLjY3IDAgMCAxLS41NS0uMjg3IDQuMjc1IDQuMjc1IDAgMCAxLS41NjctLjM1MSA2LjkyIDYuOTIgMCAwIDEtLjQ1NS0uNGMtLjE4LS4xNy0uMzEtLjM0LS4zOS0uNTEtLjA4LS4xNy0uMTU1LS4zNy0uMjI0LS41OThhMi41NTMgMi41NTMgMCAwIDEtLjEwNC0uNzQyYzAtLjkxNS4zMzMtMS42MzguOTk4LTIuMTcuNjY0LS41MzIgMS41MjMtLjc5OCAyLjU3Ni0uNzk4Ljk2OCAwIDEuNzkzLjE3IDIuNDczLjUxem03LjQ2OCA1LjY5NnYtLjI4N2MtLjAyMi0uNjA3LS4xODctMS4wODgtLjQ5NS0xLjQ0NC0uMzA5LS4zNTctLjc1LS41MzUtMS4zMjQtLjUzNS0uNTMyIDAtLjk5LjE5NC0xLjM3My41ODMtLjM4Mi4zODgtLjYyMi45NDktLjcxNyAxLjY4M2gzLjkwOXptMS4wMDUgMi43OTJ2MS40MDRjLS41OTYuMzQtMS4zODMuNTEtMi4zNjIuNTEtMS4yNTUgMC0yLjI1NS0uMzc3LTMtMS4xMzItLjc0NC0uNzU1LTEuMTE2LTEuNzQ0LTEuMTE2LTIuOTY4IDAtMS4yOTcuMzQtMi4zMTYgMS4wMjEtMy4wNTUuNjgtLjc0IDEuNTQ4LTEuMTEgMi42LTEuMTEgMS4wMzMgMCAxLjg1Mi4zMjMgMi40NTguOTY2LjYwNi42NDQuOTEgMS41NzIuOTEgMi43ODQgMCAuMzMtLjAzMy42NzYtLjA5NiAxLjAzOGgtNS4zMTRjLjEwNy43MDIuNDA1IDEuMjM5Ljg5NCAxLjYxMS40OS4zNzIgMS4xMDYuNTU4IDEuODUuNTU4Ljg2MiAwIDEuNTgtLjIwMiAyLjE1NS0uNjA2em02LjYwNS0xLjc3aC0xLjIxMmMtLjU5NiAwLTEuMDQ1LjExNi0xLjM0OS4zNS0uMzAzLjIzNC0uNDU0LjUzMi0uNDU0Ljg5NCAwIC4zNzIuMTE3LjY2NC4zNS44NzcuMjM1LjIxMy41NzUuMzIgMS4wMjIuMzIuNTEgMCAuOTEyLS4xNDIgMS4yMDQtLjQyNC4yOTMtLjI4MS40NC0uNjUxLjQ0LTEuMTA4di0uOTF6bS00LjA2OC0yLjU1NFY5LjMyNWMuNjI3LS4zNjEgMS40NTctLjU0MiAyLjQ4OS0uNTQyIDIuMTE2IDAgMy4xNzUgMS4wMjYgMy4xNzUgMy4wOFYxN2gtMS41NDh2LS45NTdjLS40MTUuNjgtMS4xNDMgMS4wMi0yLjE4NiAxLjAyLS43NjYgMC0xLjM4LS4yMi0xLjg0My0uNjYxLS40NjItLjQ0Mi0uNjk0LTEuMDAzLS42OTQtMS42ODQgMC0uNzc2LjI5My0xLjM4Ljg3OC0xLjgxLjU4NS0uNDMxIDEuNDA0LS42NDcgMi40NTctLjY0N2gxLjM0VjExLjhjMC0uNTU0LS4xMzMtLjk3MS0uMzk5LTEuMjUzLS4yNjYtLjI4Mi0uNzA3LS40MjMtMS4zMjQtLjQyM2E0LjA3IDQuMDcgMCAwIDAtMi4zNDUuNzE4em05LjMzMy0xLjkzdjEuNDJjLjM5NC0xIDEuMTAxLTEuNSAyLjEyMy0xLjUuMTQ4IDAgLjMxMy4wMTYuNDk0LjA0OHYxLjUzMWExLjg4NSAxLjg4NSAwIDAgMC0uNzUtLjE0M2MtLjU0MiAwLS45ODkuMjQtMS4zNC43MTgtLjM1MS40NzktLjUyNyAxLjA0OC0uNTI3IDEuNzA3VjE3aC0xLjU2M1Y4LjkxaDEuNTYzem01LjAxIDQuMDg0Yy4wMjIuODIuMjcyIDEuNDkyLjc1IDIuMDE5LjQ3OS41MjYgMS4xNS43OSAyLjAxLjc5LjYzOSAwIDEuMjM1LS4xNzYgMS43ODgtLjUyN3YxLjQwNGMtLjUyMS4zMTktMS4xODYuNDc5LTEuOTk1LjQ3OS0xLjI2NSAwLTIuMjc2LS40LTMuMDMxLTEuMTk3LS43NTUtLjc5OC0xLjEzMy0xLjc5Mi0xLjEzMy0yLjk4NCAwLTEuMTYuMzgtMi4xNTEgMS4xNC0yLjk3NS43NjEtLjgyNSAxLjc5LTEuMjM3IDMuMDg4LTEuMjM3LjcwMiAwIDEuMzQ2LjE0OSAxLjkzLjQ0N3YxLjQzNmEzLjI0MiAzLjI0MiAwIDAgMC0xLjc3LS40OTVjLS44NCAwLTEuNTEzLjI2Ni0yLjAxOS43OTgtLjUwNS41MzItLjc1OCAxLjIxMy0uNzU4IDIuMDQyek00MC4yNCA1LjcydjQuNTc5Yy40NTgtMSAxLjI5My0xLjUgMi41MDUtMS41Ljc4NyAwIDEuNDIuMjQ1IDEuODk5LjczNC40NzkuNDkuNzE4IDEuMTcuNzE4IDIuMDQyVjE3aC0xLjU2NHYtNS4xMDZjMC0uNTUzLS4xNC0uOTgtLjQyMi0xLjI4NC0uMjgyLS4zMDMtLjY1Mi0uNDU1LTEuMTEtLjQ1NS0uNTMxIDAtMS4wMDIuMjAyLTEuNDExLjYwNi0uNDEuNDA1LS42MTUgMS4wMjItLjYxNSAxLjg1MVYxN2gtMS41NjNWNS43MmgxLjU2M3ptMTQuOTY2IDEwLjAyYy41OTYgMCAxLjA5Ni0uMjUzIDEuNS0uNzU4LjQwNC0uNTA2LjYwNi0xLjE1Ny42MDYtMS45NTUgMC0uOTE1LS4yMDItMS42Mi0uNjA2LTIuMTE0LS40MDQtLjQ5NS0uOTItLjc0Mi0xLjU0OC0uNzQyLS41NTMgMC0xLjA1LjIyNC0xLjQ5MS42Ny0uNDQyLjQ0Ny0uNjYyIDEuMTMzLS42NjIgMi4wNTggMCAuOTU4LjIxMiAxLjY3LjYzOCAyLjEzOC40MjUuNDY5Ljk0Ni43MDMgMS41NjMuNzAzek01My4wMDQgNS43MnY0LjQyYy41NzQtLjg5NCAxLjM4OC0xLjM0MSAyLjQ0LTEuMzQxIDEuMDIyIDAgMS44NTcuMzgzIDIuNTA2IDEuMTQ5LjY0OS43NjYuOTczIDEuNzgxLjk3MyAzLjA0NyAwIDEuMTM4LS4zMDkgMi4xMDktLjkyNSAyLjkxMi0uNjE3LjgwMy0xLjQ2MyAxLjIwNS0yLjUzNyAxLjIwNS0xLjA3NSAwLTEuODk0LS40NDctMi40NTctMS4zNFYxN2gtMS41OFY1LjcyaDEuNTh6bTkuOTA4IDExLjEwNGwtMy4yMjMtNy45MTNoMS43MzlsMS4wMDUgMi42MzIgMS4yNiAzLjQxNWMuMDk2LS4zMi40OC0xLjQ1OCAxLjE1LTMuNDE1bC45MDktMi42MzJoMS42NmwtMi45MiA3Ljg2NmMtLjc3NyAyLjA3NC0xLjk2MyAzLjExLTMuNTU5IDMuMTFhMi45MiAyLjkyIDAgMCAxLS43MzQtLjA3OXYtMS4zNGMuMTcuMDQyLjM1MS4wNjQuNTQzLjA2NCAxLjAzMiAwIDEuNzU1LS41NyAyLjE3LTEuNzA4eiIvPjxwYXRoIGZpbGw9IiNGRkYiIGQ9Ik04OS42MzIgNS45Njd2LS43NzJhLjk3OC45NzggMCAwIDAtLjk3OC0uOTc3aC0yLjI4YS45NzguOTc4IDAgMCAwLS45NzguOTc3di43OTNjMCAuMDg4LjA4Mi4xNS4xNzEuMTNhNy4xMjcgNy4xMjcgMCAwIDEgMS45ODQtLjI4Yy42NSAwIDEuMjk1LjA4OCAxLjkxNy4yNTkuMDgyLjAyLjE2NC0uMDQuMTY0LS4xM20tNi4yNDggMS4wMWwtLjM5LS4zODlhLjk3Ny45NzcgMCAwIDAtMS4zODIgMGwtLjQ2NS40NjVhLjk3My45NzMgMCAwIDAgMCAxLjM4bC4zODMuMzgzYy4wNjIuMDYxLjE1LjA0Ny4yMDUtLjAxNC4yMjYtLjMwNy40NzItLjYwMS43NDYtLjg3NC4yODEtLjI4LjU2OC0uNTI2Ljg4My0uNzUxLjA2OC0uMDQyLjA3NS0uMTM3LjAyLS4ybTQuMTYgMi40NTN2My4zNDFjMCAuMDk2LjEwNC4xNjUuMTkyLjExN2wyLjk3LTEuNTM3Yy4wNjgtLjAzNC4wODktLjExNy4wNTUtLjE4NGEzLjY5NSAzLjY5NSAwIDAgMC0zLjA4LTEuODY2Yy0uMDY4IDAtLjEzNi4wNTQtLjEzNi4xM20wIDguMDQ4YTQuNDg5IDQuNDg5IDAgMCAxLTQuNDktNC40ODIgNC40ODggNC40ODggMCAwIDEgNC40OS00LjQ4MiA0LjQ4OCA0LjQ4OCAwIDAgMSA0LjQ4OSA0LjQ4MiA0LjQ4NCA0LjQ4NCAwIDAgMS00LjQ5IDQuNDgybTAtMTAuODVhNi4zNjMgNi4zNjMgMCAxIDAgMCAxMi43MjkgNi4zNyA2LjM3IDAgMCAwIDYuMzcyLTYuMzY4IDYuMzU4IDYuMzU4IDAgMCAwLTYuMzcxLTYuMzYiLz48L2c+PC9zdmc+"
                                         alt="">
                                </div>
                                <input type="text" ref="searchInput" class="form-control text-white"
                                       v-model="searchText"
                                       @input="throttleSearch()" placeholder="Введите текст для поиска...">
                            </div>
                            <ul class="list-group">
                                <li class="row list-group-item align-items-start border-bottom"
                                    v-for="(newsItem, index) in newsList"
                                    :key="newsItem.id"
                                    @click="getFullNews(index, newsItem.id)"
                                    :class="{lm_active_item:newsItem.id === newsListItemId}">
                                    <div>
                                        <img :src="newsItem.image" alt="">
                                    </div>
                                    <div class="list-group-item-heading text-dark text-nowrap ellipsis border-info" :style="[newsItem.unshown ? {'border-right' : '4px solid'} : '']">
                                        <span v-html="newsItem.title"></span>
                                        <br>
                                        <span class="list-group-item-text small">{{ newsItem.published_at }}</span>
                                    </div>
                                </li>
                            </ul>
                        </overlay-scrollbars>
                    </div>
                    <div class="col" id="content">
                        <overlay-scrollbars class="height col">
                            <div :class="{'text-black-50':loading}" v-if="Object.keys(newsBody).length > 0">
                                <h3 style="margin-top: 16px;">{{ newsBody.title }}</h3>
                                <div class="small text-black-50">Оригинал новости: <a :href="newsBody.full_url" target="_blank">{{ newsBody.full_url }}</a></div>
                                <div class="small text-black-50" v-if="Object.keys(newsBody.tags).length > 0">
                                    Валюты: <span>{{ newsBody.tags.join(', ') }}</span>
                                </div>
                                <hr>
                                <p v-html="newsBody.body"></p>
                            </div>
                        </overlay-scrollbars>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {AlgoliaSearch} from "../../mixins/AlgoliaSearch";

    let currDate = null;
    let showDate = null;
    let storageShown = {};

    export default {
        name: "News",
        mixins    : [AlgoliaSearch],
        data() {
            return {
                newsList      : [],
                newsListItemId: undefined,

                showVCalendar   : false,
                vcDate          : new Date(),
                vcAvailableDates: {
                    start: null,
                    end  : new Date()
                },

                showSearch: false,
                searchText: '',

                loading       : false,
                getByLastClick: 0,
                newsBody      : {}
            }
        },
        mounted() {
            this.getShown();
            this.getNews();
            showDate = this.$moment(this.vcDate).format('YYYYMMDD');
            setInterval(() => {
                if (this.searchText.length === 0) this.getNews();
            }, 10000);
        },
        methods   : {
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
                //Ключ хранилища по текущей дате
                currDate = this.$moment(new Date()).format('YYYYMMDD');
                //Получить список id прочитанных новостей из хранилища
                storageShown = localStorage.getItem('reads');
                storageShown = null === storageShown ? {} : JSON.parse(storageShown);

                //Создать объект с текущей датой, если еще нет или есть, но старый
                if (!storageShown.hasOwnProperty(currDate)) storageShown = {[currDate]: []};
            },
            /**
             * Сохранить данные в Хранилище
             */
            saveShown() {
                localStorage.setItem('reads', JSON.stringify(storageShown));
            },
            /**
             * Получить список новостей
             */
            getNews() {
                axios.get('/news/?date=' + this.$moment(this.vcDate).format('YYYY-MM-DD'))
                    .then((response) => {
                        this.compactNewsList(response.data);
                    })
                    .catch((e) => {
                        this.showModal(null, e.response.data.errors.date[0]);
                    })
            },
            /**
             * Конвертировать дату в таймзону клиента
             * Добавить метки о непрочитанных новостях
             *
             * @param list
             */
            compactNewsList(list) {
                list.forEach((item) => {
                    item.published_at = this.formatDate(item.published_at);
                    item['unshown'] = this.markUnshown(item.id);
                });

                this.newsList = list;
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
             * Пометить как непрочитанные новости
             *
             * @param newsId
             * @returns {boolean}
             */
            markUnshown(newsId) {
                return (this.searchText.length === 0 && currDate === showDate && storageShown[currDate].indexOf(newsId) === -1);
            },
            /**
             * Получить полный контент новости и пометить как прочитанное
             *
             * @param index
             * @param newsId
             */
            getFullNews(index, newsId) {
                if (this.loading) return false;

                this.loading = true;
                this.newsListItemId = newsId;

                axios.get('/news/' + newsId)
                    .then((response) => {
                        this.loading = false;
                        this.newsBody = response.data.data;

                        //Если дата календаря сегодняшняя и в Хранилище нет id прочитанной новости
                        if (showDate === currDate && storageShown[currDate].indexOf(newsId) === -1) {
                            this.newsList[index].unshown = false;
                            storageShown[currDate].push(newsId);
                            this.saveShown();
                        }
                    });
            },
            /**
             * Открыть/скрыть календарь
             */
            toggleVCalendar() {
                this.showVCalendar = !this.showVCalendar;
                if (this.showVCalendar) this.showSearch = false;
            },
            /**
             * Открыть/скрыть поиск
             */
            toggleSearch() {
                this.showSearch = !this.showSearch;
                if (this.showSearch) {
                    this.showVCalendar = false;
                    this.$nextTick(() => {
                        this.$refs.searchInput.focus();
                    });
                }
            },
            /**
             * Поиск
             */
            search() {
                //Если поле ввода поиска пустое - получить ноовсти с базы
                if (this.searchText.length === 0) {
                    this.getNews();
                    return;
                }

                this.$_search('news_index', ['title'], this.searchText, (response) => {
                    response.forEach((item) => {
                        //Replace title from highlight (with <em>)
                        item.title = item._highlightResult.title.value;
                        //Convert published date to time by local TZ
                        item.published_at = this.formatDate(item.published_at);
                    });
                    this.newsList = response;
                });
            }
        },
        computed  : {
            throttleSearch() {
                return _.throttle(this.search, 500);
            }
        },
        watch     : {
            vcDate() {
                showDate = this.$moment(this.vcDate).format('YYYYMMDD');
                this.getNews();
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

    #content .fxs_subtitle,
    #content .fxs_table {
        margin : 25px auto;
        width  : 30%;
    }

    #content .fxs_table,
    #content .fxs_table td {
        border : 1px solid #ccc;
    }

    .ellipsis {
        overflow      : hidden;
        text-overflow : ellipsis;
    }

    #inputSearch {
        background-image : url("data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgNTAgNTAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGcgZmlsbD0ibm9uZSI+PHBhdGggZD0iTTYuNTAyIDBoMzYuMzc5YzMuNTggMCA2LjUwMiAyLjkyIDYuNTAyIDYuNTI5djM2LjUzYzAgMy41OTYtMi45MDggNi41MjgtNi41MDIgNi41MjhINi41MDJDMi45MjIgNDkuNTg3IDAgNDYuNjY5IDAgNDMuMDZWNi41MTJDMCAyLjkyIDIuOTA1IDAgNi41MDIgMCIgZmlsbD0iIzU0NjhGRiIvPjxwYXRoIGQ9Ik0yOS44MzcgMTEuMDd2LTEuN2MwLTEuMTg5LS45Ni0yLjE1Mi0yLjE0NC0yLjE1MWgtNC45OThhMi4xNDggMi4xNDggMCAwIDAtMi4xNDQgMi4xNXYxLjc0NmMwIC4xOTUuMTguMzMuMzc1LjI4NWExNS41NjQgMTUuNTY0IDAgMCAxIDQuMzUtLjYxNSAxNS44IDE1LjggMCAwIDEgNC4yMDEuNTcuMjkuMjkgMCAwIDAgLjM2LS4yODVNMTYuMTQgMTMuMjk1bC0uODU0LS44NTdhMi4xMzggMi4xMzggMCAwIDAtMy4wMyAwbC0xLjAyMSAxLjAyMmEyLjE0NyAyLjE0NyAwIDAgMCAwIDMuMDRsLjg0Ljg0M2MuMTM1LjEzNC4zMy4xMDMuNDUtLjAzMWExNi40NiAxNi40NiAwIDAgMSAxLjYzNS0xLjkyNiAxNS41MjMgMTUuNTIzIDAgMCAxIDEuOTM1LTEuNjUzYy4xNS0uMDkuMTY0LS4zMDIuMDQ1LS40MzhtOS4xMiA1LjM5OXY3LjM1NWMwIC4yMS4yMjcuMzYyLjQyLjI1Nmw2LjUxMy0zLjM4NGMuMTQ4LS4wNzQuMTkzLS4yNTYuMTItLjQwNWE4LjA5NyA4LjA5NyAwIDAgMC02Ljc1Mi00LjEwN2MtLjE1IDAtLjMuMTItLjMuMjg1bTAgMTcuNzE5Yy01LjQzIDAtOS44NDItNC40MjQtOS44NDItOS44NjhzNC40MTItOS44NjYgOS44NDItOS44NjZjNS40MzIgMCA5Ljg0MiA0LjQyMiA5Ljg0MiA5Ljg2NnMtNC4zOTUgOS44NjgtOS44NDIgOS44NjhtMC0yMy44ODRjLTcuNzEyIDAtMTMuOTY3IDYuMjcyLTEzLjk2NyAxNC4wMTYgMCA3Ljc0NiA2LjI1NSAxNC4wMDQgMTMuOTY3IDE0LjAwNCA3LjcxMiAwIDEzLjk2Ny02LjI3MyAxMy45NjctMTQuMDE4IDAtNy43NDYtNi4yNC0xNC4wMDItMTMuOTY3LTE0LjAwMiIgZmlsbD0iI0ZGRiIvPjwvZz48L3N2Zz4=") !important;
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

    .lm_active_item {
        background : #dee2e6 !important;
    }

    #left_menu li > div:first-child {
        height       : 70px;
        margin-right : 5px;
        overflow     : hidden;
        width        : 70px;
    }

    #left_menu li > div:last-child {
        height      : 70px;
        line-height : 35px;
        width       : calc(100% - 75px);
    }
</style>
