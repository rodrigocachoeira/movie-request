<template>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <li :class="{disabled: pagination.current_page == 1}" class="page-item">
                <a v-if="pagination.current_page == 1" class="page-link">Anterior</a>
                <a v-else class="page-link" :href="currentUrl(pagination.current_page - 1)">Anterior</a>
            </li>

            <li v-for="page in pages" class="page-item" :class="{active: pagination.current_page == page}">
                <a class="page-link" v-if="page == '...'" >...</a>
                <a v-else class="page-link" :href="currentUrl(page)">{{ page }}</a>
            </li>

            <li :class="{disabled: pagination.current_page == pagination.last_page}" class="page-item">
                <a v-if="pagination.current_page == pagination.last_page" class="page-link">Próximo</a>
                <a v-else class="page-link" :href="currentUrl(pagination.current_page+1)">Próximo</a>
            </li>
        </ul>
    </nav>
</template>

<style>
    .page-item a {
        z-index: 0 !important;
    }
</style>

<script>
    import Bus from './utils/bus'

    export default {

        name: 'pagination',

        props: ['source'],

        mounted() {
            this.pagination = JSON.parse(this.source);
        },

        data () {
            return  {
                pages: [],
                pagination: {}
            }
        },

        methods: {

            /**
             * Retorna a URL atual
             * e concatena com o parâmetro
             * que específica a página
             *
             * @param page
             * @return string
             */
            currentUrl (page) {
                if (window.location.href.indexOf('?') != -1) { //Verifica se já existe algum parâmetro definido na url
                    if (window.location.href.indexOf('page=') != -1) { //Verifica se o parâmetro página já foi especificado
                        return window.location.href.replace(/page=\d{1,}/, 'page=' + page);
                    } else {
                        return window.location.href.concat('&page='+page);
                    }
                } else {
                    return window.location.href.concat('?page='+page);
                }

            },

            /**
             * Alterna entre páginas
             *
             * @param event
             * @param page
             */
            navigate (event, page) {
                event.preventDefault();
                Bus.$emit('navigate', page);
            },

            /**
             * Altera para a página
             * seguinte
             *
             * @param event
             * @param page
             */
            next (event, page) {
                event.preventDefault()
                if (this.pagination.current_page != this.pagination.last_page)
                    Bus.$emit('navigate', page + 1)
            },

            /**
             * Retorna para a página
             * anterior
             *
             * @param event
             * @param page
             */
            prev (event, page) {
                event.preventDefault()
                if (page > 1)
                    Bus.$emit('navigate', page - 1)
            },

            /**
             * Gera os números que irão
             * aparecer na paginação
             *
             * @param currentPage
             * @param collectionLength
             * @param rowsPerPage
             * @param paginationRange
             * @returns {Array}
             */
            generatePagesArray: function(currentPage, collectionLength, rowsPerPage, paginationRange)
            {
                var pages = [];
                var totalPages = Math.ceil(collectionLength / rowsPerPage);
                var halfWay = Math.ceil(paginationRange / 2);
                var position;

                if (currentPage <= halfWay) {
                    position = 'start';
                } else if (totalPages - halfWay < currentPage) {
                    position = 'end';
                } else {
                    position = 'middle';
                }

                var ellipsesNeeded = paginationRange < totalPages;
                var i = 1;
                while (i <= totalPages && i <= paginationRange) {
                    var pageNumber = this.calculatePageNumber(i, currentPage, paginationRange, totalPages);
                    var openingEllipsesNeeded = (i === 2 && (position === 'middle' || position === 'end'));
                    var closingEllipsesNeeded = (i === paginationRange - 1 && (position === 'middle' || position === 'start'));
                    if (ellipsesNeeded && (openingEllipsesNeeded || closingEllipsesNeeded)) {
                        pages.push('...');
                    } else {
                        pages.push(pageNumber);
                    }
                    i ++;
                }
                return pages;
            },

            /**
             * Calcula a quantidade de números
             * que irão aparecer de acordo com a página atual
             *
             * @param i
             * @param currentPage
             * @param paginationRange
             * @param totalPages
             * @returns {*}
             */
            calculatePageNumber: function(i, currentPage, paginationRange, totalPages)
            {
                var halfWay = Math.ceil(paginationRange/2);
                if (i === paginationRange) {
                    return totalPages;
                } else if (i === 1) {
                    return i;
                } else if (paginationRange < totalPages) {
                    if (totalPages - halfWay < currentPage) {
                        return totalPages - paginationRange + i;
                    } else if (halfWay < currentPage) {
                        return currentPage - halfWay + i;
                    } else {
                        return i;
                    }
                } else {
                    return i;
                }
            }

        },

        watch: {

            pagination () {
                this.pages = this.generatePagesArray(
                    this.pagination.current_page,
                    this.pagination.total,
                    this.pagination.per_page,
                    10,
                );
            }

        }
    }
</script>
