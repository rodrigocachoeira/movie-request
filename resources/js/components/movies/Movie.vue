<template>
    <div class="col-md-3">

        <div :class="{'disliked-behavior' : !record.liked && movie.liked != null}" class="card">
            <img class="card-img-top" :src="record.fullImagePath" alt="Card image cap">
            <div class="card-body">
                <h5 :title="record.title" class="card-title">{{ record.presentableTitle }}</h5>
                <p class="card-text">{{ record.presentableOverview }}</p>

                <hr>

                <ul class="list genre-list" >
                    <li v-for="genre in record.genres" >{{ genre.name }}</li>
                </ul>

                <div class="clearfix"><hr></div>

                <button :disabled="sending" @click="like(record)" title="Gostei" class="btn btn-sm btn-success">
                    <i v-if="!sending" class="fa fa-thumbs-up"></i>
                    <i v-else class="fa fa-spinner fa-spin"></i>
                </button>

                <button :class="{'disliked-behavior' : record.liked && movie.liked != null}" :disabled="sending" @click="dislike(record)" title="Não Gostei" class="btn btn-sm btn-danger">
                    <i v-if="!sending" class="fa fa-thumbs-down"></i>
                    <i v-else class="fa fa-spinner fa-spin"></i>
                </button>
            </div>
            <div class="card-footer text-right">
                <small v-if="record.fit == null" class="text-muted match">Não Analisado</small>
                <small v-else class="text-muted match">{{ record.fit | match }}% Match</small>
            </div>
        </div>
    </div>
</template>

<style>

    .genre-list {
        min-height: 130px;
    }

</style>

<script>

    export default {

        name: 'movie',

        props: ['movie'],

        data () {
            return {
                record: this.movie,
                sending: false
            }
        },

        created () {

        },

        filters: {

            match: function (value) {
                if (!value) return 0
                return parseFloat(Math.round(value * 100) / 100).toFixed(2);
            }

        },

        methods: {

            /**
             * Define que o usuário logado
             * no sistema gostou do filme
             *
             * @param movie
             */
            like(movie) {
                this.sending = true

                this.$http.post('/movies/'+movie.id+'/like').then(resp => {
                    this.$toastr.s("Marcado como gostei");
                    this.sending = false
                    movie.liked = true
                }, err => {
                    this.$toastr.e("Não foi possível realizar a operação, tente novamente");
                    this.sending = false
                })
            },

            /**
             * Define que o usuário logado
             * no sistema não gostou do filme
             *
             * @param movie
             */
            dislike(movie) {
                this.sending = true
                movie.liked = false

                this.$http.post('/movies/'+movie.id+'/dislike').then(resp => {
                    this.$toastr.s("Marcado como não gostei");
                    this.sending = false
                }, err => {
                    this.$toastr.e("Não foi possível realizar a operação, tente novamente");
                    this.sending = false
                })
            }

        }

    }

</script>

<style type="scss" >
    .fa-thumbs-down, .fa-thumbs-up, fa-ghost {
        color: white;
    }

    .disliked-behavior {
        opacity: 0.2;
    }

    .card {
        -webkit-transition: opacity 0.3s ease-in-out;
        -moz-transition: opacity 0.3s ease-in-out;
        -ms-transition: opacity 0.3s ease-in-out;
        -o-transition: opacity 0.3s ease-in-out;
        transition: opacity 0.3s ease-in-out;
    }

    div:hover {
        opacity: 1 !important;
    }

    .match {
        color: #9561e2 !important;
        font-size: 15px;
    }

</style>