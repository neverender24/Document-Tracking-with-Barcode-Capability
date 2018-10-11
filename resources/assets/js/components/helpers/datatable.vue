<template>
    <table class="table table-hover table-sm">
        <thead>
            <tr>
                <th v-for="column in columns"
                    :key="column.name"
                    @click="$emit('sort', column.name)" 
                    :class="sortKey === column.name ? (sortOrders[column.name] > 0 ? 'sorting_asc' : 'sorting_desc') : 'sorting'"
                    :style="'width:'+column.width+'; cursor:pointer;'"
                >
                    {{ column.label}}
                </th>
            </tr>
        </thead>
        <slot></slot>
    </table>
</template>

<style>


</style>

<script>
    export default {
        props: ['columns', 'sortKey', 'sortOrders']
    }
</script>