<template>
    <div class="app-wrapper">
        <h1>Hello World!</h1>
        <h2>{{ title }}</h2>
    </div>
</template>

<script>
    export default {
        name: "Index",
        props: {
            title: null
        }
    }
</script>
