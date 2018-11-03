<template>
    <div class="row movies">
        <movie :key="movie.id" v-for="movie in records.data" :movie="movie" ></movie>
    </div>
</template>

<script>
    import movie from './Movie'

    export default {

        name: 'movies',

        props: ['movies'],

        components: {
            movie
        },

        data () {
            return {
                records: JSON.parse(this.movies)
            }
        },

        created () {
        },

        methods: {

        }

    }

</script>