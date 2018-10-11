<template>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <li  v-if="pagination.prevPageUrl" class="page-item">
                <a @click="$emit('prev');" class="page-link">Previous</a>
            </li>
             <li  v-else class="page-item disabled">
                <a class="page-link">Previous</a>
            </li>
            <li  v-if="pagination.nextPageUrl" class="page-item">
                <a @click="$emit('next');" class="page-link">Next</a>
            </li>
             <li  v-else class="page-item disabled">
                <a class="page-link">Next</a>
            </li>
        </ul>
    </nav>
</template>
<script>
    export default {
        props: ['pagination'],
        created() {

        }
    }
</script>